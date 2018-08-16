<?php namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\kdmResource;
use App\Resource_Type;

class Gear extends Model {
	protected $table = 'Gear';
	public $timestamps = false;
	protected $primaryKey = 'GearID';
	
	public function origin() {
		return $this->belongsTo('App\Gear_Origin', 'Gear_OriginID');
	}
	
	public function kdmResources() {
		return kdmResource::leftJoin('Gear_To_Resources', 'Resources.ResourceID', '=', 'Gear_To_Resources.ResourceID')
			->leftJoin('Gear', 'Gear_To_Resources.GearID', '=', 'Gear.GearID')
			->whereNotNull('Gear_To_Resources.ResourceID')
			->whereRaw('Gear_To_Resources.GearID = ?', [$this->GearID])
			->select('Resources.*', 'Gear_To_Resources.Quantity')
			->get();
	}
	
	public function resourceTypes() {
		return Resource_Type::leftJoin('Gear_To_Resources', 'Resource_Type.Resource_TypeID', '=', 'Gear_To_Resources.Resource_TypeID')
			->leftJoin('Gear', 'Gear_To_Resources.GearID', '=', 'Gear.GearID')
			->whereNotNull('Gear_To_Resources.Resource_TypeID')
			->whereRaw('Gear_To_Resources.GearID = ?', [$this->GearID])
			->select('Resource_Type.*', 'Gear_To_Resources.Quantity')
			->get();
	}
	
	public function recipe() {
		return array_merge($this->kdmResources()->toArray(), $this->resourceTypes()->toArray());
	}
}