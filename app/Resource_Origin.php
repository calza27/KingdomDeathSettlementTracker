<?php namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Quary;
use App\Nemesis;


class Resource_Origin extends Model {
	protected $table = 'Resource_Origins';
	public $timestamps = false;
	protected $primaryKey = 'Resource_OriginID';
	
	public function resources() {
		return $this->hasMany('App\kdmResource', 'Resource_OriginID');
	}
	
	public function quary() {
		return Quary::leftJoin('Resource_Origins', 'Quaries.QuaryID', '=', 'Resource_Origins.QuaryID')
		->whereNotNull('Resource_Origins.QuaryID')
		->whereRaw('Quaries.QuaryID = ?', [$this->QuaryID])
		->select('Quaries.*')
		->get();
	}
	
	public function nemesis() {
		return Nemesis::leftJoin('Resource_Origins', 'Nemesis.NemesisID', '=', 'Resource_Origins.NemesisID')
		->whereNotNull('Resource_Origins.NemesisID')
		->whereRaw('Nemesis.NemesisID = ?', [$this->NemesisID])
		->('Nemesis.*')
		->get();
	}
}