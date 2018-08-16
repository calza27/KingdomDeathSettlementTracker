<?php namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Impairment extends Model {
	protected $table = 'Survivors_To_Impairments';
	public $timestamps = false;
	protected $primaryKey = 'ImpairmentID';
}