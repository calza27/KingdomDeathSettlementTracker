<?php namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Resource_Type;

class kdmResource extends Model {
	protected $table = 'Resources';
	public $timestamps = false;
	protected $primaryKey = 'ResourceID';
	
	public function origin() {
		return $this->belongsTo('App\Resource_Origin', 'Resource_OriginID');
	}
	
	public function types() {
		return Resource_Type::leftJoin('Resource_To_Type', 'Resource_Type.Resource_TypeID', '=', 'Resource_To_Type.Resource_TypeID')
			->leftJoin('Resources', 'Resource_To_Type.ResourceID', '=', 'Resources.ResourceID')
			->whereRaw('Resource_To_Type.ResourceID = ?', [$this->ResourceID])
			->select('Resource_Type.*')
			->get();
	}
}