<?php namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Quary;
use App\Nemesis;
use App\Location;

class Gear_Origin extends Model {
	protected $table = 'Gear_Origins';
	public $timestamps = false;
	protected $primaryKey = 'Gear_OriginID';
	
	public function gear() {
		return $this->hasMany('App\Gear', 'Gear_OriginID');
	}
	
	public function location() {
		return Location::leftJoin('Gear_Origins', 'Locations.LocationID', '=', 'Gear_Origins.LocationID')
		->whereNotNull('Gear_Origins.LocationID')
		->whereRaw('Locations.LocationID = ?', [$this->LocationID])
		->select('Locations.*')
		->get();
	}
	
	public function quary() {
		return Quary::leftJoin('Gear_Origins', 'Quaries.QuaryID', '=', 'Gear_Origins.QuaryID')
		->whereNotNull('Gear_Origins.QuaryID')
		->whereRaw('Quaries.QuaryID = ?', [$this->QuaryID])
		->select('Quaries.*')
		->get();
	}
	
	public function nemesis() {
		return Nemesis::leftJoin('Gear_Origins', 'Nemesis.NemesisID', '=', 'Gear_Origins.NemesisID')
		->whereNotNull('Gear_Origins.NemesisID')
		->whereRaw('Nemesis.NemesisID = ?', [$this->NemesisID])
		->select('Nemesis.*')
		->get();
	}
}