<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

//Home
Route::get('/', 'AppController@home');

//Start new game/settlement
Route::get('/newgame', 'AppController@newGame');
//List of settlements
Route::get('/list', 'AppController@viewGames');
//Create new settlement
Route::post('/settlement',['uses' => 'SettlementController@store']);
//View one settlement
Route::get('/settlement/{id}', ['as' => 'settlement', 'uses' => 'SettlementController@view']);
//AJAX edit settlement
	Route::post('/settlement/lanternYear', 'SettlementController@AJAXupdateLanternYear');
	Route::post('/settlement/principle', 'SettlementController@AJAXupdatePrinciple');
	Route::post('/settlement/innovation', 'SettlementController@AJAXupdateInnovation');
	Route::post('/settlement/removeInnovation', 'SettlementController@AJAXremoveInnovation');
	Route::post('/settlement/location', 'SettlementController@AJAXupdateLocation');
	Route::post('/settlement/addGear', 'SettlementController@AJAXaddGear');
	Route::post('/settlement/removeGear', 'SettlementController@AJAXremoveGear');
	Route::post('/settlement/addResource', 'SettlementController@AJAXaddResource');
	Route::post('/settlement/removeResource', 'SettlementController@AJAXremoveResource');
	Route::post('/settlement/addQuary', 'SettlementController@AJAXaddQuary');
	Route::post('/settlement/addNemesis', 'SettlementController@AJAXaddNemesis');
	Route::post('/settlement/addDefeatedQuary', 'SettlementController@AJAXaddDefeatedQuary');
	Route::post('/settlement/addDefeatedNemesis', 'SettlementController@AJAXaddDefeatedNemesis');
	Route::post('/settlement/saveNotes', 'SettlementController@AJAXsaveNotes');
//New and edit survivor
Route::get('/newSurvivors/{settlement}', 'SurvivorController@newSurvivor');
Route::post('/survivors/added', 'SurvivorController@addSurvivors');
Route::get('/survivor/{survivor}',['as' => 'editSurvivor', 'uses' => 'SurvivorController@editSurvivor']);
Route::post('/survivorFinalise', 'SurvivorController@saveSurvivor');
//AJAX edit survivor
	Route::post('/survivor/stat', 'SurvivorController@AJAXeditStat');
	Route::post('/survivor/disorder', 'SurvivorController@AJAXdisorder');
	Route::post('/survivor/fightingArt', 'SurvivorController@AJAXfightingArt');
	Route::post('/survivor/secretFightingArt', 'SurvivorController@AJAXsecretFightingArt');
	Route::post('/survivor/delete', 'SurvivorController@AJAXdeleteSurvivor');
	Route::post('/survivor/ability', 'SurvivorController@AJAXability');
	Route::post('/survivor/impairment', 'SurvivorController@AJAXimpairment');
	Route::post('/survivor/retire', 'SurvivorController@AJAXretire');
	Route::post('/survivor/wpt', 'SurvivorController@AJAXwpt');
	Route::post('/survivor/wpl', 'SurvivorController@AJAXwpl');
	Route::post('/survivor/courageType', 'SurvivorController@AJAXcourageType');
	Route::post('/survivor/courage', 'SurvivorController@AJAXcourage');
	Route::post('/survivor/understandingType', 'SurvivorController@AJAXunderstandingType');
	Route::post('/survivor/understanding', 'SurvivorController@AJAXunderstanding');
	Route::post('/survivor/saveNotes', 'SurvivorController@AJAXsaveNotes');
/*References*/
Route::get('/equipment','ReferenceController@viewEquipment');
Route::get('/faad','ReferenceController@viewFaad');
Route::get('/enemies','ReferenceController@viewEnemies');
Route::get('/glossary','ReferenceController@viewGlossary');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);
