<?php namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Body_Location extends Model {
	protected $table = 'Body_Locations';
	public $timestamps = false;
	protected $primaryKey = 'LocationID';
	
	public function permanent_injuries() {
		return $this->hasMany('App\Permanent_Injury', 'LocationID');
	}
}