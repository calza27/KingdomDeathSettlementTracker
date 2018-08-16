<?php namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Nemesis extends Model {
	protected $table = 'Nemesis';
	public $timestamps = false;
	protected $primaryKey = 'NemesisID';
}