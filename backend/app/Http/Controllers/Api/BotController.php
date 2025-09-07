<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rally;
use App\Models\Guild;
use App\Models\Channel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class BotController extends Controller
{
	public function pendingRallies(Request $request): JsonResponse
	{
		$rallies = Rally::query()
			->where('status', 'pending')
			->orderBy('created_at', 'asc')
			->limit(100)
			->get();
		return response()->json($rallies);
	}

	public function ack(int $id): JsonResponse
	{
		Rally::where('id', $id)->update(['status' => 'scheduled']);
		return response()->json(['ok' => true]);
	}

	public function guilds(): JsonResponse
	{
		return response()->json(Guild::orderBy('name')->get());
	}

	public function channels(int $guildId): JsonResponse
	{
		return response()->json(Channel::where('guild_id', $guildId)->orderBy('name')->get());
	}
}