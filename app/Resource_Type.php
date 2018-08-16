<?php namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Resource_Type extends Model {
	protected $table = 'Resource_Type';
	public $timestamps = false;
	protected $primaryKey = 'Resource_TypeID';
}