<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reinforcement extends Model
{
	protected $table = 'reinforcements';
	protected $fillable = [
		'rally_id','assignee_discord_id','role','notes'
	];
}