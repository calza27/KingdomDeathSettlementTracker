<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Survivor;
use App\Settlement;
use Carbon\Carbon;

class AppController extends Controller
{
	public function home() {
		$survivors = Survivor::get();
		foreach($survivors as $s) {
			if($s->SettlementID == null) {
				$s->update(['deleted' => Carbon::now()->toDateTimeString()]);
			}
		}
		return view('home');
	}
	
	public function newGame() {
		$settlement = new Settlement;
		$settlement->save();
		$settlement->setDefaults();
		return view('settlements.create', ['settlement' => $settlement]);
	}
	
	public function viewGames() {
		$settlements = Settlement::whereNull('deleted')->get();
        return view('settlementList', ['settlements' => $settlements]);
	}
}
?>