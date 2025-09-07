@extends('layouts.app')

@section('content')
	<h2 class="neon mb-3">Dashboard</h2>
	@if(session('status'))
		<div class="alert alert-success">{{ session('status') }}</div>
	@endif

	<div class="row g-4">
		<div class="col-lg-6">
			<div class="card p-3">
				<h4 class="neon">Create Rally</h4>
				<form method="POST" action="{{ route('rallies.create') }}" class="row g-2">
					@csrf
					<div class="col-12">
						<label class="form-label">Rally Name</label>
						<input class="form-control" name="name" required>
					</div>
					<div class="col-6">
						<label class="form-label">Guild ID</label>
						<input class="form-control" name="guild_id" type="number" required>
					</div>
					<div class="col-6">
						<label class="form-label">Channel ID</label>
						<input class="form-control" name="channel_id" type="number" required>
					</div>
					<div class="col-6">
						<label class="form-label">Prep (sec)</label>
						<input class="form-control" name="prep_seconds" type="number" min="0" required>
					</div>
					<div class="col-6">
						<label class="form-label">Attacker Travel (sec)</label>
						<input class="form-control" name="attacker_travel_seconds" type="number" min="0" required>
					</div>
					<div class="col-6">
						<label class="form-label">Your Travel (sec)</label>
						<input class="form-control" name="your_travel_seconds" type="number" min="0" required>
					</div>
					<div class="col-6">
						<label class="form-label">Safety Buffer (sec)</label>
						<input class="form-control" name="safety_buffer_seconds" type="number" min="0" required>
					</div>
					<div class="col-12">
						<label class="form-label">Mention (@role or @user)</label>
						<input class="form-control" name="mention_target" placeholder="@Raiders or @John">
					</div>
					<div class="col-12">
						<button class="btn btn-neon">Create</button>
					</div>
				</form>
			</div>
		</div>

		<div class="col-lg-6">
			<div class="card p-3">
				<h4 class="neon">Assign Reinforcement</h4>
				<form method="POST" action="{{ route('reinforcements.create') }}" class="row g-2">
					@csrf
					<div class="col-6">
						<label class="form-label">Rally ID</label>
						<input class="form-control" name="rally_id" type="number" required>
					</div>
					<div class="col-6">
						<label class="form-label">Assignee Discord ID</label>
						<input class="form-control" name="assignee_discord_id" type="number" required>
					</div>
					<div class="col-6">
						<label class="form-label">Role</label>
						<input class="form-control" name="role">
					</div>
					<div class="col-6">
						<label class="form-label">Notes</label>
						<input class="form-control" name="notes">
					</div>
					<div class="col-12">
						<button class="btn btn-neon">Assign</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="row g-4 mt-1">
		<div class="col-12">
			<div class="card p-3">
				<h4 class="neon">Active Rallies</h4>
				<div class="table-responsive">
					<table class="table table-dark table-striped">
						<thead><tr><th>ID</th><th>Name</th><th>Status</th><th>Send At (UTC)</th><th>Prealert (UTC)</th></tr></thead>
						<tbody>
						@foreach($rallies as $r)
						<tr>
							<td>{{ $r->id }}</td>
							<td>{{ $r->name }}</td>
							<td>{{ $r->status }}</td>
							<td>{{ optional($r->send_at)->toDateTimeString() }}</td>
							<td>{{ optional($r->prealert_at)->toDateTimeString() }}</td>
						</tr>
						@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="col-12">
			<div class="card p-3">
				<h4 class="neon">Reinforcements</h4>
				<div class="table-responsive">
					<table class="table table-dark table-striped">
						<thead><tr><th>ID</th><th>Rally</th><th>Assignee</th><th>Role</th><th>Notes</th></tr></thead>
						<tbody>
						@foreach($reinforcements as $x)
						<tr>
							<td>{{ $x->id }}</td>
							<td>{{ $x->rally_id }}</td>
							<td>{{ $x->assignee_discord_id }}</td>
							<td>{{ $x->role }}</td>
							<td>{{ $x->notes }}</td>
						</tr>
						@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
@endsection