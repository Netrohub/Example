<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class DiscordController extends Controller
{
	public function redirect(): RedirectResponse
	{
		return Socialite::driver('discord')
			->scopes(['identify','email','guilds'])
			->redirect();
	}

	public function callback(): RedirectResponse
	{
		$discordUser = Socialite::driver('discord')->user();
		$user = User::updateOrCreate(
			['discord_id' => $discordUser->getId()],
			[
				'username' => $discordUser->getNickname() ?: $discordUser->getName() ?: 'User',
				'discriminator' => method_exists($discordUser, 'getRaw') ? ($discordUser->getRaw()['discriminator'] ?? null) : null,
				'avatar' => $discordUser->getAvatar(),
				'email' => $discordUser->getEmail(),
			]
		);
		Auth::login($user, true);
		return redirect()->route('dashboard');
	}

	public function logout(): RedirectResponse
	{
		Auth::logout();
		return redirect('/');
	}
}