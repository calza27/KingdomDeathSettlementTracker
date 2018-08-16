<?php namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Innovation extends Model {
	protected $table = 'Innovations';
	public $timestamps = false;
	protected $primaryKey = 'InnovationID';
}