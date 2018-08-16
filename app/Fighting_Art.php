<?php namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Fighting_Art extends Model {
	protected $table = 'Fighting_Arts';
	public $timestamps = false;
	protected $primaryKey = 'Fighting_ArtID';
}