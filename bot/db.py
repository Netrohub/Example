from __future__ import annotations

import aiomysql
from typing import Any, Dict, List, Optional

from config import settings


class DatabasePool:
	_pool: Optional[aiomysql.Pool] = None

	@classmethod
	async def get_pool(cls) -> aiomysql.Pool:
		if cls._pool is None:
			cls._pool = await aiomysql.create_pool(
				host=settings.db_host,
				port=settings.db_port,
				user=settings.db_user,
				password=settings.db_password,
				db=settings.db_name,
				autocommit=True,
				charset="utf8mb4",
				cursorclass=aiomysql.DictCursor,
			)
		return cls._pool

	@classmethod
	async def close(cls) -> None:
		if cls._pool is not None:
			cls._pool.close()
			await cls._pool.wait_closed()
			cls._pool = None


async def fetch_one(query: str, args: Optional[List[Any]] = None) -> Optional[Dict[str, Any]]:
	pool = await DatabasePool.get_pool()
	async with pool.acquire() as conn:
		async with conn.cursor() as cur:
			await cur.execute(query, args or [])
			row = await cur.fetchone()
			return row


async def fetch_all(query: str, args: Optional[List[Any]] = None) -> List[Dict[str, Any]]:
	pool = await DatabasePool.get_pool()
	async with pool.acquire() as conn:
		async with conn.cursor() as cur:
			await cur.execute(query, args or [])
			rows = await cur.fetchall()
			return rows


async def execute(query: str, args: Optional[List[Any]] = None) -> int:
	pool = await DatabasePool.get_pool()
	async with pool.acquire() as conn:
		async with conn.cursor() as cur:
			affected = await cur.execute(query, args or [])
			return affected