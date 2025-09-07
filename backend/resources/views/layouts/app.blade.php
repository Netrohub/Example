<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>{{ config('app.name', 'RallyTimerPro') }}</title>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
		<style>
			body { background: #0b0f17; color: #e5f4ff; }
			.navbar { background: linear-gradient(90deg,#0b0f17,#0f1b2d); border-bottom: 1px solid #1f2a44; }
			.card { background: #0f1622; border: 1px solid #1f2a44; }
			.neon { color: #00e5ff; text-shadow: 0 0 6px rgba(0,229,255,0.7); }
			.btn-neon { background:#00e5ff; color:#06121e; border:none; box-shadow:0 0 12px rgba(0,229,255,0.5); }
			.form-control, .form-select { background:#0b1220; color:#cde8ff; border:1px solid #1f2a44; }
		</style>
	</head>
	<body>
		<nav class="navbar navbar-expand-lg navbar-dark px-3">
			<a class="navbar-brand neon" href="/">RallyTimerPro</a>
			<div class="ms-auto">
				@auth
				<form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-sm btn-neon">Logout</button></form>
				@else
				<a class="btn btn-sm btn-neon" href="{{ route('auth.discord') }}">Login with Discord</a>
				@endauth
			</div>
		</nav>
		<div class="container py-4">
			@yield('content')
		</div>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	</body>
</html>