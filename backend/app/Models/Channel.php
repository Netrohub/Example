<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
	protected $table = 'channels';
	public $incrementing = false;
	protected $keyType = 'int';
	protected $fillable = ['id', 'guild_id', 'name'];
}