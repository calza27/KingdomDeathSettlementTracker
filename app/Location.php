<?php namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Gear;

class Location extends Model {
	protected $table = 'Locations';
	public $timestamps = false;
	protected $primaryKey = 'LocationID';
	
	public function gear() {
		return Gear::leftJoin('Gear_Origins', 'Gear.Gear_OriginID', '=', 'Gear_Origins.Gear_OriginID')
		->leftJoin('Locations', 'Gear_Origins.LocationID', '=', 'Locations.LocationID')
		->select('Gear.*')
		->whereRaw('Gear_Origins.LocationID = ?', [$this->LocationID])
		->get();
	}
}