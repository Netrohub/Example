<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenMiddleware
{
	public function handle(Request $request, Closure $next): Response
	{
		$token = config('app.api_token') ?: env('API_TOKEN');
		$auth = $request->bearerToken();
		if (!$token || $auth !== $token) {
			return response()->json(['message' => 'Unauthorized'], 401);
		}
		return $next($request);
	}
}