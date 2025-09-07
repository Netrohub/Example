from __future__ import annotations

import os
from typing import List, Optional

from dotenv import load_dotenv
from pydantic import BaseModel, Field


class Settings(BaseModel):
	# Discord
	bot_token: str = Field(alias="DISCORD_BOT_TOKEN")
	client_id: Optional[str] = Field(default=None, alias="DISCORD_CLIENT_ID")
	guild_allowlist: List[int] = Field(default_factory=list, alias="DISCORD_GUILD_ALLOWLIST")

	# Database
	db_host: str = Field(default="127.0.0.1", alias="DB_HOST")
	db_port: int = Field(default=3306, alias="DB_PORT")
	db_name: str = Field(default="rally_db", alias="DB_NAME")
	db_user: str = Field(default="rally_user", alias="DB_USER")
	db_password: str = Field(default="secret", alias="DB_PASSWORD")

	# API
	api_base_url: Optional[str] = Field(default=None, alias="API_BASE_URL")
	api_token: Optional[str] = Field(default=None, alias="API_TOKEN")

	# Timing
	prealert_seconds: int = Field(default=15, alias="PREALERT_SECONDS")
	poll_interval_seconds: int = Field(default=5, alias="POLL_INTERVAL_SECONDS")

	@staticmethod
	def _parse_allowlist(raw: Optional[str]) -> List[int]:
		if not raw:
			return []
		return [int(x.strip()) for x in raw.split(",") if x.strip().isdigit()]

	@classmethod
	def load(cls) -> "Settings":
		# Load .env if present
		load_dotenv()
		# Build data from environment
		data = dict(os.environ)
		# Parse allowlist into list of ints
		allowlist = cls._parse_allowlist(data.get("DISCORD_GUILD_ALLOWLIST"))
		inst = cls(**data)
		inst.guild_allowlist = allowlist
		return inst


settings = Settings.load()