<?php namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Disorder extends Model {
	protected $table = 'Disorders';
	public $timestamps = false;
	protected $primaryKey = 'DisorderID';
}