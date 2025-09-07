from __future__ import annotations

from datetime import datetime
from typing import Optional
from pydantic import BaseModel


class Rally(BaseModel):
	id: int
	guild_id: int
	channel_id: int
	creator_discord_id: int
	name: str
	prep_seconds: int
	attacker_travel_seconds: int
	your_travel_seconds: int
	safety_buffer_seconds: int
	mention_target: Optional[str] = None
	created_at: datetime
	status: str
	send_at: datetime
	prealert_at: Optional[datetime] = None


class Alert(BaseModel):
	id: int
	rally_id: int
	type: str
	scheduled_at: datetime
	sent_at: Optional[datetime] = None
	payload: Optional[str] = None