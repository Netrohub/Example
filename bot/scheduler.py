from __future__ import annotations

from datetime import datetime, timezone
from typing import Callable, Any

from apscheduler.schedulers.asyncio import AsyncIOScheduler
from apscheduler.triggers.date import DateTrigger


class Scheduler:
	def __init__(self) -> None:
		self._scheduler = AsyncIOScheduler(timezone=timezone.utc)

	def start(self) -> None:
		if not self._scheduler.running:
			self._scheduler.start()

	def shutdown(self) -> None:
		if self._scheduler.running:
			self._scheduler.shutdown(wait=False)

	def schedule_at(self, when: datetime, func: Callable[..., Any], *args: Any, **kwargs: Any) -> None:
		trigger = DateTrigger(run_date=when)
		self._scheduler.add_job(func, trigger=trigger, args=args, kwargs=kwargs, misfire_grace_time=5)