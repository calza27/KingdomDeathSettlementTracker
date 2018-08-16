<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Settlement;
use App\Survivor;
use App\Innovation;
use App\Disorder;
use App\Fighting_Art;
use App\Secret_Fighting_Art;
use App\Knowledge;
use App\Weapon_Proficiency;

class SurvivorController extends Controller
{	
	public function newSurvivor($id) {
		$settlement = Settlement::where('SettlementID', '=', $id)->first();
		$males = $settlement->survivorsLiving()->select(DB::raw("CONCAT(Name, ' ', COALESCE(Surname, '')) AS full_name, SurvivorID"))->where('Gender', 1)->orderBy('Hunt_XP','desc')->get()->lists('full_name', 'SurvivorID');
		$females = $settlement->survivorsLiving()->select(DB::raw("CONCAT(Name, ' ', COALESCE(Surname, '')) AS full_name, SurvivorID"))->where('Gender', 0)->orderBy('Hunt_XP','desc')->get()->lists('full_name', 'SurvivorID');
		return view('newSurvivor', ['settlement' => $settlement, 'males' => $males, 'females' => $females]);
	}
	
	public function addSurvivors(Request $request) {
		$settlement = Settlement::where('SettlementID', '=', $request['SettlementID'])->first();
		//Creates each survivor, adds them to settlement
		foreach($request['Survivor'] as $form) {
			$survivor = new Survivor;
			$survivor->SettlementID = $request['SettlementID'];
			$survivor->Name = $form['Name'];
			if($survivor->Name != null) $survivor->Survival = 1;
			else $survivor->Survival = 0;
			$survivor->Born_Lantern_Year = $settlement->Current_Lantern_Year;
			$survivor->Gender = $form['Gender'];
			if(array_key_exists('Saviour', $form)) $survivor->Saviour = 1;
			else $survivor->Saviour = 0;
			if($request["Surname"] != '') {
				$survivor->Surname = $request["Surname"];
			}
			$survivor->save();
			if($request["Father"] != '' && $request["Mother"] != '') {
				$father = Survivor::where('SurvivorID', '=', $request["Father"])->first();
				$mother = Survivor::where('SurvivorID', '=', $request["Mother"])->first();
				if($request["Surname"] != '') {
					if($father->Surname == '') {
						$father->Surname = $request["Surname"];
						$father->save();
					}
					if($mother->Surname == '') {
						$mother->Surname = $request["Surname"];
						$mother->save();
					}
				}
				$survivor->setParents($father, $mother);
			}
			$survivor->setDefaults();
		}
		
		//Updates settlement populations
		$settlement->updatePopulation();
		return redirect()->route('settlement', $settlement->SettlementID);
	}
	
	public function editSurvivor($id) {
		$survivor = Survivor::where('SurvivorID', '=', $id)->first();
		$fightingArts = Fighting_Art::all()->sortBy('Name')->lists('Name', 'Fighting_ArtID');
		$fightingArts = array_diff($fightingArts, $survivor->fightingArts()->lists('Name', 'Fighting_ArtID'));
		$secretFightingArts = Secret_Fighting_Art::all()->sortBy('Name')->lists('Name', 'Secret_Fighting_ArtID');
		$secretFightingArts = array_diff($secretFightingArts, $survivor->secretFightingArts()->lists('Name', 'Secret_Fighting_ArtID'));
		$disorders = Disorder::lists('Name', 'DisorderID');
		$disorders = array_diff($disorders, $survivor->disorders()->lists('Name', 'DisorderID'));
		$knowledgeCourage = Knowledge::limit(3)->get()->sortBy('KnowledgeID')->lists('Name', 'KnowledgeID');
		$knowledgeUnderstanding = Knowledge::orderBy('KnowledgeID', 'desc')->limit(3)->get()->lists('Name', 'KnowledgeID');
		$weapons = Weapon_Proficiency::orderBy('Weapon_ProficiencyID', 'desc')->get()->sortBy('Weapon_ProficiencyID')->lists('Name', 'Weapon_ProficiencyID');
		$weaponSpecs = $survivor->settlement()->first()->weaponSpecialisations();
		$familyInnovation = Innovation::where('Name', '=', 'Family')->first();
		$hasFamilyInnovation = $survivor->settlement()->first()->innovations()->contains($familyInnovation);
		return view('survivor', ['survivor' => $survivor, 'disorders' => $disorders, 'fightingArts' => $fightingArts, 'secretFightingArts' => $secretFightingArts, 'knowledgeCourage' => $knowledgeCourage, 'knowledgeUnderstanding' => $knowledgeUnderstanding, 'weapons' => $weapons, 'weaponSpecs' => $weaponSpecs, 'hasFamilyInnovation' => $hasFamilyInnovation]);
	}
	
	public function saveSurvivor(Request $request) {
		$survivor = Survivor::where('SurvivorID', '=', $request['Survivor']['ID'])->first();
		$settlement = $survivor->settlement;
		if(array_key_exists('Name', $request['Survivor']) && $request['Survivor']['Name'] !== "") {
			$survivor->update([
				'Name' => $request['Survivor']['Name'],
				'Survival' => ++$survivor->Survival
			]);
		}
		if(array_key_exists('Surname', $request['Survivor']) && $request['Survivor']['Surname'] !== "") {
			$survivor->update([
				'Surname' => $request['Survivor']['Surname']
			]);
		}
		if(array_key_exists('Dead', $request['Survivor'])) {
			$survivor->update([
				'Dead' => 1,
				'Cause_Of_Death' => $request['Survivor']['CauseOfDeath'],
				'Died_Lantern_Year' => $settlement->Current_Lantern_Year
			]);
		} else {
			$survivor->update([
				'Dead' => 0,
				'Cause_Of_Death' => ''
			]);
		}
		if(array_key_exists('Cannot_Use_Abilities', $request['Survivor'])) {
			$survivor->update([
				'Cannot_Use_Abilities' => 1
			]);
		}
		if(array_key_exists('Cannot_Activate_Weapons', $request['Survivor'])) {
			$survivor->update([
				'Cannot_Activate_Weapons' => 1
			]);
		}
		if(array_key_exists('Cannot_Activate_2H_Weapons', $request['Survivor'])) {
			$survivor->update([
				'Cannot_Activate_2H_Weapons' => 1
			]);
		}
		if(array_key_exists('Cannot_Activate_Plus2_Str_Gear', $request['Survivor'])) {
			$survivor->update([
				'Cannot_Activate_Plus2_Str_Gear' => 1
			]);
		}
		if(array_key_exists('Cannot_Consume', $request['Survivor'])) {
			$survivor->update([
				'Cannot_Consume' => 1
			]);
		}
		if(array_key_exists('No_Intimacy', $request['Survivor'])) {
			$survivor->update([
				'No_Intimacy' => 1
			]);
		}
		if(array_key_exists('Skip_Hunt', $request['Survivor'])) {
			$survivor->update([
				'Skip_Hunt' => 1
			]);
		}
		if(array_key_exists('Cannot_Gain_Survival', $request['Survivor'])) {
			$survivor->update([
				'Cannot_Gain_Survival' => 1
			]);
		}
		if(array_key_exists('Cannot_Use_Survival', $request['Survivor'])) {
			$survivor->update([
				'Cannot_Use_Survival' => 1
			]);
		}
		if(array_key_exists('Cannot_Use_Abilities', $request['Survivor'])) {
			$survivor->update([
				'Cannot_Use_Abilities' => 1
			]);
		}
		$settlement->update([
			'Population' => count($settlement->survivorsLiving)
		]);
		$survivor->save();
		$settlement->save();
		return redirect()->route('settlement', $settlement->SettlementID);
	}
	
	public function AJAXeditStat(Request $request) {
		$survivor = Survivor::where('SurvivorID', '=', $request['SurvivorID'])->first();
		if($request['stat'] === 'SUR') {
			if(($request['change'] == 1 && $survivor->Survival < $survivor->Settlement()->first()->Survival_Limit) || ($request['change'] == -1 && $survivor->Survival > 0)) {
				$survivor->Update([
					'Survival' => $survivor->Survival + $request['change']
				]);
			}
			return $survivor->Survival;
		}
		else if($request['stat'] === 'XP') {
			if($request['change'] == 1 || $survivor->Hunt_XP > 0) {
				$survivor->Update([
					'Hunt_XP' => $survivor->Hunt_XP + $request['change']
				]);
				if($survivor->Hunt_XP == 16) {
					$survivor->Update([
						'Retired' => 1
					]);
				}
			}
			return $survivor->Hunt_XP . " XP";
		}
		else if($request['stat'] === 'MVT') {
			if($request['change'] == 1 || $survivor->Movement > 0) {
				$survivor->Update([
					'Movement' => $survivor->Movement + $request['change']
				]);
			}
			return $survivor->Movement;
		}
		else if($request['stat'] === 'ACC'){
			$survivor->Update([
				'Accuracy' => $survivor->Accuracy + $request['change']
			]);
			return $survivor->Accuracy;
		}
		else if($request['stat'] === 'STR'){
			$survivor->Update([
				'Strength' => $survivor->Strength + $request['change']
			]);
			return $survivor->Strength;
		}
		else if($request['stat'] === 'EVA'){
			$survivor->Update([
				'Evasion' => $survivor->Evasion + $request['change']
			]);
			return $survivor->Evasion;
		}
		else if($request['stat'] === 'LCK'){
			$survivor->Update([
				'Luck' => $survivor->Luck + $request['change']
			]);
			return $survivor->Luck;
		}
		else if($request['stat'] === 'SPD'){
			$survivor->Update([
				'Speed' => $survivor->Speed + $request['change']
			]);
			return $survivor->Speed;
		}
		else if($request['stat'] === 'INS'){
			if(($request['change'] === '-1' && $survivor->Insanity > 0) || $request['change'] === '1' ) {
				$survivor->Update([
					'Insanity' => $survivor->Insanity + $request['change']
				]);
			}
			return $survivor->Insanity;
		}
	}
	
	public function AJAXdisorder(Request $request) {
		$survivor = Survivor::where('SurvivorID', '=', $request['SurvivorID'])->first();
		$disorder = Disorder::where('DisorderID', '=', $request['value'])->first();
		if($request["remove"] != null) {
			$survivor->removeDisorder($disorder);
		} else if(!in_array($disorder, $survivor->disorders()->toArray())) {
			$survivor->addDisorder($disorder);
		}
	}
	
	public function AJAXfightingArt(Request $request) {
		$survivor = Survivor::where('SurvivorID', '=', $request['SurvivorID'])->first();
		$fightingArt = Fighting_Art::where('Fighting_ArtID', '=', $request['value'])->first();
		if($request['remove'] !== null) {
			$survivor->removeFightingArt($fightingArt);
		} else if(!in_array($fightingArt, $survivor->fightingArts()->toArray())) {
			$survivor->addFightingArt($fightingArt);
		}
	}
	
	public function AJAXsecretFightingArt(Request $request) {
		$survivor = Survivor::where('SurvivorID', '=', $request['SurvivorID'])->first();
		$secretFightingArt = Secret_Fighting_Art::where('Secret_Fighting_ArtID', '=', $request['value'])->first();
		if($request["remove"] != null) {
			$survivor->removeSecretFightingArt($secretFightingArt);
		} else if(!in_array($secretFightingArt, $survivor->secretFightingArts()->toArray())) {
			$survivor->addSecretFightingArt($secretFightingArt);
		}
	}
	
	public function AJAXdeleteSurvivor(Request $request) {
		$survivor = Survivor::where('SurvivorID', '=', $request['SurvivorID'])->first();
		$survivor->delete();
		$survivor->Settlement()->first()->updatePopulation();
	}
	
	public function AJAXability(Request $request) {
		$survivor = Survivor::where('SurvivorID', '=', $request['SurvivorID'])->first();
		if($request['type'] == 'add') {
			$survivor->addAbility($request['name'], $request['description']);
		} else if($request['type'] == 'remove') {
			$survivor->removeAbility($request['value']);
		}
	}
	
	public function AJAXimpairment(Request $request) {
		$survivor = Survivor::where('SurvivorID', '=', $request['SurvivorID'])->first();
		if($request['type'] == 'add') {
			$survivor->addImpairment($request['name'], $request['description']);
		} else if($request['type'] == 'remove') {
			$survivor->removeImpairment($request['value']);
		}
	}
	
	public function AJAXretire(Request $request) {
		$survivor = Survivor::where('SurvivorID', '=', $request['SurvivorID'])->first();
		$survivor->Update([
			'Retired' => 1
		]);
	}
	
	public function AJAXwpt(Request $request) {
		$survivor = Survivor::where('SurvivorID', '=', $request['SurvivorID'])->first();
		$survivor->Update([
			'Weapon_ProficiencyID' => $request['value']
		]);
	}
	
	public function AJAXwpl(Request $request) {
		$survivor = Survivor::where('SurvivorID', '=', $request['SurvivorID'])->first();
		if($request['value'] == '1') {
			$survivor->Update([
				'Weapon_Proficiency' => ++$survivor->Weapon_Proficiency
			]);
		} else {
			$survivor->Update([
				'Weapon_Proficiency' => --$survivor->Weapon_Proficiency
			]);
		}
	}
	
	public function AJAXcourageType(Request $request) {
		$survivor = Survivor::where('SurvivorID', '=', $request['SurvivorID'])->first();
		$survivor->update([
			'Courage_Type' => $request['value']
		]);
	}
	
	public function AJAXcourage(Request $request) {
		$survivor = Survivor::where('SurvivorID', '=', $request['SurvivorID'])->first();
		if($request['value'] == '1') {
			$survivor->Update([
				'Courage' => ++$survivor->Courage
			]);
		} else {
			$survivor->Update([
				'Courage' => --$survivor->Courage
			]);
		}
	}
	
	public function AJAXunderstandingType(Request $request) {
		$survivor = Survivor::where('SurvivorID', '=', $request['SurvivorID'])->first();
		$survivor->Update([
			'Understanding_Type' => $request['value']
		]);
	}
	
	public function AJAXunderstanding(Request $request) {
		$survivor = Survivor::where('SurvivorID', '=', $request['SurvivorID'])->first();
		if($request['value'] == '1') {
			$survivor->Update([
				'Understanding' => ++$survivor->Understanding
			]);
		} else {
			$survivor->Update([
				'Understanding' => --$survivor->Understanding
			]);
		}
	}
	
	public function AJAXsaveNotes(Request $request) {
		$survivor = Survivor::where('SurvivorID', '=', $request['SurvivorID'])->first();
		$survivor->Update([
			'Other_Notes' => $request['value']
		]);
	}
}

?>