<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Gear;
use App\Fighting_Art;
use App\Secret_Fighting_Art;
use App\Disorder;
use App\Nemesis;
use App\Quary;
use App\Glossary;

class ReferenceController extends Controller
{
	public function viewEquipment() {
        $g = Gear::orderBy('Gear_OriginID', 'ASC')->orderBy('Name', 'ASC')->get();
        return view('list', ['g' => $g]);
    }
	
	public function viewFaad() {
        $fa = Fighting_Art::all()->sortBy('Name');
		$sfa = Secret_Fighting_Art::all()->sortBy('Name');
		$d = Disorder::all()->sortBy('Name');
        return view('list', ['fa' => $fa, 'sfa' => $sfa, 'd' => $d]);
    }
	
	public function viewEnemies() {
        $n = Nemesis::all()->sortBy('Name');
		$q = Quary::all()->sortBy('Name');
        return view('list', ['n' => $n, 'q' => $q]);
    }
	
    public function viewGlossary() {
        $glossary = Glossary::all()->sortBy('Name');
        return view('list', ['glossary' => $glossary]);
    }
}

?>