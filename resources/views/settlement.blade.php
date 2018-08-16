@extends('master')
@section('title', 'Settlement') 
@section('content')
<style>
	.settlementHeader { overflow: hidden; width: 100%; margin-top: 10px; }
	.survival { float: left; width: 25%; position: relative; }
	.survivalLimitNum { float: left; font-size: 40px; border: 1px solid black; width: 47px; display: inline-block; text-align: center; position: relative; left: 0px: top: 0px; }
	.survivalLimitText { position: relative; top: 0px; left: -20px; }
	.survivalDepartureNum { font-size: 20px; border: 1px solid black; width: 24px; display: inline-block; text-align: center; vertical-align: bottom; float: left; position: relative; top: 23px; left: 0px; }
	.survivalDepartureText { position: relative; top: 30px; left: -90px; }
	.header { width: 50%; float: left; text-align: center; }
	.settlementName { font-weight: bold; font-size: 40px; }
	.minorInfo { float: right; width: 25%; text-align: right; }
	.minorInfo > span { display: block; }
	.sectionTitle { font-weight: bold; text-decoration: underline; font-size: 20px; margin: 10px 0; }
	.sectionTitle > span { text-decoration: none; float: right; }
	.principles { overflow-y: auto; width: 90%; margin: 0 auto; }
	.principles > div { width: 25%; float: left; text-align: justify; }
	.items, .enemies, .defeatedEnemies { overflow-y: auto; }
	.gear, .resources, .gearST, .resourcesST, .quaries, .quariesSt, .nemesi, .nemesisST, .defeatedQuariesST, .defeatedNemesisST, .defeatedNemesi, .defeatedQuaries { width: calc(50% - 5px); }
	.gear, .gearST, .quaries, .quariesSt, .defeatedQuaries, .nemesisST, .defeatedQuariesST { float: left; }
	.resources, .resourcesST, .resourcesST .newResource, .nemesi, .nemesisST, .nemesisST  .newNemesis, .defeatedNemesi, .defeatedNemesisST { float: right; }
	.gearST .newGear, .resourcesST .newResource, .quariesSt .newQuary, .nemesisST .newNemesis, #newDefeatedQuary, #newDefeatedNemesis, .newInnovation, .newLocation { font-weight: normal; font-size: 16px; }
	.innovations, .locations, .gear, .resources, .defeatedQuaries, .defeatedNemesi { display: flex; flex-flow: row wrap; }
	.innovations .innovation, .locations .location, .gear .gearItem, .resources .resourceItem { border-bottom: 1px solid #9A9A9A;  border-left: 1px solid #9A9A9A; border-right: 1px solid #9A9A9A;padding: 3px 5px; flex: 1 1 calc(25% - 12px); }
	.innovations .innovation .innName, .locations .location .locName { font-weight: bold; text-decoration: underline; }
	.locGear { width: auto; }
	.locGear a { color: inherit; text-decoration: inherit; }
	.locGear a:not(:last-child) .locGearItem { width: 100%; overflow-y: auto; border-bottom: 1px solid #DDD; }
	.locGear.hidden { display: none; }
	.locGearName { width: 50%; float: left; }
	.locGearRecipe { width: 50%; float: right; }
	#toggleDead { margin-left: 10px; }
	table { border-collapse: seperate; border-spacing: 0px 10px; width: 100%; }
	table tr:first-child td{ border-bottom: 3px solid black; }
	table tr:not(:first-child) td { border-bottom: 1px solid #A8A9AD; }
	.has-tooltip { position: relative; }
	.has-tooltip .tooltip { opacity: 0; visibility: hidden; -webkit-transition: visibility 0s ease 0.5s,opacity .3s ease-in; -moz-transition: visibility 0s ease 0.5s,opacity .3s ease-in; -o-transition: visibility 0s ease 0.5s,opacity .3s ease-in; transition: visibility 0s ease 0.5s,opacity .3s ease-in; }
	.has-tooltip:hover .tooltip { opacity: 1; visibility: visible; }
	.tooltip { background-color: #d9d9d9; color: #000; position: absolute; text-align: center; z-index: 4; width: 400px; border: 1px solid #d6d200; bottom: 130%; left: 50%; margin-left: -300px; padding: 3px; border-radius: 3px; }
	.tooltip:after { border-top: 5px solid #d6d200; border-left: 4px solid transparent; border-right: 4px solid transparent; bottom: -5px; content: " "; font-size: 0px; left: 75%; line-height: 0%; margin-left: -6px; position: absolute; width: 0px; z-index: 1; }
	.notes { margin-top: 5px; }
	.notes #settlementNotes { width: 100%; margin-bottom: 5px; resize: none; }
	.survivors > table tr:not(:first-child):hover { background-color: #FF0; }
</style>
<script>
	function AJAXError(error) {
		document.getElementsByClassName("container")[0].innerHTML = JSON.stringify(error);
	}
	
	$(window).scroll(function() {
		localStorage.settlementScrollTop = $(this).scrollTop();
		classes = document.getElementsByClassName("locGear")[0].classList;
		if(classes.length > 1) {
			localStorage.setItem("gearState", 1)
		} else {
			localStorage.setItem("gearState", 0)
		}
	});
	
	$(document).ready(function() {
		var deadSurvivors = document.getElementsByClassName('deadSurvivor');
		if(deadSurvivors.length != 0) {
			for(var i=0; i<deadSurvivors.length; i++) {
				deadSurvivors[i].style.display = "none";
			}
		}
		
		if (localStorage.settlementScrollTop != "undefined") {
			$(window).scrollTop(localStorage.settlementScrollTop);
		}
		if(localStorage.gearState != "undefined") {
			var locGear = document.getElementsByClassName("locGear");
			if(localStorage.getItem("gearState") == 1) {
				for(var i=0; i<locGear.length; i++) {
					locGear[i].className += " hidden";
				}
			}
		}
		localStorage.settlementScrollTop = $(this).scrollTop();
		classes = document.getElementsByClassName("locGear")[0].classList;
		if(classes.length > 1) {
			localStorage.setItem("gearState", 1);
		}
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
			}
		});
	});
	
	function toggleLocationGear() {
		var locGear = document.getElementsByClassName("locGear");
		for(var i=0; i<locGear.length; i++) {
			locGear[i].classList.toggle('hidden');
		}
		classes = document.getElementsByClassName("locGear")[0].classList;
		if(classes.length > 1) {
			localStorage.setItem("gearState", 1)
		} else {
			localStorage.setItem("gearState", 0)
		}
	}
	
	function lanternYear(type) {
		var ly = document.getElementsByClassName('lanternYear')[0].getElementsByClassName('value')[0];
		$.ajax({
			method: 'POST',
			url: '/settlement/lanternYear',
			data : {'type' : type, 'SettlementID' : {{$settlement->SettlementID}}},
			success: function(response){ // What to do if we succeed
				ly.innerHTML = response;
			},
			error: function(jqXHR, textStatus, errorThrown) {
				AJAXError(jqXHR);
				console.log(JSON.stringify(jqXHR));
				console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
			}
		});
	}
	
	function principle(type, value, input) {
		var valTag = input.parentNode;
		$.ajax({
			method: 'POST',
			url: '/settlement/principle',
			data : {'type' : type, 'value' : value, 'SettlementID' : {{$settlement->SettlementID}}},
			success: function(response){ // What to do if we succeed
				location.reload();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				AJAXError(jqXHR);
				console.log(JSON.stringify(jqXHR));
				console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
			}
		});
	}
	
	function innovation(value, input) {
		var valTag = input.parentNode;
		$.ajax({
			method: 'POST',
			url: '/settlement/innovation',
			data : {'value' : value, 'SettlementID' : {{$settlement->SettlementID}}},
			success: function(response){ // What to do if we succeed
				if(response !== "") {
					location.reload();
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				AJAXError(jqXHR);
				console.log(JSON.stringify(jqXHR));
				console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
			}
		});
	}
	
	function removeInnovation(value) {
		$.ajax({
			method: 'POST',
			url: '/settlement/removeInnovation',
			data : {'value' : value, 'SettlementID' : {{$settlement->SettlementID}}},
			success: function(response){ // What to do if we succeed
				if(response !== "") {
					location.reload();
				}
				//AJAXError(response);
			},
			error: function(jqXHR, textStatus, errorThrown) {
				AJAXError(jqXHR);
				console.log(JSON.stringify(jqXHR));
				console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
			}
		});
	}
	
	function locationEdit(value, input) {
		var valTag = input.parentNode;
		$.ajax({
			method: 'POST',
			url: '/settlement/location',
			data : {'value' : value, 'SettlementID' : {{$settlement->SettlementID}}},
			success: function(response){ // What to do if we succeed
				if(response !== "") {
					location.reload();
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				AJAXError(jqXHR);
				console.log(JSON.stringify(jqXHR));
				console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
			}
		});
	}
	
	function addGear(item) {
		$.ajax({
			method: 'POST',
			url: '/settlement/addGear',
			data : {'value' : item, 'SettlementID' : {{$settlement->SettlementID}}},
			success: function(response){ // What to do if we succeed
				location.reload();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				AJAXError(jqXHR);
				console.log(JSON.stringify(jqXHR));
				console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
			}
		});
	}
	
	function removeGear(item) {
		$.ajax({
			method: 'POST',
			url: '/settlement/removeGear',
			data : {'value' : item, 'SettlementID' : {{$settlement->SettlementID}}},
			success: function(response){ // What to do if we succeed
				location.reload();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				AJAXError(jqXHR);
				console.log(JSON.stringify(jqXHR));
				console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
			}
		});
	}
	
	function addResource(item) {
		$.ajax({
			method: 'POST',
			url: '/settlement/addResource',
			data : {'value' : item, 'SettlementID' : {{$settlement->SettlementID}}},
			success: function(response){ // What to do if we succeed
				location.reload();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				AJAXError(jqXHR);
				console.log(JSON.stringify(jqXHR));
				console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
			}
		});
	}
	
	function removeResource(item) {
		$.ajax({
			method: 'POST',
			url: '/settlement/removeResource',
			data : {'value' : item, 'SettlementID' : {{$settlement->SettlementID}}},
			success: function(response){ // What to do if we succeed
				location.reload();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				AJAXError(jqXHR);
				console.log(JSON.stringify(jqXHR));
				console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
			}
		});
	}
	
	function addQuary(item) {
		$.ajax({
			method: 'POST',
			url: '/settlement/addQuary',
			data : {'value' : item, 'SettlementID' : {{$settlement->SettlementID}}},
			success: function(response){ // What to do if we succeed
				location.reload();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				AJAXError(jqXHR);
				console.log(JSON.stringify(jqXHR));
				console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
			}
		});
	}
	
	function addNemesis(item) {
		$.ajax({
			method: 'POST',
			url: '/settlement/addNemesis',
			data : {'value' : item, 'SettlementID' : {{$settlement->SettlementID}}},
			success: function(response){ // What to do if we succeed
				location.reload();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				AJAXError(jqXHR);
				console.log(JSON.stringify(jqXHR));
				console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
			}
		});
	}
	
	function addDefeatedQuary() {
		var item = document.getElementsByName("defeatedQuary")[0];
		var itemText  = item.options[item.selectedIndex].text;
		var level = document.getElementsByName("defeatedQuaryLevel")[0].value;
		$.ajax({
			method: 'POST',
			url: '/settlement/addDefeatedQuary',
			data : {'value' : item.value, 'level' : level, 'SettlementID' : {{$settlement->SettlementID}}},
			success: function(response){ // What to do if we succeed
				location.reload();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				AJAXError(jqXHR);
				console.log(JSON.stringify(jqXHR));
				console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
			}
		});
	}
	
	function addDefeatedNemesis() {
		var item = document.getElementsByName("defeatedNemesis")[0];
		var itemText  = item.options[item.selectedIndex].text;
		var level = document.getElementsByName("defeatedNemesisLevel")[0].value;
		$.ajax({
			method: 'POST',
			url: '/settlement/addDefeatedNemesis',
			data : {'value' : item.value, 'level' : level, 'SettlementID' : {{$settlement->SettlementID}}},
			success: function(response){ // What to do if we succeed
				location.reload();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				AJAXError(jqXHR);
				console.log(JSON.stringify(jqXHR));
				console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
			}
		});
	}
	
	function editNotes() {
		var notes = document.getElementById('settlementNotes');
		var button = document.getElementById('saveNotes');
		if(notes.disabled) {
			notes.disabled = false;
			button.value = "Save Notes";
		} else {
			$.ajax({
			method: 'POST',
			url: '/settlement/saveNotes',
			data : {'value' : notes.value, 'SettlementID' : {{$settlement->SettlementID}}},
			success: function(response){ // What to do if we succeed
				notes.disabled = true;
				button.value = "Edit Notes";
			},
			error: function(jqXHR, textStatus, errorThrown) {
				AJAXError(jqXHR);
				console.log(JSON.stringify(jqXHR));
				console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
			}
		});
		}
	}
	
	function toggleDead() {
		var deadSurvivors = document.getElementsByClassName('deadSurvivor');
		var button = document.getElementById('toggleDead');
		if(deadSurvivors.length != 0) {
			if(deadSurvivors[0].style.display == "none") {
				for(var i=0; i<deadSurvivors.length; i++) {
					deadSurvivors[i].style.display = "";
				}
				button.value = "Hide Dead";
			} else {
				for(var i=0; i<deadSurvivors.length; i++) {
					deadSurvivors[i].style.display = "none";
				}
				button.value = "Show Dead";
			}
		}
	}
</script>
<div class="container">
	<div class="settlementHeader">
		<div class="survival">
			<div class="survivalLimit">
				<span class="survivalLimitNum">{{ $settlement->Survival_Limit }}</span>
				<span class="survivalLimitText">Survival Limit</span>
					<span class="survivalDepartureNum"> {{ $settlement->Survival_Upon_Departure }}</span>
					<span class="survivalDepartureText">Survival Upon Departure<span>
			</div>
		</div>
		<div class="header">
			<span class="settlementName">{{ $settlement->Settlement_Name }}</span>
		</div>
		<div class="minorInfo">
			<span class="population">Population: {{ count($settlement->survivorsLiving) }}</span>
			<span class="lanternYear">Lantern Year: <span class="value">{{ $settlement->Current_Lantern_Year }}</span></span>
		</div>
	</div>
	<hr/>
	<div class="sectionTitle">
		Principles
	</div>
	<div class="principles">
		<div class="newLife">
			<div class="principleType">
				<b>Principle: New Life</b>
			</div>
			<div class="value">
				@if($settlement->Principle_New_Life === null)
					<input type="button"  value="Protect the Young" onclick="principle('newLife', 0, this); return false;"/>
					<input type="button"  value="Survival of the Fittest" onclick="principle('newLife', 1, this); return false;"/>
				@elseif($settlement->Principle_New_Life)
					<li>Survival of the Fittest</li>
				@else
					<li>Protect the Young</li>
				@endif
			</div>
		</div>
		<div class="death">
			<div class="principleType">
				<b>Principle: Death</b>
			</div>
			<div class="value">
				@if($settlement->Principle_Death === null)
					<input type="button"  value="Cannibalize" onclick="principle('death', 0, this); return false;"/>
					<input type="button"  value="Graves" onclick="principle('death', 1, this); return false;"/>
				@elseif($settlement->Principle_Death)
					<li>Graves</li>
				@else
					<li>Cannibalize</li>
				@endif
			</div>
		</div>
		<div class="society">
			<div class="principleType">
				<b>Principle: Society</b>
			</div>
			<div class="value">
				@if($settlement->Principle_Society === null)
					<input type="button"  value="Collective Toil" onclick="principle('society', 0, this); return false;"/>
					<input type="button"  value="Accept Darkness" onclick="principle('society', 1, this); return false;"/>
				@elseif($settlement->Principle_Society)
					<li>Accept Darkness</li>
				@else
					<li>Collective Toil</li>
				@endif
			</div>
		</div>
		<div class="conviction">
			<div class="principleType">
				<b>Principle: Conviction</b>
			</div>
			<div class="value">
				@if($settlement->Principle_Conviction === null)
					<input type="button"  value="Barbaric" onclick="principle('conviction', 0, this); return false;"/>
					<input type="button"  value="Romantic" onclick="principle('conviction', 1, this); return false;"/>
				@elseif($settlement->Principle_Conviction)
					<li>Romantic</li>
				@else
					<li>Barbaric</li>
				@endif
			</div>
		</div>
	</div>
	<hr/>
	<div class="sectionTitle">
		Innovations
		<span class="newInnovation">Add New Innovation: {!! Form::select('Innovation', [null=>''] + $innovations, '', array('onchange' => 'innovation(this.value, this)')) !!}</span>
	</div>
	<div class="innovations">
		@foreach($settlement->innovations() as $innovation)
			<div class="innovation">
				<div class="innName">
					<a href="#" onclick="removeInnovation({{ $innovation->InnovationID }}); return false;">-</a> {{ $innovation->Name }}
				</div>
				<div class="innDescription">
					{!! $innovation->Description !!}
				</div>
			</div>
		@endforeach
	</div>
	<hr/>
	<div class="sectionTitle">
		Locations
		<span><input type="button" onclick="toggleLocationGear(); return false;"  value="Toggle Location Gear"/></span>
		<span class="newLocation">Add New Location: {!! Form::select('Location', [null=>''] + $locations, '', array('onchange' => 'locationEdit(this.value, this)')) !!}</span>
	</div>
	<div class="locations">
		@foreach($settlement->locations() as $location)
			<div class="location">
				<div class="locName">
					{{ $location->Name }}
				</div>
				<div class="locGear">	
					@foreach($location->gear() as $gear)
						<a href="#" onclick="addGear({{ $gear->GearID }}); return false;">
							<div class="locGearItem">
								<div class="locGearName">
									{{ $gear->Name }}
								</div>
								<div class="locGearRecipe">
									@foreach($gear->recipe() as $recipeItem)
										<div>{{ $recipeItem['Name'] }}&nbsp;*{{ $recipeItem['Quantity'] }}</div>
									@endforeach
								</div>
							</div>
						</a>
					@endforeach
				</div>
			</div>
		@endforeach
	</div>
	<hr/>
	<div class="items">
		<div class="sectionTitle gearST">
			Gear
			<span class="newGear">Add New Gear: {!! Form::select('Gear', [null=>''] + $otherGear, '', array('onchange' => 'addGear(this.value)')) !!}</span>
		</div>
		<div class="sectionTitle resourcesST">
			Resources
			<span class="newResource">Add New Resource: {!! Form::select('Resource', [null=>''] + $resources, '', array('onchange' => 'addResource(this.value)')) !!}</span>
		</div>
		<div class="gear">
			@foreach ($settlement->gears() as $gear)
				<div class="gearItem">
					<a href="#" onclick="removeGear({{$gear->GearID }}); return false;">-</a>
					<span>{{ $gear->Quantity }}* {{ $gear->Name }}</span>
					<a href="#" onclick="addGear({{$gear->GearID }}); return false;">+</a>
				@foreach ($gear->kdmResources() as $kdmResource)
					<div style="color:red;">{{ $kdmResource->Name }} * {{ $kdmResource->Quantity }}</div>
				@endforeach
				
				@foreach ($gear->resourceTypes() as $kdmResource)
					<div style="color:red;">{{ $kdmResource->Name }} * {{ $kdmResource->Quantity }}</div>
				@endforeach
				</div>
			@endforeach
		</div>
		<div class="resources">
			<div class="totals" style="text-align: center;">
				@foreach($settlement->resourceTypes() as $type => $qty)
					<span style="color:red;margin:0 3px;">{{ $type }}*{{ $qty }}</span>
				@endforeach
			</div>
			@foreach ($settlement->kdmResources() as $resource)
				<div class="resourceItem">
					<a href="#" onclick="removeResource({{$resource->ResourceID }}); return false;">-</a>
					<span style="font-weight:bold;">{{ $resource->Quantity }}* {{ $resource->Name }}</span>
					@foreach($resource->types() as $type)
						<span style="color:red;">{{ $type->Name }}</span>
					@endforeach
					<a href="#" onclick="addResource({{$resource->ResourceID }}); return false;">+</a>
				</div>
			@endforeach
		</div>
	</div>
	<hr/>
	<div class="enemies">
		<div class="sectionTitle quariesSt">
			Quaries
			<span class="newQuary">Add New Quary: {!! Form::select('Quary', [null=>''] + $quary, '', array('onchange' => 'addQuary(this.value)')) !!}</span>
		</div>
		<div class="sectionTitle nemesisST">
			Nemesis Monsters
			<span class="newNemesis">Add New Nemesis: {!! Form::select('Nemesis', [null=>''] + $nemesis, '', array('onchange' => 'addNemesis(this.value)')) !!}</span>
		</div>
		<div class="quaries">
			@foreach($settlement->quaryCanHunt() as $quary)
				<div class="quary">
					{{ $quary->Name }}
				</div>
			@endforeach
		</div>
		<div class="nemesi">
			@foreach($settlement->nemesisCanHunt() as $nemesis)
				<div class="nemesis">
					{{ $nemesis->Name }}
				</div>
			@endforeach
		</div>
	</div>
	<hr/>
	<div class="defeatedEnemies">
		<div class="sectionTitle defeatedQuariesST">
			Defeated Quaries
			<span id="newDefeatedQuary">Defeat New Quary: {!! Form::select('defeatedQuary', [null=>''] + $quaryAvailable) !!} Level {!! Form::select('defeatedQuaryLevel', array('1' => 1, '2' => 2, '3' => 3), '', array('class' => 'small')) !!} <input type="button" value="Add" onclick="addDefeatedQuary(); return false;"/></span>
		</div>
		<div class="sectionTitle defeatedNemesisST">
			Defeated Nemesis
			<span id="newDefeatedNemesis">Defeat New Nemesis: {!! Form::select('defeatedNemesis', [null=>''] + $nemesisAvailable) !!} Level {!! Form::select('defeatedNemesisLevel', array('1' => 1, '2' => 2, '3' => 3), '', array('class' => 'small')) !!} <input type="button" value="Add" onclick="addDefeatedNemesis(); return false;"/></span>
		</div>
		<div class="defeatedQuaries">
			<table style="width: 100%;">
				<tr>
					<td style="border-right: 1px solid black; width: 32px; text-align: center;">
						Quantity
					</td>
					<td style="border-right: 1px solid black; padding: 0px 3px;  text-align: left;">
						Enemy
					</td>
					<td style="text-align: left; padding-left: 10px; width: 32px;">
						Level
					</td>
				</tr>
				@foreach($settlement->defeatedQuaries() as $quary)
					<tr>
						<td style="border-right: 1px solid #A8A9AD; width: 32px; text-align: center;">
							{{ $quary->Quantity }}
						</td>
						<td style="border-right: 1px solid #A8A9AD; padding: 0px 3px; text-align: left;">
							{{ $quary->Name }}
						</td>
						<td style="text-align: left; padding-left: 10px; width: 32px;">
							{{ $quary->Level }}
						</td>
					</tr>
				@endforeach
			</table>
		</div>
		<div class="defeatedNemesi">
			<table style="width: 100%;">
				<tr>
					<td style="border-right: 1px solid black; width: 32px; text-align: center;">
						Quantity
					</td>
					<td style="border-right: 1px solid black; padding: 0px 3px;  text-align: left;">
						Enemy
					</td>
					<td style="text-align: left; padding-left: 10px; width: 32px;">
						Level
					</td>
				</tr>
				@foreach($settlement->defeatedNemesis() as $nemesis)
					<tr>
						<td style="border-right: 1px solid #A8A9AD; width: 32px; text-align: center;">
							{{ $nemesis->Quantity }}
						</td>
						<td style="border-right: 1px solid #A8A9AD; padding: 0px 3px; text-align: left;">
							{{ $nemesis->Name }}
						</td>
						<td style="text-align: left; padding-left: 10px; width: 32px;">
							{{ $nemesis->Level }}
						</td>
					</tr>
				@endforeach
			</table>
		</div>
	</div>
	<hr/>
	<div class="sectionTitle">
		Survivors
		<input type="button" value="Show Dead" id="toggleDead" onclick="toggleDead(); return false;"/>
	</div>
	<div class="survivors">
		<table>
			<tr>
				<td style="width: 32px; text-align: center;">
					<div class="dead"></div>
				</td>
				<td style="border-right: 1px solid black; width: 150px; max-width: 150px; padding: 0px 3px;  text-align: left;">
					Name
				</td>
				<td style="border-right: 1px solid black; width: 32px; padding: 0px 3px; text-align: center;">
					Sex
				</td>
				<td style="border-right: 1px solid black; width: 32px; padding: 0px 3px; text-align: center;">
					MVT
				</td>
				<td style="border-right: 1px solid black; width: 32px; padding: 0px 3px; text-align: center;">
					ACC
				</td>
				<td style="border-right: 1px solid black; width: 32px; padding: 0px 3px; text-align: center;">
					STR
				</td>
				<td style="border-right: 1px solid black; width: 32px; padding: 0px 3px; text-align: center;">
					EVA
				</td>
				<td style="border-right: 1px solid black; width: 32px; padding: 0px 3px; text-align: center;">
					LCK
				</td>
				<td style="border-right: 1px solid black; width: 32px; padding: 0px 3px; text-align: center;">
					SPD
				</td>
				<td style="border-right: 1px solid black; width: 32px; padding: 0px 3px; text-align: center;">
					Insanity
				</td>
				<td style="border-right: 1px solid black; width: 32px;  padding: 0px 3px;  text-align: center;">
					Survival
				</td>
				<td style="border-right: 1px solid black; width: 32px;  padding: 0px 3px;  text-align: center;">
					Hunt&nbsp;XP
				</td>
				<td style="border-right: 1px solid black; width: 32px;  padding: 0px 3px;  text-align: center;">
					Weapon Proficiency
				</td>
				<td style="border-right: 1px solid black; width: 32px;  padding: 0px 3px;  text-align: center;">
					Weapon Proficiency Level
				</td>
				<td style="border-right: 1px solid black; width: 32px;  padding: 0px 3px;  text-align: center;">
					Courage
				</td>
				<td style="border-right: 1px solid black; width: 32px;  padding: 0px 3px;  text-align: center;">
					Understanding
				</td>
				<td style="border-right: 1px solid black; padding-left: 10px;  text-align: center;">
					Fighting Arts
				</td>
				<td style="text-align: left; padding-left: 10px;">
					Disorders
					<input type="button" style="float: right;" value="Add New Survivors" onclick="location.href = '/newSurvivors/{{$settlement->SettlementID}}';"/>
				</td>
			</tr>
			@foreach ($settlement->survivors as $survivor)
				<tr onclick="window.location = '{!! route('editSurvivor', ['survivor'=>$survivor]) !!}'"
				@if($survivor->Dead == '1')
					class="deadSurvivor"
				@endif
				>
					<td style="width: 32px; text-align: center;">
						@if($survivor->Dead)
							<div class="radioOn"></div>
						@else
							<div class="radioOff"></div>
						@endif
					</td>
					<td style="border-right: 1px solid #A8A9AD; width: 150px;  max-width: 150px; padding: 0px 3px; text-align: left;">
						@if($survivor->Saviour === 1)
							(S)
						@endif
						{{ $survivor->fullname() }}
					</td>
					<td style="border-right: 1px solid #A8A9AD; width: 32px; padding: 0px 3px; text-align: center;">
						@if($survivor->Gender)
							M
						@else
							F
						@endif
					</td>
					<td style="border-right: 1px solid #A8A9AD; width: 32px;  padding: 0px 3px; text-align: center;">
						{{ $survivor->Movement }}
					</td>
					<td style="border-right: 1px solid #A8A9AD; width: 32px;  padding: 0px 3px; text-align: center;">
						{{ $survivor->Accuracy }}
					</td>
					<td style="border-right: 1px solid #A8A9AD; width: 32px;  padding: 0px 3px; text-align: center;">
						{{ $survivor->Strength }}
					</td>
					<td style="border-right: 1px solid #A8A9AD; width: 32px;  padding: 0px 3px; text-align: center;">
						{{ $survivor->Evasion }}
					</td>
					<td style="border-right: 1px solid #A8A9AD; width: 32px;  padding: 0px 3px; text-align: center;">
						{{ $survivor->Luck }}
					</td>
					<td style="border-right: 1px solid #A8A9AD; width: 32px;  padding: 0px 3px; text-align: center;">
						{{ $survivor->Speed }}
					</td>
					<td style="border-right: 1px solid #A8A9AD; width: 32px;  padding: 0px 3px; text-align: center;">
						{{ $survivor->Insanity }}
					</td>
					<td style="border-right: 1px solid #A8A9AD; width: 32px;  rpadding: 0px 3px; text-align: center;">
						{{ $survivor->Survival }}
					</td>
					<td style="border-right: 1px solid #A8A9AD; width: 32px;  rpadding: 0px 3px; text-align: center;">
						{{ $survivor->Hunt_XP }}
					</td>
					<td style="border-right: 1px solid #A8A9AD; width: 32px;  rpadding: 0px 3px; text-align: center;">
						@if($survivor->weaponProficiency() !== null)
							{{ $survivor->weaponProficiency()->Name }}
						@else
							<i>None</i>
						@endif
					</td>
					<td style="border-right: 1px solid #A8A9AD; width: 32px;  rpadding: 0px 3px; text-align: center;">
						{{ $survivor->Weapon_Proficiency }}
					</td>
					<td style="border-right: 1px solid #A8A9AD; width: 32px;  rpadding: 0px 3px; text-align: center;">
						{{ $survivor->Courage }}
					</td>
					<td style="border-right: 1px solid #A8A9AD; width: 32px;  rpadding: 0px 3px; text-align: center;">
						{{ $survivor->Understanding }}
					</td>
					<td style="text-align: left; padding-left: 10px;">
						{!! $survivor->fightingArtSummary() !!}
					</td>
					<td style="text-align: left; padding-left: 10px;">
						{!! $survivor->disorderSummary() !!}
					</td>
				</tr>
			@endforeach
		</table>
	</div>
	<hr/>
	<div class="sectionTitle">
		Notes
	</div>
	<div class="notes">
		<textarea disabled id="settlementNotes" rows="10" cols="100" maxlength="65534">{{ $settlement->Additional_Settlement_Notes }}</textarea>
		<input type="button" id="saveNotes" value="Edit Notes" onclick="editNotes(); return false;"/>
	</div>
</div>
<div id="tester">
</div>
@endsection
