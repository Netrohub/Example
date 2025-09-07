from __future__ import annotations

import asyncio
from datetime import datetime, timedelta, timezone
from typing import Optional, List, Dict, Any

from discord_client import DiscordClient
from config import settings
from db import fetch_all, execute, DatabasePool
from models import Rally
from scheduler import Scheduler
from api import fetch_pending_rallies_via_api, ack_rally_via_api


SQL_SELECT_PENDING = (
	"""
	SELECT id, guild_id, channel_id, creator_discord_id, name,
	       prep_seconds, attacker_travel_seconds, your_travel_seconds,
	       safety_buffer_seconds, mention_target, created_at, status,
	       send_at, prealert_at
	FROM rallies
	WHERE status = 'pending'
	ORDER BY created_at ASC
	"""
)

SQL_MARK_SCHEDULED = "UPDATE rallies SET status='scheduled' WHERE id=%s"


def ensure_datetimes(row: Dict[str, Any]) -> Dict[str, Any]:
	# Convert naive to aware UTC
	for key in ("send_at", "prealert_at", "created_at"):
		value = row.get(key)
		if value and value.tzinfo is None:
			row[key] = value.replace(tzinfo=timezone.utc)
	return row


def compute_times_if_needed(row: Dict[str, Any]) -> Dict[str, Any]:
	send_at = row.get("send_at")
	if not send_at:
		now = datetime.now(timezone.utc)
		attacker_arrival = now + timedelta(seconds=row["prep_seconds"] + row["attacker_travel_seconds"])
		send_at = attacker_arrival - timedelta(seconds=row["your_travel_seconds"] + row["safety_buffer_seconds"])
		row["send_at"] = send_at
	prealert_at = row.get("prealert_at")
	if not prealert_at and send_at:
		prealert_at = send_at - timedelta(seconds=settings.prealert_seconds)
		row["prealert_at"] = prealert_at
	return row


async def compute_and_schedule(rally: Rally, client: DiscordClient, scheduler: Scheduler) -> None:
	mention = rally.mention_target or ""

	async def send_prealert() -> None:
		content = f"⏳ {mention} {settings.prealert_seconds} seconds be ready! To rein for rally #{rally.id} \"{rally.name}\"."
		await client.send_message(rally.channel_id, content)

	async def send_main() -> None:
		content = f"🚨 {mention} SEND REINFORCEMENTS NOW! (Rally #{rally.id} \"{rally.name}\")"
		await client.send_message(rally.channel_id, content)

	now = datetime.now(timezone.utc)
	if rally.prealert_at and rally.prealert_at > now:
		scheduler.schedule_at(rally.prealert_at, asyncio.create_task, send_prealert())
	if rally.send_at and rally.send_at > now:
		scheduler.schedule_at(rally.send_at, asyncio.create_task, send_main())


async def fetch_pending() -> List[Dict[str, Any]]:
	api_rows = await fetch_pending_rallies_via_api()
	if api_rows:
		return api_rows
	rows = await fetch_all(SQL_SELECT_PENDING)
	return rows


async def poll_and_schedule(client: DiscordClient, scheduler: Scheduler) -> None:
	while True:
		try:
			rows = await fetch_pending()
			for row in rows:
				row = compute_times_if_needed(row)
				row = ensure_datetimes(row)
				rally = Rally(**row)
				await compute_and_schedule(rally, client, scheduler)
				await execute(SQL_MARK_SCHEDULED, [rally.id])
				await ack_rally_via_api(rally.id)
		except Exception as e:
			print(f"Polling error: {e}")
		await asyncio.sleep(settings.poll_interval_seconds)


async def main_async() -> None:
	client = DiscordClient()
	scheduler = Scheduler()
	scheduler.start()

	@client.event
	async def on_ready():
		print(f"Logged in as {client.user}")
		asyncio.create_task(poll_and_schedule(client, scheduler))

	try:
		await client.start(settings.bot_token)
	finally:
		scheduler.shutdown()
		await DatabasePool.close()


if __name__ == "__main__":
	asyncio.run(main_async())