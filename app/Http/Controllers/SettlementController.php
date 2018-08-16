<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Settlement;
use App\Survivor;
use App\Innovation;
use App\Disorder;
use App\Location;
use App\Gear;
use App\kdmResource;
use App\Quary;
use App\Nemesis;

class SettlementController extends Controller
{
	public function create() {
		$settlement = new Settlement;
		return view('settlements.create', ['settlement' => $settlement]);
	}
	
	public function store(Request $request) {
		//dd($request);
		//Updates the settlements name
		$settlement = Settlement::where('SettlementID', '=', $request['SettlementID'])->first();
		$settlement->update(['Settlement_Name' => $request['Settlement_Name']]);
		
		//Creates each survivor, adds them to settlement
		foreach($request['Survivor'] as $form) {
			$survivor = new Survivor;
			$survivor->SettlementID = $request['SettlementID'];
			$survivor->Name = $form['Name'];
			if($survivor->Name != null) $survivor->Survival = 1;
			else $survivor->Survival = 0;
			$survivor->Born_Lantern_Year = 0;
			$survivor->Gender = $form['Gender'];
			$survivor->Hunt_XP = 1;
			$survivor->save();
			$survivor->setDefaults();
		}
		
		//Updates settlement populations
		$settlement->updatePopulation();
		return redirect()->route('settlement', $settlement->SettlementID);
	}
	
	public function viewAll() {
		$settlements = Settlement::whereNull('deleted')->get();
        return view('settlementList', ['settlements' => $settlements]);
	}
	
	public function view($id) {
		$settlement = Settlement::where('SettlementID', '=', $id)->first();
		$innovations = Innovation::all()->sortBy('Name')->lists('Name', 'InnovationID');
		$innovations = array_diff($innovations, $settlement->innovations()->lists('Name', 'InnovationID'));
		$locations = Location::all()->sortBy('Name')->lists('Name', 'LocationID');
		$locations = array_diff($locations, $settlement->locations()->lists('Name', 'LocationID'));
		$resources = kdmResource::orderBy('OriginID', 'ASC')->orderBy('Name', 'ASC')->lists('Name', 'ResourceID');
		$resources = array_diff($resources, $settlement->kdmResources()->lists('Name', 'ResourceID'));
		$otherGear = Gear::orderBy('Gear_OriginID', 'ASC')->orderBy('Name', 'ASC')->lists('Name', 'GearID');
		$otherGear = array_diff($otherGear, $settlement->gears()->lists('Name', 'GearID'));
		$quary = Quary::all()->sortBy('Name')->lists('Name', 'QuaryID');
		$quary = array_diff($quary, $settlement->quaryCanHunt()->lists('Name', 'QuaryID'));
		$nemesis = Nemesis::all()->sortBy('Name')->lists('Name', 'NemesisID');
		$nemesis = array_diff($nemesis, $settlement->nemesisCanHunt()->lists('Name', 'NemesisID'));
		$quaryAvailable = $settlement->quaryCanHunt()->sortBy('Name')->lists('Name', 'QuaryID');
		$nemesisAvailable = $settlement->nemesisCanHunt()->sortBy('Name')->lists('Name', 'NemesisID');
		return view('settlement', ['settlement' => $settlement, 'innovations' => $innovations, 'locations' => $locations, 'resources' => $resources, 'otherGear' => $otherGear, 'quary' => $quary, 'nemesis' => $nemesis, 'quaryAvailable' => $quaryAvailable, 'nemesisAvailable' => $nemesisAvailable]);
	}
	
	public function AJAXupdateLanternYear(Request $request) {
		$settlement = Settlement::where('SettlementID', '=', $request['SettlementID'])->first();
		if($request['type'] === 'inc') {
			$settlement->update([
				'Lantern_Year' => ++$settlement->Current_Lantern_Year
			]);
		} else {
			$settlement->update([
				'Lantern_Year' => --$settlement->Current_Lantern_Year
			]);
		}
		return $settlement->Current_Lantern_Year;
	}
	
	public function AJAXupdatePrinciple(Request $request) {
		$settlement = Settlement::where('SettlementID', '=', $request['SettlementID'])->first();
		if($request['type'] === 'newLife') {
			$settlement->update([
				'Principle_New_Life' => $request['value']
			]);
			if($request['value']) {
				$settlement->update([
					'Survival_Limit' => ++$settlement->Survival_Limit
				]);
				foreach($settlement->survivors()->get() as $survivor) {
					$survivor->update([
						'Strength' => ++$survivor->Strength,
						'Evasion' => ++$survivor->Evasion
					]);
				}
				return '<li>Survival of the Fittest</li>';
			}
			else return '<li>Protect the Young</li>';
		} else if($request['type'] === 'death') {
			$settlement->update([
				'Principle_Death' => $request['value']
			]);
			if($request['value']) return '<li>Graves</li>';
			else {
				$settlement->update([
					'Survival_Limit' => ++$settlement->Survival_Limit
				]);
				return '<li>Cannibalize</li>';
			}
		} else if($request['type'] === 'society') {
			$settlement->update([
				'Principle_Society' => $request['value']
			]);
			if($request['value']) return '<li>Accept Darkness</li>';
			else return '<li>Collective Toil</li>';
		} else if($request['type'] === 'conviction') {
			$settlement->update([
				'Survival_Limit' => ++$settlement->Survival_Limit,
				'Principle_Conviction' => $request['value']
			]);
			if($request['value']) return '<li>Romantic</li>';
			else {
				foreach($settlement->survivors()->get() as $survivor) {
					$survivor->update([
						'Strength' => ++$survivor->Strength
					]);
				}
				return '<li>Barbaric</li>';
			}
		}
	}
	
	public function AJAXupdateInnovation(Request $request) {
		$settlement = Settlement::where('SettlementID', '=', $request['SettlementID'])->first();
		$innovation = Innovation::where('InnovationID', '=', $request['value'])->first();
		if(!in_array($innovation, $settlement->innovations()->toArray())) {
			$settlement->addInnovation($innovation);
			return "<div class='innName'>".$innovation->Name."</div><div class='innDescription'>".$innovation->Description."</div>";
		} else return "";
	}
	
	public function AJAXremoveInnovation(Request $request) {
		$settlement = Settlement::where('SettlementID', '=', $request['SettlementID'])->first();
		$innovation = Innovation::where('InnovationID', '=', $request['value'])->first();
		$settlement->removeInnovation($innovation);
		return "true";
	}
	
	public function AJAXupdateLocation(Request $request) {
		$settlement = Settlement::where('SettlementID', '=', $request['SettlementID'])->first();
		$location = Location::where('LocationID', '=', $request['value'])->first();
		if(!in_array($location, $settlement->locations()->toArray())) {
			$settlement->addLocation($location);
			return "<div class='locName'>".$location->Name."</div>";
		} else return "";
	}
	
	public function AJAXaddGear(Request $request) {
		$settlement = Settlement::where('SettlementID', '=', $request['SettlementID'])->first();
		$gear = Gear::where('GearID', '=', $request['value'])->first();
		$settlement->addGear($gear);
	}
	
	public function AJAXremoveGear (Request $request) {
		$settlement = Settlement::where('SettlementID', '=', $request['SettlementID'])->first();
		$gear = Gear::where('GearID', '=', $request['value'])->first();
		$settlement->removeGear($gear);
	}
	
	public function AJAXaddResource(Request $request) {
		$settlement = Settlement::where('SettlementID', '=', $request['SettlementID'])->first();
		$resource = kdmResource::where('ResourceID', '=', $request['value'])->first();
		$settlement->addResource($resource);
	}
	
	public function AJAXremoveResource (Request $request) {
		$settlement = Settlement::where('SettlementID', '=', $request['SettlementID'])->first();
		$resource = kdmResource::where('ResourceID', '=', $request['value'])->first();
		$settlement->removeResource($resource);
	}
	
	public function AJAXaddQuary(Request $request) {
		$settlement = Settlement::where('SettlementID', '=', $request['SettlementID'])->first();
		$quary = Quary::where('QuaryID', '=', $request['value'])->first();
		$settlement->addQuary($quary);
	}
	
	public function AJAXaddNemesis(Request $request) {
		$settlement = Settlement::where('SettlementID', '=', $request['SettlementID'])->first();
		$nemesis = Nemesis::where('NemesisID', '=', $request['value'])->first();
		$settlement->addNemesis($nemesis);
	}
	
	public function AJAXaddDefeatedQuary(Request $request) {
		$settlement = Settlement::where('SettlementID', '=', $request['SettlementID'])->first();
		$quary = Quary::where('QuaryID', '=', $request['value'])
			->first();
		if($quary !== null) {
			$settlement->addDefeatedQuaries($quary, $request['level']);
		}
		$settlement->Update([
			'Current_Lantern_Year' => ++$settlement->Current_Lantern_Year
		]);
	}
	
	public function AJAXaddDefeatedNemesis(Request $request) {
		$settlement = Settlement::where('SettlementID', '=', $request['SettlementID'])->first();
		$nemesis = Nemesis::where('NemesisID', '=', $request['value'])
		->first();
		if($nemesis !== null) {
			$settlement->addDefeatedNemesis($nemesis, $request['level']);
		}
	}
	
	public function AJAXsaveNotes(Request $request) {
		$settlement = Settlement::where('SettlementID', '=', $request['SettlementID'])->first();
		$settlement->update([
			'Additional_Settlement_Notes' => $request['value']
		]);
	}
}

?>