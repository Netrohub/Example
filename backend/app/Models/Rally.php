<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rally extends Model
{
	protected $table = 'rallies';
	protected $fillable = [
		'guild_id','channel_id','creator_discord_id','name','prep_seconds',
		'attacker_travel_seconds','your_travel_seconds','safety_buffer_seconds',
		'mention_target','status','send_at','prealert_at'
	];

	protected $casts = [
		'send_at' => 'datetime',
		'prealert_at' => 'datetime',
	];
}