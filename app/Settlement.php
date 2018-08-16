<?php namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Gear;
use App\Quary;
use App\Nemesis;
use App\Innovation;
use App\Location;
use App\kdmResource;

class Settlement extends Model {
	protected $table = 'Settlements';
	public $timestamps = false;
	protected $primaryKey = 'SettlementID';
	protected $fillable = [
		'Settlement_Name',
		'Survival_Limit',
		'Survival_Upon_Departure',
		'Current_Lantern_Year',
		'First_Child_Born',
		'First_Death',
		'Five_Innovations',
		'Population_Fifteen',
		'Population',
		'Principle_New_Life',
		'Principle_Death',
		'Principle_Society',
		'Principle_Conviction',
		'Additional_Settlement_Notes',
		'Lantern_Research_Level',
		'deleted'
	];
	
	public $survivalUpInnovations = [
		"Language",
		"Hovel",
		"Symposium",
		"Pottery",
		"Storytelling",
		"Cooking", 
		"Ultimate Weapon",
		"Final Fighting Art",
		"Faith",
		"Bed"
	];
	
	public $survivalUponDepartureUpInnovations = [
		"Hovel",
		"Ammonia",
		"Lantern Oven",
		"Guidepost",
		"Family"
	];
	
	
	public function updatePopulation() {
		$newPop = $this->survivorsLiving->count();
		$this->update(['Population' => $newPop]);
		if($newPop >= 15) {
			$this->update(['Population_Fifteen' => 1]);
		}
	}
	
	public function setDefaults() {
		$this->Settlement_Name = "";
		$this->Survival_Limit = 1;
		$this->Survival_Upon_Departure = 1;
		$this->Current_Lantern_Year = 1;
		$this->First_Child_Born = 0;
		$this->First_Death = 0;
		$this->Five_Innovations = 0;
		$this->Population_Fifteen = 0;
		$this->Lantern_Research_Level = 0;
		$this->Population = 0;
		$language = Innovation::where('Name', 'Language')->first();
		$this->addInnovation($language);
		$lanternHoard = Location::where('Name', 'Lantern Hoard')->first();
		$this->addLocation($lanternHoard);
	}
	
	public function survivors() {
		return $this->hasMany('App\Survivor', 'SettlementID')->where('Deleted', null);
	}
	
	public function survivorsLiving() {
		return $this->survivors()->where('Dead', 0);
	}
	
	public function survivorsTopFour() {
		return $this->survivorsLiving()->orderBy('Hunt_XP','desc')->limit(4);
	}
	
	public function gears() {
		return Gear::leftJoin('Settlement_To_Gear', 'Gear.GearID', '=', 'Settlement_To_Gear.GearID')
			->leftJoin('Settlements', 'Settlement_To_Gear.SettlementID', '=', 'Settlements.SettlementID')
			->select('Gear.*', 'Settlement_To_Gear.Quantity')
			->whereRaw('Settlement_To_Gear.SettlementID = ?', [$this->SettlementID])
			->get();
	}
	
	public function addGear(Gear $g) {
		$objs = DB::table('Settlement_To_Gear')
			->where('Settlement_To_Gear.SettlementID', $this->SettlementID)
			->where('Settlement_To_Gear.GearID',$g->GearID)
			->get();
		if($objs != null) {
			DB::table('Settlement_To_Gear')
			->where('Settlement_To_Gear.SettlementID', $this->SettlementID)
			->where('Settlement_To_Gear.GearID',$g->GearID)
			->increment('Quantity');
		}
		else {
			DB::table('Settlement_To_Gear')->insert([
				'SettlementID' => $this->SettlementID,
				'GearID' => $g->GearID,
				'Quantity' => 1
			]);
		}
	}
	
	public function removeGear(Gear $g) {
		$objs = DB::table('Settlement_To_Gear')
			->where('Settlement_To_Gear.SettlementID', $this->SettlementID)
			->where('Settlement_To_Gear.GearID',$g->GearID)
			->get();
		if($objs != null) {
			if($objs[0]->Quantity > 1) {
				DB::table('Settlement_To_Gear')
					->where('Settlement_To_Gear.SettlementID', $this->SettlementID)
					->where('Settlement_To_Gear.GearID',$g->GearID)
					->decrement('Quantity');
			} else {
				DB::table('Settlement_To_Gear')
					->where('Settlement_To_Gear.SettlementID', $this->SettlementID)
					->where('Settlement_To_Gear.GearID',$g->GearID)
					->delete();
			}
		}
	}
	
	public function defeatedQuaries() {
		return Quary::leftJoin('Settlement_To_Defeated_Monsters', 'Quaries.QuaryID', '=', 'Settlement_To_Defeated_Monsters.QuaryID')
			->leftJoin('Settlements', 'Settlement_To_Defeated_Monsters.SettlementID', '=', 'Settlements.SettlementID')
			->whereNotNull('Settlement_To_Defeated_Monsters.QuaryID')
			->select('Quaries.*', 'Settlement_To_Defeated_Monsters.Level', 'Settlement_To_Defeated_Monsters.Quantity')
			->whereRaw('Settlement_To_Defeated_Monsters.SettlementID = ?', [$this->SettlementID])
			->get();
	}
	
	public function addDefeatedQuaries(Quary $q, $level) {
		$objs = DB::table('Settlement_To_Defeated_Monsters')
			->where('Settlement_To_Defeated_Monsters.SettlementID', $this->SettlementID)
			->where('Settlement_To_Defeated_Monsters.QuaryID', $q->QuaryID)
			->where('Settlement_To_Defeated_Monsters.Level', $level)
			->get();
		if($objs != null) {
			DB::table('Settlement_To_Defeated_Monsters')
			->where('Settlement_To_Defeated_Monsters.SettlementID', $this->SettlementID)
			->where('Settlement_To_Defeated_Monsters.QuaryID', $q->QuaryID)
			->where('Settlement_To_Defeated_Monsters.Level', $level)
			->increment('Quantity');
		}
		else {
			DB::table('Settlement_To_Defeated_Monsters')->insert([
				'SettlementID' => $this->SettlementID,
				'QuaryID' => $q->QuaryID,
				'NemesisID' => null,
				'Level' => $level,
				'Quantity' => 1
			]);
		}
	}
	
	public function defeatedNemesis() {
		return Nemesis::leftJoin('Settlement_To_Defeated_Monsters', 'Nemesis.NemesisID', '=', 'Settlement_To_Defeated_Monsters.NemesisID')
			->leftJoin('Settlements', 'Settlement_To_Defeated_Monsters.SettlementID', '=', 'Settlements.SettlementID')
			->whereNotNull('Settlement_To_Defeated_Monsters.NemesisID')
			->select('Nemesis.*', 'Settlement_To_Defeated_Monsters.Level', 'Settlement_To_Defeated_Monsters.Quantity')
			->whereRaw('Settlement_To_Defeated_Monsters.SettlementID = ?', [$this->SettlementID])
			->get();
	}
	
	public function addDefeatedNemesis(Nemesis $n, $level) {		
		$objs = DB::table('Settlement_To_Defeated_Monsters')
			->where('Settlement_To_Defeated_Monsters.SettlementID', $this->SettlementID)
			->where('Settlement_To_Defeated_Monsters.NemesisID', $n->NemesisID)
			->where('Settlement_To_Defeated_Monsters.Level', $level)
			->get();
		if($objs != null) {
			DB::table('Settlement_To_Defeated_Monsters')
			->where('Settlement_To_Defeated_Monsters.SettlementID', $this->SettlementID)
			->where('Settlement_To_Defeated_Monsters.NemesisID', $n->NemesisID)
			->where('Settlement_To_Defeated_Monsters.Level', $level)
			->increment('Quantity');
		}
		else {
			DB::table('Settlement_To_Defeated_Monsters')->insert([
				'SettlementID' => $this->SettlementID,
				'QuaryID' => null,
				'NemesisID' => $n->NemesisID,
				'Level' => $level,
				'Quantity' => 1
			]);
		}
	}
	
	public function innovations() {
		return Innovation::leftJoin('Settlement_To_Innovations', 'Innovations.InnovationID', '=', 'Settlement_To_Innovations.InnovationID')
			->leftJoin('Settlements', 'Settlement_To_Innovations.SettlementID', '=', 'Settlements.SettlementID')
			->select('Innovations.*')
			->whereRaw('Settlement_To_Innovations.SettlementID = ?', [$this->SettlementID])
			->get();
	}
	
	public function addInnovation(Innovation $i) {
		$objs = DB::table('Settlement_To_Innovations')
			->where('Settlement_To_Innovations.SettlementID', $this->SettlementID)
			->where('Settlement_To_Innovations.InnovationID',$i->InnovationID)
			->get();
		if($objs == null) {
			DB::table('Settlement_To_Innovations')->insert([
				'SettlementID' => $this->SettlementID,
				'InnovationID' => $i->InnovationID
			]);
			if(in_array($i->Name, $this->survivalUpInnovations)) {
				$this->Survival_Limit = ++$this->Survival_Limit;
			}
			if(in_array($i->Name, $this->survivalUponDepartureUpInnovations)) {
				$this->Survival_Upon_Departure = ++$this->Survival_Upon_Departure;
			}
			$this->save();
			if($i->Name == 'Paint') {
				foreach($this->survivors()->get() as $survivor) {
					$survivor->update([
						'Dash' => 1
					]);
				}
			} else if($i->Name == 'Language') {
				foreach($this->survivors()->get() as $survivor) {
					$survivor->update([
						'Encourage' => 1
					]);
				}
			} else if($i->Name == 'Inner Lantern') {
				foreach($this->survivors()->get() as $survivor) {
					$survivor->update([
						'Surge' => 1
					]);
				}
			} else if($i->Name == 'Destiny') {
				foreach($this->survivors()->get() as $survivor) {
					$survivor->update([
						'Endure' => 1
					]);
				}
			}
		}
	}
	
	public function removeInnovation(Innovation $i) {
		$objs = DB::table('Settlement_To_Innovations')
			->where('Settlement_To_Innovations.SettlementID', $this->SettlementID)
			->where('Settlement_To_Innovations.InnovationID',$i->InnovationID)
			->get();
		if($objs !== null) {
			DB::table('Settlement_To_Innovations')
			->where('Settlement_To_Innovations.SettlementID', $this->SettlementID)
			->where('Settlement_To_Innovations.InnovationID',$i->InnovationID)
			->delete();
			if(in_array($i->Name, $this->survivalUpInnovations)) {
				$this->Survival_Limit = --$this->Survival_Limit;
			}
			if(in_array($i->Name, $this->survivalUponDepartureUpInnovations)) {
				$this->Survival_Upon_Departure = --$this->Survival_Upon_Departure;
			}
			$this->save();
			if($i->Name == 'Paint') {
				foreach($this->survivors()->get() as $survivor) {
					$survivor->update([
						'Dash' => 0
					]);
				}
			} else if($i->Name == 'Language') {
				foreach($this->survivors()->get() as $survivor) {
					$survivor->update([
						'Encourage' => 0
					]);
				}
			} else if($i->Name == 'Inner Lantern') {
				foreach($this->survivors()->get() as $survivor) {
					$survivor->update([
						'Surge' => 0
					]);
				}
			} else if($i->Name == 'Faith') {
				foreach($this->survivors()->get() as $survivor) {
					$survivor->update([
						'Endure' => 0
					]);
				}
			}
		}
	}
	
	public function locations() {
		return Location::leftJoin('Settlement_To_Locations', 'Locations.LocationID', '=', 'Settlement_To_Locations.LocationID')
			->leftJoin('Settlements', 'Settlement_To_Locations.SettlementID', '=', 'Settlements.SettlementID')
			->select('Locations.*')
			->whereRaw('Settlement_To_Locations.SettlementID = ?', [$this->SettlementID])
			->get();
	}
	
	public function addLocation(Location $l) {
		$objs = DB::table('Settlement_To_Locations')
			->where('Settlement_To_Locations.SettlementID', $this->SettlementID)
			->where('Settlement_To_Locations.LocationID',$l->LocationID)
			->get();
		if($objs == null) {
			DB::table('Settlement_To_Locations')->insert([
				'SettlementID' => $this->SettlementID,
				'LocationID' => $l->LocationID
			]);
		}
	}
	
	public function quaryCanHunt() {
		return Quary::leftJoin('Settlement_To_Quary', 'Quaries.QuaryID', '=', 'Settlement_To_Quary.QuaryID')
			->leftJoin('Settlements', 'Settlement_To_Quary.SettlementID', '=', 'Settlements.SettlementID')
			->select('Quaries.*')
			->whereRaw('Settlement_To_Quary.SettlementID = ?', [$this->SettlementID])
			->get();
	}
	
	public function addQuary(Quary $q) {
		DB::table('Settlement_To_Quary')->insert([
			'SettlementID' => $this->SettlementID,
			'QuaryID' => $q->QuaryID
		]);
	}
	
	public function nemesisCanHunt() {
		return Nemesis::leftJoin('Settlement_To_Nemesis', 'Nemesis.NemesisID', '=', 'Settlement_To_Nemesis.NemesisID')
			->leftJoin('Settlements', 'Settlement_To_Nemesis.SettlementID', '=', 'Settlements.SettlementID')
			->select('Nemesis.*', 'Settlement_To_Nemesis.Level')
			->whereRaw('Settlement_To_Nemesis.SettlementID = ?', [$this->SettlementID])
			->get();
	}
	
	public function addNemesis(Nemesis $n) {
		$objs = DB::table('Settlement_To_Nemesis')
			->where('Settlement_To_Nemesis.SettlementID', $this->SettlementID)
			->where('Settlement_To_Nemesis.NemesisID', $n->NemesisID)
			->get();
		if($objs != null) {
			DB::table('Settlement_To_Nemesis')
			->where('Settlement_To_Nemesis.SettlementID', $this->SettlementID)
			->where('Settlement_To_Nemesis.NemesisID', $n->NemesisID)
			->increment('Level');
		}
		else {
			DB::table('Settlement_To_Nemesis')->insert([
				'SettlementID' => $this->SettlementID,
				'NemesisID' => $n->NemesisID,
				'Level' => 1
			]);
		}
	}
	
	public function kdmResources() {
		return kdmResource::leftJoin('Settlement_To_Resources', 'Resources.ResourceID', '=', 'Settlement_To_Resources.ResourceID')
			->leftJoin('Settlements', 'Settlement_To_Resources.SettlementID', '=', 'Settlements.SettlementID')
			->whereNotNull('Settlement_To_Resources.ResourceID')
			->select('Resources.*', 'Settlement_To_Resources.Quantity')
			->whereRaw('Settlement_To_Resources.SettlementID = ?', [$this->SettlementID])
			->get();
	}
	
	public function resourceTypes() {
		$types = array();
		foreach(Resource_Type::all() as $type) {
			$types[$type->Name] = 0;
		}
		foreach($this->kdmResources() as $resource) {
			foreach($resource->types() as $type) {
				$types[$type->Name] += $resource->Quantity;
			}
		}
		return $types;
	}
	
	public function addResource(kdmResource $r) {
		$objs = DB::table('Settlement_To_Resources')
			->where('Settlement_To_Resources.SettlementID', $this->SettlementID)
			->where('Settlement_To_Resources.ResourceID', $r->ResourceID)
			->get();
		if($objs != null) {
			DB::table('Settlement_To_Resources')
			->where('Settlement_To_Resources.SettlementID', $this->SettlementID)
			->where('Settlement_To_Resources.ResourceID',$r->ResourceID)
			->increment('Quantity');
		}
		else {
			DB::table('Settlement_To_Resources')->insert([
				'SettlementID' => $this->SettlementID,
				'ResourceID' => $r->ResourceID,
				'Quantity' => 1
			]);
		}
	}
	
	public function removeResource(kdmResource $r) {
		$objs = DB::table('Settlement_To_Resources')
			->where('Settlement_To_Resources.SettlementID', $this->SettlementID)
			->where('Settlement_To_Resources.ResourceID',$r->ResourceID)
			->get();
		if($objs != null) {
			if($objs[0]->Quantity > 1) {
				DB::table('Settlement_To_Resources')
					->where('Settlement_To_Resources.SettlementID', $this->SettlementID)
					->where('Settlement_To_Resources.ResourceID',$r->ResourceID)
					->decrement('Quantity');
			} else {
				DB::table('Settlement_To_Resources')
					->where('Settlement_To_Resources.SettlementID', $this->SettlementID)
					->where('Settlement_To_Resources.ResourceID',$r->ResourceID)
					->delete();
			}
		}
	}
	
	public function weaponSpecialisations() {
		$weapons = array();
		foreach($this->survivors()->get() as $survivor) {
			if($survivor->Weapon_Proficiency >= 8) {
				if(!in_array($survivor->weaponProficiency()->get(), $weapons)) array_push($weapons, $survivor->weaponProficiency());
			}
		}
		return $weapons;
	}
}
