<?php

namespace App\Http\Controllers;

use App\Models\Rally;
use App\Models\Reinforcement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Carbon\CarbonImmutable;

class DashboardController extends Controller
{
	public function index(Request $request): View
	{
		$rallies = Rally::orderByDesc('created_at')->limit(50)->get();
		$reinforcements = Reinforcement::orderByDesc('created_at')->limit(50)->get();
		return view('dashboard.index', compact('rallies','reinforcements'));
	}

	public function createRally(Request $request): RedirectResponse
	{
		$data = $request->validate([
			'name' => 'required|string|max:255',
			'guild_id' => 'required|integer',
			'channel_id' => 'required|integer',
			'prep_seconds' => 'required|integer|min:0',
			'attacker_travel_seconds' => 'required|integer|min:0',
			'your_travel_seconds' => 'required|integer|min:0',
			'safety_buffer_seconds' => 'required|integer|min:0',
			'mention_target' => 'nullable|string|max:255',
		]);

		$now = CarbonImmutable::now('UTC');
		$attackerArrival = $now->addSeconds($data['prep_seconds'] + $data['attacker_travel_seconds']);
		$sendAt = $attackerArrival->subSeconds($data['your_travel_seconds'] + $data['safety_buffer_seconds']);
		$prealertAt = $sendAt->subSeconds((int) config('rally.prealert_seconds', 15));

		Rally::create([
			'guild_id' => $data['guild_id'],
			'channel_id' => $data['channel_id'],
			'creator_discord_id' => (int) optional(Auth::user())->discord_id,
			'name' => $data['name'],
			'prep_seconds' => $data['prep_seconds'],
			'attacker_travel_seconds' => $data['attacker_travel_seconds'],
			'your_travel_seconds' => $data['your_travel_seconds'],
			'safety_buffer_seconds' => $data['safety_buffer_seconds'],
			'mention_target' => $data['mention_target'] ?? null,
			'status' => 'pending',
			'send_at' => $sendAt->toDateTimeString(),
			'prealert_at' => $prealertAt->toDateTimeString(),
		]);

		return redirect()->route('dashboard')->with('status', 'Rally created');
	}

	public function updateRally(Request $request, Rally $rally): RedirectResponse
	{
		$data = $request->validate([
			'name' => 'required|string|max:255',
			'prep_seconds' => 'required|integer|min:0',
			'attacker_travel_seconds' => 'required|integer|min:0',
			'your_travel_seconds' => 'required|integer|min:0',
			'safety_buffer_seconds' => 'required|integer|min:0',
			'mention_target' => 'nullable|string|max:255',
			'status' => 'required|in:pending,scheduled,completed,cancelled',
		]);

		$now = CarbonImmutable::now('UTC');
		$attackerArrival = $now->addSeconds($data['prep_seconds'] + $data['attacker_travel_seconds']);
		$sendAt = $attackerArrival->subSeconds($data['your_travel_seconds'] + $data['safety_buffer_seconds']);
		$prealertAt = $sendAt->subSeconds((int) config('rally.prealert_seconds', 15));

		$rally->update(array_merge($data, [
			'send_at' => $sendAt->toDateTimeString(),
			'prealert_at' => $prealertAt->toDateTimeString(),
		]));

		return redirect()->route('dashboard')->with('status', 'Rally updated');
	}

	public function createReinforcement(Request $request): RedirectResponse
	{
		$data = $request->validate([
			'rally_id' => 'required|integer|exists:rallies,id',
			'assignee_discord_id' => 'required|integer',
			'role' => 'nullable|string|max:100',
			'notes' => 'nullable|string|max:255',
		]);

		Reinforcement::create($data);
		return redirect()->route('dashboard')->with('status', 'Reinforcement added');
	}
}