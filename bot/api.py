from __future__ import annotations

from typing import Any, Dict, List, Optional

import httpx

from config import settings


async def fetch_pending_rallies_via_api() -> List[Dict[str, Any]]:
	if not settings.api_base_url:
		return []
	url = settings.api_base_url.rstrip("/") + "/api/rallies/pending"
	headers = {}
	if settings.api_token:
		headers["Authorization"] = f"Bearer {settings.api_token}"
	async with httpx.AsyncClient(timeout=10.0) as client:
		resp = await client.get(url, headers=headers)
		resp.raise_for_status()
		data = resp.json()
		# Expecting list of JSON rally-like dicts
		return data if isinstance(data, list) else []


async def ack_rally_via_api(rally_id: int) -> None:
	if not settings.api_base_url:
		return
	url = settings.api_base_url.rstrip("/") + f"/api/rallies/{rally_id}/ack"
	headers = {"Content-Type": "application/json"}
	if settings.api_token:
		headers["Authorization"] = f"Bearer {settings.api_token}"
	async with httpx.AsyncClient(timeout=10.0) as client:
		try:
			await client.post(url, headers=headers, json={})
		except Exception:
			pass