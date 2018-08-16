<?php namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Permanent_Injury extends Model {
	protected $table = 'Permanent_Injuries';
	public $timestamps = false;
	protected $primaryKey = 'Permanent_InjuryID';
	
	public function body_location() {
		return $this->belongsTo('App\Body_Location', 'LocationID');
	}
}