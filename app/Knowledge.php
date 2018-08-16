<?php namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Knowledge extends Model {
	protected $table = 'Knowledge';
	public $timestamps = false;
	protected $primaryKey = 'KnowledgeID';
}