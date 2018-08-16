<?php namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Glossary extends Model {
	protected $table = 'Glossary';
	public $timestamps = false;
	protected $primaryKey = 'GlossaryID';
}
