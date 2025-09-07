from __future__ import annotations

import asyncio
from typing import Optional

import discord


class DiscordClient(discord.Client):
	def __init__(self, *, intents: Optional[discord.Intents] = None) -> None:
		intents = intents or discord.Intents.default()
		intents.message_content = True
		super().__init__(intents=intents)

	async def send_message(self, channel_id: int, content: str) -> None:
		channel = self.get_channel(channel_id)
		if channel is None:
			# Try fetching if not cached
			try:
				channel = await self.fetch_channel(channel_id)
			except Exception:
				return
		if hasattr(channel, "send"):
			await channel.send(content)

	async def wait_until_ready_safe(self) -> None:
		try:
			await self.wait_until_ready()
		except asyncio.CancelledError:
			pass