@extends('layouts.app')

@section('content')
	<div class="text-center py-5">
		<h1 class="neon">RallyTimerPro</h1>
		<p class="mt-3">Manage reinforcements and rally timers with Discord integration.</p>
		@auth
			<a class="btn btn-neon" href="{{ route('dashboard') }}">Open Dashboard</a>
		@else
			<a class="btn btn-neon" href="{{ route('auth.discord') }}">Login with Discord</a>
		@endauth
	</div>
@endsection