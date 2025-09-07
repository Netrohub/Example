<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
	protected $middlewareAliases = [
		'auth' => \App\Http\Middleware\Authenticate::class,
		'api_token' => \App\Http\Middleware\ApiTokenMiddleware::class,
	];
}