<?php namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Weapon_Proficiency extends Model {
	protected $table = 'Weapon_Proficiency';
	public $timestamps = false;
	protected $primaryKey = 'Weapon_ProficiencyID';
}