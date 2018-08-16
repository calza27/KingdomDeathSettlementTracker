<?php namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Quary extends Model {
	protected $table = 'Quaries';
	public $timestamps = false;
	protected $primaryKey = 'QuaryID';
}