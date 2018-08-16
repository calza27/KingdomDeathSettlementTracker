<?php namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Secret_Fighting_Art extends Model {
	protected $table = 'Secret_Fighting_Arts';
	public $timestamps = false;
	protected $primaryKey = 'Secret_Fighting_ArtID';
}