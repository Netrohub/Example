<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
	protected $table = 'alerts';
	protected $fillable = [
		'rally_id','type','scheduled_at','sent_at','payload'
	];

	protected $casts = [
		'scheduled_at' => 'datetime',
		'sent_at' => 'datetime',
	];
}