<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guild extends Model
{
	protected $table = 'guilds';
	public $incrementing = false;
	protected $keyType = 'int';
	protected $fillable = ['id', 'name', 'icon'];
}