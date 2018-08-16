<?php namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Ability;
use App\Impairment;
use App\Permanent_Injury;
use App\Disorder;
use App\Fighting_Art;
use App\Secret_Fighting_Art;
use DateTime;


class Survivor extends Model {
	protected $table = 'Survivors';
	public $timestamps = false;
	protected $primaryKey = 'SurvivorID';
	protected $fillable = [
		'Name',
		'Surname',
		'SettlementID',
		'Gender',
		'Hunt_XP',
		'Movement',
		'Accuracy',
		'Strength',
		'Evasion',
		'Luck',
		'Speed',
		'Skip_Hunt',
		'Dodge',
		'Encourage',
		'Surge',
		'Dash',
		'Endure',
		'Cannot_Gain_Survival',
		'Cannot_Use_Survival',
		'Cannot_Use_Abilities',
		'Saviour',
		'Dead',
		'Retired',
		'Reroll_Available',
		'Cause_Of_Death',
		'Survival',
		'Born_Lantern_Year',
		'Died_Lantern_Year',
		'Insanity',
		'Head_Armour',
		'Arm_Armour',
		'Body_Armour',
		'Waist_Armour',
		'Leg_Armour',
		'Courage',
		'Courage_Type',
		'Understanding',
		'Understanding_Type',
		'Weapon_Proficiency',
		'Weapon_ProficiencyID',
		'Cannot_Activate_Weapons',
		'Cannot_Activate_2H_Weapons',
		'Cannot_Activate_Plus2_Str_Gear',
		'Cannot_Consume',
		'No_Intimacy',
		'History_Great_Deeds',
		'Other_Notes',
		'deleted'
	];
	
	public function setDefaults() {
		$this->Hunt_XP = 0;
		$this->Movement = 5;
		$this->Accuracy = 0;
		$this->Strength = 0;
		$this->Evasion = 0;
		$this->Luck = 0;
		$this->Speed = 0;
		$this->Skip_Hunt = 0;
		$this->Dodge = 1;
		$this->Cannot_Gain_Survival = 0;
		$this->Cannot_Use_Survival = 0;
		$this->Cannot_Use_Abilities = 0;
		$this->Dead = 0;
		$this->Retired = 0;
		$this->Reroll_Available = 0;
		$this->Insanity = 0;
		$this->Head_Armour = 0;
		$this->Arm_Armour = 0;
		$this->Body_Armour = 0;
		$this->Waist_Armour = 0;
		$this->Leg_Armour = 0;
		$this->Courage = 0;
		$this->Understanding = 0;
		$this->Weapon_Proficiency = 0;
		$this->Cannot_Activate_Weapons = 0;
		$this->Cannot_Activate_2H_Weapons = 0;
		$this->Cannot_Activate_Plus2_Str_Gear = 0;
		$this->Cannot_Consume = 0;
		$this->No_Intimacy = 0;
		$this->save();
		$this->processSurvivalActions();
		$this->processSettlementUpgrades();
		$this->save();
		return;
	}
	
	public function fullname() {
		$out = "";
		if($this->Name != null) {
			$out = $out . $this->Name . " ";
		}
		if($this->Surname != null) {
			$out = $out . $this->Surname;
		}
		return $out;
	}
		
	//If this survivors settlement contains the innovations requried for various survival actions, add the survival action
	private function processSurvivalActions() {
		$settlement = $this->settlement()->first();
		$paint = Innovation::where('Name', '=', 'Paint')->get()->first();
		$language = Innovation::where('Name', '=', 'Language')->first();
		$innerLantern = Innovation::where('Name', '=', 'Inner Lantern')->first();
		$destiny = Innovation::where('Name', '=', 'Destiny')->first();
		if($settlement->innovations()->contains($paint)) $this->Dash = 1;
		else $this->Dash = 0;
		if($settlement->innovations()->contains($language)) $this->Encourage = 1;
		else $this->Encourage = 0;
		if($settlement->innovations()->contains($innerLantern)) $this->Surge = 1;
		else $this->Surge = 0;
		if($settlement->innovations()->contains($destiny)) $this->Endure = 1;
		else $this->Endure = 0;
	}
	
	private function processSettlementUpgrades() {
		$settlement = $this->settlement()->first();
		if($settlement->Principle_New_Life === 1) {
			$this->Strength = ++$this->Strength;
			$this->Evasion = ++$this->Evasion;
		}
		if($settlement->Principle_Conviction === 0) $this->Strength = ++$this->Strength;
		if($settlement->Principle_Death === 1) $this->Understanding = ++$this->Understanding;
		$saga = Innovation::where('Name', '=', 'Saga')->first();
		if($settlement->innovations()->contains($saga)) {
			$this->Courage = $this->Courage+=2;
			$this->Understanding = $this->Understanding+=2;
			$this->Hunt_XP = $this->Hunt_XP+=2;
		}
		$clanOfDeath = Innovation::where('Name', '=', 'Clan of Death')->first();
		if($settlement->innovations()->contains($clanOfDeath)) {
			$this->Accuracy = ++$this->Accuracy;
			$this->Strength = ++$this->Strength;
			$this->Evasion = ++$this->Evasion;
		}
	}
	
	public function settlement() {
		return $this->belongsTo('App\Settlement', 'SettlementID');
	}
	
	public function Courage_Type() {
		return $this->belongsTo('App\Knowledge', 'Courage_Type');
	}
	
	public function Understanding_Type() {
		return $this->belongsTo('App\Knowledge', 'Understanding_Type');
	}
	
	public function abilities() {
		return Ability::leftJoin('Survivors', 'Survivors_To_Abilities.SurvivorID', '=', 'Survivors.SurvivorID')
			->select('Survivors_To_Abilities.*')
			->whereRaw('Survivors_To_Abilities.SurvivorID = ?', [$this->SurvivorID])
			->get();
	}
	
	public function addAbility($name, $description) {
		DB::table('Survivors_To_Abilities')->insert([
			'SurvivorID' => $this->SurvivorID,
			'Name' => $name,
			'Description' => $description
		]);
	}
	
	public function removeAbility($id) {
		$objs = DB::table('Survivors_To_Abilities')
			->where('Survivors_To_Abilities.SurvivorID', $this->SurvivorID)
			->where('Survivors_To_Abilities.AbilityID',$id)
			->get();
		if($objs != null) {
			DB::table('Survivors_To_Abilities')
			->where('Survivors_To_Abilities.SurvivorID', $this->SurvivorID)
			->where('Survivors_To_Abilities.AbilityID', $id)
			->delete();
		}
	}
	
	public function impairments() {
		return Impairment::leftJoin('Survivors', 'Survivors_To_Impairments.SurvivorID', '=', 'Survivors.SurvivorID')
			->select('Survivors_To_Impairments.*')
			->whereRaw('Survivors_To_Impairments.SurvivorID = ?', [$this->SurvivorID])
			->get();
	}
	
	public function addImpairment($name, $description) {
		DB::table('Survivors_To_Impairments')->insert([
			'SurvivorID' => $this->SurvivorID,
			'Name' => $name,
			'Description' => $description
		]);
	}
	
	public function removeImpairment($id) {
		$objs = DB::table('Survivors_To_Impairments')
			->where('Survivors_To_Impairments.SurvivorID', $this->SurvivorID)
			->where('Survivors_To_Impairments.ImpairmentID',$id)
			->get();
		if($objs != null) {
			DB::table('Survivors_To_Impairments')
			->where('Survivors_To_Impairments.SurvivorID', $this->SurvivorID)
			->where('Survivors_To_Impairments.ImpairmentID', $id)
			->delete();
		}
	}
	
	public function parents() {
		$father = Survivor::leftJoin('Survivors_To_Parents', 'Survivors.SurvivorID', '=', 'Survivors_To_Parents.FatherID')
		->select('Survivors.*')
		->whereRaw('Survivors_To_Parents.SurvivorID = ?', [$this->SurvivorID])
		->get();
		$mother = Survivor::leftJoin('Survivors_To_Parents', 'Survivors.SurvivorID', '=', 'Survivors_To_Parents.MotherID')
		->select('Survivors.*')
		->whereRaw('Survivors_To_Parents.SurvivorID = ?', [$this->SurvivorID])
		->get();
		
		$out = array($father, $mother);
		return $out;
	}
	
	public function setParents($father, $mother) {
		DB::table('Survivors_To_Parents')->insert([
			'SurvivorID' => $this->SurvivorID,
			'FatherID' => $father->SurvivorID,
			'MotherID' => $mother->SurvivorID
		]);
	}
	
	public function children() {
		$children = Surivior::leftJoin('Survivors_To_Parents', 'Survivors.SurvivorID', '=', 'Survivors_To_Parents.SurvivorID')
			->select('Survivor.*');
		if($this->Gender) { /*Male*/
			return $children->whereRaw('Survivors_To_Parents.FatherID = ?', [$this->SurvivorID])
			->get();
		} else {
			return $children->whereRaw('Survivors_To_Parents.MotherID = ?', [$this->SurvivorID])
			->get();
		}
	}
	
	public function permanentInjuries() {
		return Permanent_Injury::leftJoin('Survivors_To_Permanent_Injuries', 'Permanent_Injuries.Permanent_InjuryID', '=', 'Survivors_To_Permanent_Injuries.Permanent_InjuryID')
			->leftJoin('Survivors', 'Survivors_To_Permanent_Injuries.SurvivorID', '=', 'Survivors.SurvivorID')
			->select('Permanent_Injuries.*')
			->whereRaw('Survivors_To_Permanent_Injuries.SurvivorID = ?', [$this->SurvivorID])
			->get();
	}
	
	public function disorders() {
		return Disorder::leftJoin('Survivor_To_Disorders', 'Disorders.DisorderID', '=', 'Survivor_To_Disorders.DisorderID')
			->leftJoin('Survivors', 'Survivor_To_Disorders.SurvivorID', '=', 'Survivors.SurvivorID')
			->select('Disorders.*')
			->whereRaw('Survivor_To_Disorders.SurvivorID = ?', [$this->SurvivorID])
			->get();
	}
	
	public function addDisorder(Disorder $d) {
		DB::table('Survivor_To_Disorders')->insert([
			'SurvivorID' => $this->SurvivorID,
			'DisorderID' => $d->DisorderID
		]);
	}
	
	public function removeDisorder(Disorder $d) {
		$objs = DB::table('Survivor_To_Disorders')
			->where('Survivor_To_Disorders.SurvivorID', $this->SurvivorID)
			->where('Survivor_To_Disorders.DisorderID',$d->DisorderID)
			->get();
		if($objs != null) {
			DB::table('Survivor_To_Disorders')
				->where('Survivor_To_Disorders.SurvivorID', $this->SurvivorID)
				->where('Survivor_To_Disorders.DisorderID',$d->DisorderID)
				->delete();
		}
	}
	
	public function fightingArtSummary() {
		$out = '';
		foreach($this->fightingArts() as $fa) {
			$out = $out . '<span class="has-tooltip">' . $fa->Name . '<span class="tooltip">' . $fa->Description . '</span></span>, ';
		}
		foreach($this->secretFightingArts() as $sfa) {
			$out = $out . '<span class="has-tooltip">' . $sfa->Name . '<span class="tooltip">' . $sfa->Description . '</span></span>, ';
		}
		if($out == "") return "<i>None</i>";
		return substr($out, 0, -2);
	}
	
	public function disorderSummary() {
		$out = "";
		foreach($this->disorders() as $d) {
			$out = $out . '<span class="has-tooltip">' . $d->Name . '<span class="tooltip">' . $d->Description . '</span></span>, ';
		}
		if($out == "") return "<i>None</i>";
		return substr($out, 0, -2);
	}
	
	public function weaponProficiency() {
		return $this->belongsTo('App\Weapon_Proficiency', 'Weapon_ProficiencyID')->first();
	}
	
	public function fightingArts() {
		return Fighting_Art::leftJoin('Survivor_To_Fighting_Arts', 'Fighting_Arts.Fighting_ArtID', '=', 'Survivor_To_Fighting_Arts.Fighting_ArtID')
			->leftJoin('Survivors', 'Survivor_To_Fighting_Arts.SurvivorID', '=', 'Survivors.SurvivorID')
			->select('Fighting_Arts.*')
			->whereRaw('Survivor_To_Fighting_Arts.SurvivorID = ?', [$this->SurvivorID])
			->get();
	}
	
	public function addFightingArt(Fighting_Art $fa) {
		DB::table('Survivor_To_Fighting_Arts')->insert([
			'SurvivorID' => $this->SurvivorID,
			'Fighting_ArtID' => $fa->Fighting_ArtID
		]);
	}
	
	public function removeFightingArt(Fighting_Art $fa) {
		$objs = DB::table('Survivor_To_Fighting_Arts')
			->where('Survivor_To_Fighting_Arts.SurvivorID', $this->SurvivorID)
			->where('Survivor_To_Fighting_Arts.Fighting_ArtID',$fa->Fighting_ArtID)
			->get();
		if($objs != null) {
			DB::table('Survivor_To_Fighting_Arts')
				->where('Survivor_To_Fighting_Arts.SurvivorID', $this->SurvivorID)
				->where('Survivor_To_Fighting_Arts.Fighting_ArtID',$fa->Fighting_ArtID)
				->delete();
		}
	}
	
	public function secretFightingArts() {
		return Secret_Fighting_Art::leftJoin('Survivor_To_Secret_Fighting_Arts', 'Secret_Fighting_Arts.Secret_Fighting_ArtID', '=', 'Survivor_To_Secret_Fighting_Arts.Secret_Fighting_ArtID')
			->leftJoin('Survivors', 'Survivor_To_Secret_Fighting_Arts.SurvivorID', '=', 'Survivors.SurvivorID')
			->select('Secret_Fighting_Arts.*')
			->whereRaw('Survivor_To_Secret_Fighting_Arts.SurvivorID = ?', [$this->SurvivorID])
			->get();
	}
	
	public function addSecretFightingArt(Secret_Fighting_Art $sfa) {
		DB::table('Survivor_To_Secret_Fighting_Arts')->insert([
			'SurvivorID' => $this->SurvivorID,
			'Secret_Fighting_ArtID' => $sfa->Secret_Fighting_ArtID
		]);
	}
	
	public function removeSecretFightingArt(Secret_Fighting_Art $sfa) {
		$objs = DB::table('Survivor_To_Secret_Fighting_Arts')
			->where('Survivor_To_Secret_Fighting_Arts.SurvivorID', $this->SurvivorID)
			->where('Survivor_To_Secret_Fighting_Arts.Secret_Fighting_ArtID',$sfa->Secret_Fighting_ArtID)
			->get();
		if($objs != null) {
			DB::table('Survivor_To_Secret_Fighting_Arts')
				->where('Survivor_To_Secret_Fighting_Arts.SurvivorID', $this->SurvivorID)
				->where('Survivor_To_Secret_Fighting_Arts.Secret_Fighting_ArtID',$sfa->Secret_Fighting_ArtID)
				->delete();
		}
	}
	
	public function faLimit() {
		return (count($this->fightingArts()) + count($this->secretFightingArts()) < 3);
	}
	
	public function disorderLimit() {
		return (count($this->disorders())< 3);
	}
	
	public function delete() {
		$now = new DateTime();
		$this->update([
			'deleted' => $now->getTimestamp()
		]);
	}
	
	
}
