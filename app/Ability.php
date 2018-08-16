<?php namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Ability extends Model {
	protected $table = 'Survivors_To_Abilities';
	public $timestamps = false;
	protected $primaryKey = 'AbilityID';
}