@extends('master')
@section('title', 'Survivor') 
@section('content')
<style>
	#lightbox { z-index: 100; position: absolute; width: calc(90% - 40px); }
	#lightbox > div { height: calc(100% - 10px); width: 100%; position: absolute; top: 0px; left: 0px; background-color: rgba(0,0,0, 0.25);} }
	.survivor { width: 90%; border: 3px solid rgb(100,100,100); margin: 0 auto; padding: 2px;}
	.buttons { overflow-y: auto; margin-bottom: 5px; }
	.buttons input { float: left; margin-right: 10px; }
	.buttons .btn-success { float: right; }
	.topHead { overflow-y: auto; }
	.topHead>:not(:last-child) { float: left; }
	.topHead>:last-child { float: left; width: auto; }
	.survivorDead table td:first-child { width: 50px; }
	.survivorDead table td:last-child { width: 500px; }
	.survivorDead input[type="text"] { width: 500px; }
	.survivorInfo { overflow-y: auto; width: 100%; }
	.survivorInfo .huntXP { width: 60px; float: right; border: 1px solid black; }
	.survivorInfo .huntXP > div:not(.retired) { float: right; }
	.survivorName { float: left; }
	.survivorName > table { font-size: 16px; }
	.survivorName > table tr.title { font-weight: bold; }
	.survivorName > table td { padding: 0px 3px; }
	#retireButton { float: right; }
	.survivorXP { float: right; }
	.survival { width: auto; height: auto; overflow-y: auto; }
	.survivorInjuries { overflow-y: auto; margin: 3px 5px 5px 5px; }
	.insanity { float: left; width: 250px; }
	.survivalValue { float: right; }
	.survivalActions { float: right; }
	
	.leftRightContainer { width: 100%; display: table; border-collapse: separate; border-spacing: 3px; }
	.leftRightContainer > div { width: 100%; display: table-row; }
	.leftRightContainer > div > div { padding-left: 3px; width: 50%; width: 50%; display: table-cell; }
	.leftHalf { border-right: 1px solid #9A9A9A; }
	.rightHalf { border-left: 1px solid #9A9A9A; }
	
	.sectionTitle { font-weight: bold; text-decoration: underline; font-size: 20px; margin: 10px 0; }
	.sectionTitle > span { text-decoration: none; float: right; }
	
	.formInput { border: 1px solid black; overflow-y: auto; }
	.field { display: inline-block; width: auto; margin: 0 auto; }
	.adjusters { float: right; margin: 0;}
	input[type=text][readonly] { border: none; text-align: center; width: 16px; }
	
	.survivorStats { overflow-y: auto; margin: 3px 5px 5px 5px; }	
	input[type=button] { display: block; }
	.inlineButtons input[type=button] { display: inline; }
	.statHeaders, .stats { overflow-y: auto; }
	.stats > span:not(:last-child), .injuries > span:not(:last-child) { margin-right: 3px; }
	.statHeaders span { font-size: 10px; }
	.statHeaders span, .stats span { float: left; width: calc(100% / 6 - 5px);  text-align: center; }

	.injury { float: right; margin: 0; }
	.injuryHeaders, .injuries { overflow-y: auto; }
	.injuryHeaders span { font-size: 10px; }
	.injuryHeaders span, .injuries span { float: left; width: calc(100% - 5px);  text-align: center; }
	
	.heading { float: left; }

	.disorders > div > div:first-child, .fightingArts > div > div:first-child { overflow-y: auto; width: auto; font-weight: bold; }
	.disorders > div > div:first-child > input, .fightingArts > div > div:first-child > input { float: left; margin-right: 5px; }
	.top { overflow-y: auto; }
	.abilities, .impairments { padding: 5px; }
	.noAbilities, .specificImpairments { float: right; }
	.noAbilities table, .specificImpairments table { border-collapse: collapse; margin: 3px; }
	.noAbilities table tr td, .specificImpairments table tr td { text-align: center; border: 1px solid #c7c7c7; padding: 3px; }
	#newAbilityDescription, #newImpairmentDescription { width: 1000px; }
	.specificImpairments > table td { max-width: 110px; }
	.notes { margin-top: 5px; }
	.notes #survivorNotes { width: 100%; margin-bottom: 5px; resize: none; }
</style>
<script type="text/javascript">
	function AJAXError(error) {
		document.getElementsByClassName("container")[0].innerHTML = JSON.stringify(error);
	}
	
	$(window).scroll(function() {
		sessionStorage.survivorScrollTop = $(this).scrollTop();
	});
	
	$(document).ready(function() {
		if({{$survivor->Dead}} === 1) {
			var lightbox = document.getElementById('lightbox');
			var ref = lightbox.parentNode.parentElement;
			lightbox.style.height = ref.offsetHeight - 40;
		}
		
		var i;
		var wpBoxes = document.querySelectorAll('#weaponProficiencyLevel input[type="checkbox"]');
		for(i=0; i < {{ $survivor->Weapon_Proficiency }}; i++) {
			wpBoxes[i].checked = true;
		}
		var courageBoxes = document.querySelectorAll('#courageLevel input[type="checkbox"]');
		for(i=0; i < {{ $survivor->Courage }}; i++) {
			courageBoxes[i].checked = true;
		}
		var understandingBoxes = document.querySelectorAll('#understandingLevel input[type="checkbox"]');
		for(i=0; i < {{ $survivor->Understanding }}; i++) {
			understandingBoxes[i].checked = true;
		}
		if (sessionStorage.survivorScrollTop != "undefined") {
			$(window).scrollTop(sessionStorage.survivorScrollTop);
		}
		sessionStorage.survivorScrollTop = $(window).scrollTop();
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
			}
		});
	});
	
	function deleteSurvivor() {
		if(confirm('Are you sure you want to delete this survivor?')) {
			$.ajax({
			method: 'POST',
			url: '/survivor/delete',
			data : {'SurvivorID' : {{$survivor->SurvivorID}}},
			success: function(response){ // What to do if we succeed
				location.href = '/settlement/{{ $survivor->settlement->SettlementID }}';
			},
			error: function(jqXHR, textStatus, errorThrown) {
				AJAXError(jqXHR);
				console.log(JSON.stringify(jqXHR));
				console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
			}
		});
		}
	}
	
	function stat(stat, change, input) {
		var span = document.getElementById(stat);
		$.ajax({
			method: 'POST',
			url: '/survivor/stat',
			data : {'stat' : stat, 'change': change, 'SurvivorID' : {{$survivor->SurvivorID}}},
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
	
	function toggleDead(input) {
		var COD = input.parentNode.parentNode.getElementsByTagName('td')[1].getElementsByTagName('input')[0];
		COD.disabled = !input.checked;
	}
	
	function retire() {
		if(confirm('Are you sure you wish to retire {{ $survivor->Name }}. This operation cannot be undone!')) {
			$.ajax({
			method: 'POST',
			url: '/survivor/retire',
			data : {'SurvivorID' : {{$survivor->SurvivorID}}},
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
	}
	
	function addDisorder(value) {
		$.ajax({
			method: 'POST',
			url: '/survivor/disorder',
			data : {'value' : value, 'SurvivorID' : {{$survivor->SurvivorID}}},
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
	
	function removeDisorder(value) {
		$.ajax({
			method: 'POST',
			url: '/survivor/disorder',
			data : {'value' : value, 'remove' : 'remove', 'SurvivorID' : {{$survivor->SurvivorID}}},
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
	
	function addFightingArt(value) {
		$.ajax({
			method: 'POST',
			url: '/survivor/fightingArt',
			data : {'value' : value, 'SurvivorID' : {{$survivor->SurvivorID}}},
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
	
	function removeFightingArt(value) {
		$.ajax({
			method: 'POST',
			url: '/survivor/fightingArt',
			data : {'value' : value, 'remove' : 'remove', 'SurvivorID' : {{$survivor->SurvivorID}}},
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
	
	function addSecretFightingArt(value) {
		$.ajax({
			method: 'POST',
			url: '/survivor/secretFightingArt',
			data : {'value' : value, 'SurvivorID' : {{$survivor->SurvivorID}}},
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
	
	function removeSecretFightingArt(value) {
		$.ajax({
			method: 'POST',
			url: '/survivor/secretFightingArt',
			data : {'value' : value, 'remove' : 'remove', 'SurvivorID' : {{$survivor->SurvivorID}}},
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
	
	function newAbility() {
		var name = document.getElementById('newAbilityName').value;
		var description = document.getElementById('newAbilityDescription').value;
		$.ajax({
			method: 'POST',
			url: '/survivor/ability',
			data : {'name' : name, 'description' : description, 'type' : 'add', 'SurvivorID' : {{$survivor->SurvivorID}}},
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
	
	function removeAbility(value) {
		$.ajax({
			method: 'POST',
			url: '/survivor/ability',
			data : {'value' : value, 'type' : 'remove', 'SurvivorID' : {{$survivor->SurvivorID}}},
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
	
	function newImpairment() {
		var name = document.getElementById('newImpairmentName').value;
		var description = document.getElementById('newImpairmentDescription').value;
		$.ajax({
			method: 'POST',
			url: '/survivor/impairment',
			data : {'name' : name, 'description' : description, 'type' : 'add', 'SurvivorID' : {{$survivor->SurvivorID}}},
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
	
	function removeImpairment(value) {
		$.ajax({
			method: 'POST',
			url: '/survivor/impairment',
			data : {'value' : value, 'type' : 'remove', 'SurvivorID' : {{$survivor->SurvivorID}}},
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
	
	function applyWeaponProficiencyType(value) {
		$.ajax({
			method: 'POST',
			url: '/survivor/wpt',
			data : {'value' : value, 'SurvivorID' : {{$survivor->SurvivorID}}},
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
	
	function wpl(value) {
		if((value == '1' && {{ $survivor->Weapon_Proficiency }} < 8) || (value == '-1' && {{ $survivor->Weapon_Proficiency }} > 0)) {
			$.ajax({
				method: 'POST',
				url: '/survivor/wpl',
				data : {'value' : value, 'SurvivorID' : {{$survivor->SurvivorID}}},
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
	}
	
	function applyCourageType(value) {
		$.ajax({
			method: 'POST',
			url: '/survivor/courageType',
			data : {'value' : value, 'SurvivorID' : {{$survivor->SurvivorID}}},
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
	
	function courage(value) {
		if((value == '1' && {{ $survivor->Courage }} < 9) || (value == '-1' && {{ $survivor->Courage }} > 0)) {
			$.ajax({
				method: 'POST',
				url: '/survivor/courage',
				data : {'value' : value, 'SurvivorID' : {{$survivor->SurvivorID}}},
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
	}
	
	function applyUnderstandingType(value) {
		$.ajax({
			method: 'POST',
			url: '/survivor/understandingType',
			data : {'value' : value, 'SurvivorID' : {{$survivor->SurvivorID}}},
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
	
	function understanding(value) {
		if((value == '1' && {{ $survivor->Understanding }} < 9) || (value == '-1' && {{ $survivor->Understanding }} > 0)) {
			$.ajax({
				method: 'POST',
				url: '/survivor/understanding',
				data : {'value' : value, 'SurvivorID' : {{$survivor->SurvivorID}}},
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
	}
	
	function editNotes() {
		var notes = document.getElementById('survivorNotes');
		var button = document.getElementById('saveNotes');
		if(notes.disabled) {
			notes.disabled = false;
			button.value = "Save Notes";
		} else {
			$.ajax({
			method: 'POST',
			url: '/survivor/saveNotes',
			data : {'value' : notes.value, 'SurvivorID' : {{$survivor->SurvivorID}}},
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
</script>
<div class="container">
	{!! Form::model($survivor, ['action' => 'SurvivorController@saveSurvivor']) !!}
		<div class="buttons">
			<input type="button"  value="Back" onclick="location.href = '/settlement/{{ $survivor->settlement->SettlementID }}';"/>
			<input type="button" value="Delete" onclick="deleteSurvivor(); return false;"/>
			<button class="btn btn-success" type="submit">Save</button>
		</div>
		@if($survivor->Dead)
			<div id="lightbox">
				<div>
				</div>
			</div>
		@endif
		<div class="survivor">
			{!! Form::text('Survivor[ID]', $survivor->SurvivorID, ['class' => 'form-control', 'style' => 'display:none']) !!}
			<div class="topHead">
				<div class="survivorInfo">
					<span class="survivorName">
						<table>
							<tr class="title">
								<td>
									Name
								</td>
								@if($hasFamilyInnovation)
									<td>
										Surname
									</td>
								@endif
								<td>
									Gender
								</td>
								@if($survivor->Saviour == 1)
									<td>
									</td>
								@endif
							</tr>
							<tr>
								<td>
									@if($survivor->Name != null)
										{{ $survivor->Name }} 
									@else
										{!! Form::text('Survivor[Name]', '',  ['class' => 'form-control']) !!}	
									@endif
								</td>
								@if($hasFamilyInnovation)
									<td>
										@if($survivor->Surname != null)
											{{ $survivor->Surname }} 
										@else
											{!! Form::text('Survivor[Surname]', '',  ['class' => 'form-control']) !!}
										@endif
									</td>
								@endif
								<td style="text-align:center;">
									@if($survivor->Gender)
										(M)
									@else
										(F)
									@endif
								</td>
								@if($survivor->Saviour == 1)
									<td style="text-align:center;">
										(Saviour)
									</td>
								@endif
							</tr>
							<tr class="title">
								@if($survivor->parents()[0]->first())
									<td>
										Father
									</td>
								@endif
								@if($survivor->parents()[0]->first())
									<td>
										Mother
									</td>
								@endif
							</tr>
							<tr>
								@if($survivor->parents()[0]->first())
									<td onclick="window.location = '{!! route('editSurvivor', ['survivor'=>$survivor->parents()[0]->first()]) !!}'">
										{{ $survivor->parents()[0]->first()->fullname() }}
									</td>
								@endif
								@if($survivor->parents()[1]->first())
									<td onclick="window.location = '{!! route('editSurvivor', ['survivor'=>$survivor->parents()[1]->first()]) !!}'">
										{{ $survivor->parents()[1]->first()->fullname() }}
									</td>
								@endif
							</tr>
						</table>
					</span>
					<input type="button"  value="Retire" onclick=" retire()" id="retireButton"/>
					<div class="huntXP">
						@if($survivor->Retired == 1)
							<div class="retired">
								Retired
							</div>
						@endif
						<span id="XP">{{ $survivor->Hunt_XP }} XP</span>
						@if($survivor->Retired == 0)
							<div>
								<input type="button"  value="+" onclick="stat('XP', '1', this)"/>
								<input type="button"  value="-" onclick="stat('XP', '-1', this)"/>
							</div>
						@endif
					</div>						
				</div>
				<div class="survivorDead">
					<table>
						@if($survivor->Dead)
							<tr>
								<td>
									Dead: 
								</td>
								<td>
									{{ $survivor->Cause_Of_Death }}
								</td>
							</tr>
						@else
							<tr>
								<td> Dead </td>
								<td> Cause of Death </td>
							</tr>
							<tr>
								<td>
									{!! Form::checkbox('Survivor[Dead]', 1, $survivor->Dead, ['class' => 'form-control', 'onclick' => 'toggleDead(this)']) !!}
								</td>
								<td>
									{!! Form::text('Survivor[CauseOfDeath]', '',  ['class' => 'form-control', 'disabled']) !!}
								</td>
							</tr>
						@endif
					</table>
				</div>
			</div>
			<div class="survivorInjuries">
				<div class="insanity">
					<div class="injuryHeaders">
						<span class="insanity">
							Insanity
						</span>
					</div>
					<div class="injuries">
						<span class="insanity formInput">
							<div class="field">
								<span id="INS">{{ $survivor->Insanity }}</span>
							</div>
							<div class="adjusters">
								<input type="button"  value="+" onclick="stat('INS', '1', this)"/>
								<input type="button"  value="-" onclick="stat('INS', '-1', this)"/>
							</div>
						</span>
					</div>
				</div>
				<div class="survival">
					<div class="survivalActions">
						<ul>
							<li class="@if($survivor->Dodge) on @else off @endif">Dodge</li>
							<li class="@if($survivor->Encourage) on @else off @endif">Encourage</li>
							<li class="@if($survivor->Surge) on @else off @endif">Surge</li>
							<li class="@if($survivor->Dash) on @else off @endif">Dash</li>
							<li class="@if($survivor->Endure) on @else off @endif">Endure</li>
						</ul>
					</div>
					<div class="survivalValue">
						<div>
							Survival
						</div>
						<div class="survivorSurvival formInput">
							<div class="field">
								<span id="SUR">{{ $survivor->Survival }}</span>
							</div>
							<div class="adjusters">
								<input type="button"  value="+" onclick="stat('SUR', '1', this)"/>
								<input type="button"  value="-" onclick="stat('SUR', '-1', this)"/>
							</div>
						</div>
					</div>
				</div>
			</div>
			<hr/>
			<div class="survivorStats">
				<div class="statHeaders">
					<span class="movement">MVT</span>
					<span class="accuracy">ACC</span>
					<span class="strength">STR</span>
					<span class="evasion">EVA</span>
					<span class="luck">LCK</span>
					<span class="speed">SPD</span>
				</div>
				<div class="stats">
					<span class="movement formInput">
						<div class="field">
							<span id="MVT">{{ $survivor->Movement }}</span>
						</div>
						<div class="adjusters">
							<input type="button"  value="+" onclick="stat('MVT', '1', this)"/>
							<input type="button"  value="-" onclick="stat('MVT', '-1', this)"/>
						</div>
					</span>
					<span class="accuracy formInput">
						<div class="field">
							<span id="ACC">{{ $survivor->Accuracy }}</span>
						</div>
						<div class="adjusters">
							<input type="button"  value="+" onclick="stat('ACC', '1', this)"/>
							<input type="button"  value="-" onclick="stat('ACC', '-1', this)"/>
						</div>
					</span>
					<span class="strength formInput">
						<div class="field">
							<span id="STR">{{ $survivor->Strength }}</span>
						</div>
						<div class="adjusters">
							<input type="button"  value="+" onclick="stat('STR', '1', this)"/>
							<input type="button"  value="-" onclick="stat('STR', '-1', this)"/>
						</div>
					</span>
					<span class="evasion formInput">
						<div class="field">
							<span id="EVA">{{ $survivor->Evasion }}</span>
						</div>
						<div class="adjusters">
							<input type="button"  value="+" onclick="stat('EVA', '1', this)"/>
							<input type="button"  value="-" onclick="stat('EVA', '-1', this)"/>
						</div>
					</span>
					<span class="luck formInput">
						<div class="field">
							<span id="LCK">{{ $survivor->Luck }}</span>
						</div>
						<div class="adjusters">
							<input type="button"  value="+" onclick="stat('LCK', '1', this)"/>
							<input type="button"  value="-" onclick="stat('LCK', '-1', this)"/>
						</div>
					</span>
					<span class="speed formInput">
						<div class="field">
							<span id="SPD">{{ $survivor->Speed }}</span>
						</div>
						<div class="adjusters">
							<input type="button"  value="+" onclick="stat('SPD', '1', this)"/>
							<input type="button"  value="-" onclick="stat('SPD', '-1', this)"/>
						</div>
					</span>
				</div>
			</div>
			<hr/>
			<div class="leftRightContainer">
				<div>
					<div class="leftHalf">
						<div class="sectionTitle">
							Fighting Arts &amp; Secret Fighting Arts
						</div>
						<div class="fightingArts">
							@if($survivor->faLimit())
								<div class="newFightingArt">Add New Fighting Art: {!! Form::select('FightingArt', [null=>''] + $fightingArts, '', array('onchange' => 'addFightingArt(this.value)')) !!}</div>
								<div class="newFightingArt">Add New Secret Fighting Art: {!! Form::select('SecretFightingArt', [null=>''] + $secretFightingArts, '', array('onchange' => 'addSecretFightingArt(this.value, this)')) !!}</div>
							@endif
							@foreach ($survivor->fightingArts() as $fa)
								<div class="fightingArt">
									<div class="faName">
										<input type="button"  value="Remove" onclick="removeFightingArt({{$fa->Fighting_ArtID}})"/> {{ $fa->Name }}
									</div>
									<div class="faDescription">
										{!! $fa->Description !!}
									</div>
								</div>
							@endforeach
							@foreach ($survivor->secretFightingArts() as $sfa)
								<div class="secretFightingArt">
									<div class="sfaName">
										<input type="button"  value="Remove" onclick="removeSecretFightingArt({{$sfa->Secret_Fighting_ArtID}})"/> {{ $sfa->Name }}
									</div>
									<div class="sfaDescription">
										{!! $sfa->Description !!}
									</div>
								</div>
							@endforeach
						</div>
						<hr/>
						<div class="sectionTitle">
							Disorders
						</div>
						<div class="disorders">
							@if($survivor->disorderLimit())
								<span class="newDisorder">Add New Disorder: {!! Form::select('Disorder', [null=>''] + $disorders, '', array('onchange' => 'addDisorder(this.value)')) !!}</span>
							@endif
							@foreach ($survivor->disorders() as $d)
								<div class="disorder">
									<div class="dName">
										<input type="button"  value="Remove" onclick="removeDisorder({{$d->DisorderID}})"/> {{ $d->Name }}
									</div>
									<div class="dDescription">
										{!! $d->Description !!}
									</div>
								</div>
							@endforeach
						</div>
					</div>
					<div class="rightHalf inlineButtons">
						<div class="sectionTitle">
							Weapon Proficiency
						</div>
						<div class="weaponProficiency">
							<div class="leftRightContainer">
								<div>
									<div class="leftHalf">
										<div class="weaponProficiencyType">
											@if($survivor->weaponProficiency() == null)
												{!! Form::select('Weapon Proficiency', [null=>''] + $weapons, '', array('onchange' => 'applyWeaponProficiencyType(this.value)')) !!}
											@else
												<b>{{ $survivor->weaponProficiency()->Name }}</b>
												<ul>
													@if($survivor->Weapon_Proficiency >= 3)
														<b>Specialist:</b>
														<li>
															{!! $survivor->weaponProficiency()->Specialist !!}
														</li>
													@endif
													@if($survivor->Weapon_Proficiency >= 8)
														<b>Master:</b> 
														<li>
															{!! $survivor->weaponProficiency()->Mastery !!}
														</li>
													@endif
												</ul>
											@endif
											<ul>
												@foreach($weaponSpecs as $weaponSpec)
													@if($survivor->weaponProficiency() == null || $weaponSpec->Name != $survivor->weaponProficiency()->Name)
														<b>{{ $weaponSpec->Name }} Specialist from a Master:</b>
														<li>
															{!! $weaponSpec->Specialist !!}
														</li>
													@endif
												@endforeach
											</ul>
										</div>
									</div>
									<div class="rightHalf">
										<div id="weaponProficiencyLevel">
											<input type="button"  value="-" onclick="wpl('-1')"/>
											<input type="checkbox" class="1" onclick="return false;"/>
											<input type="checkbox" class="2" onclick="return false;"/>
											<input type="checkbox" class="3"onclick="return false;"/>
											<input type="checkbox" class="4" onclick="return false;"/>
											<input type="checkbox" class="5" onclick="return false;"/>
											<input type="checkbox" class="6" onclick="return false;"/>
											<input type="checkbox" class="7" onclick="return false;"/>
											<input type="checkbox" class="8" onclick="return false;"/>
											<input type="button"  value="+" onclick="wpl('1')"/>
										
										</div>
									</div>
								</div>
							</div>
						</div>
						<hr/>
						<div class="sectionTitle">
							Courage
						</div>
						<div class="courage">
							<div class="weaponProficiencyType">
								<div class="leftRightContainer">
									<div>
										<div class="leftHalf">
											<div>
												Bold Reward
											</div>
											@if($survivor->Courage_Type == null)
												{!! Form::select('Bold', [null=>''] + $knowledgeCourage, '', array('onchange' => 'applyCourageType(this.value)')) !!}
											@else
												<ul>
													<li>
														{{ $survivor->Courage_Type()->first()->Name }}
													</li>
													<li>
														{!! $survivor->Courage_Type()->first()->Description !!}
													</li>
												</ul>
											@endif
										</div>
										<div class="rightHalf">
											<div id="courageLevel">
												<input type="button"  value="-" onclick="courage('-1')"/>
												<input type="checkbox" class="1" onclick="return false;"/>
												<input type="checkbox" class="2" onclick="return false;"/>
												<input type="checkbox" class="3"onclick="return false;"/>
												<input type="checkbox" class="4" onclick="return false;"/>
												<input type="checkbox" class="5" onclick="return false;"/>
												<input type="checkbox" class="6" onclick="return false;"/>
												<input type="checkbox" class="7" onclick="return false;"/>
												<input type="checkbox" class="8" onclick="return false;"/>
												<input type="checkbox" class="9" onclick="return false;"/>
												<input type="button"  value="+" onclick="courage('1')"/>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<hr/>
						<div class="sectionTitle">
							Understanding
						</div>
						<div class="understanding">
							<div class="leftRightContainer">
								<div>
									<div class="leftHalf">
										<div>
											Insight Reward
										</div>
										@if($survivor->Understanding_Type == null)
											{!! Form::select('Insight', [null=>''] + $knowledgeUnderstanding, '', array('onchange' => 'applyUnderstandingType(this.value)')) !!}
										@else
											<ul>
												<li>
													{{ $survivor->Understanding_Type()->first()->Name }}
												</li>
												<li>
													{!! $survivor->Understanding_Type()->first()->Description !!}
												</li>
											</ul>
										@endif
									</div>
									<div class="rightHalf">
										<div id="understandingLevel">
											<input type="button"  value="-" onclick="understanding('-1')"/>
											<input type="checkbox" class="1" onclick="return false;"/>
											<input type="checkbox" class="2" onclick="return false;"/>
											<input type="checkbox" class="3"onclick="return false;"/>
											<input type="checkbox" class="4" onclick="return false;"/>
											<input type="checkbox" class="5" onclick="return false;"/>
											<input type="checkbox" class="6" onclick="return false;"/>
											<input type="checkbox" class="7" onclick="return false;"/>
											<input type="checkbox" class="8" onclick="return false;"/>
											<input type="checkbox" class="9" onclick="return false;"/>
											<input type="button"  value="+" onclick="understanding('1')"/>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<hr/>
			<div class="abilities">
				<div class="top">
					<div class="heading">
						<h3>Abilities</h3>
					</div>
					<div class="noAbilities">
						<table>
							<tr>
								<td>
									Cannot Use Abilities
								</td>
							</tr>
							<tr>
								<td>
									@if($survivor->Cannot_Use_Abilities == 1)
										{!! Form::checkbox('Survivor[Cannot_Use_Abilities]', 1, true, ['class' => 'form-control']) !!}
									@else
										{!! Form::checkbox('Survivor[Cannot_Use_Abilities]', 1, false, ['class' => 'form-control']) !!}
									@endif
								</td>
							</tr>
						</table>
					</div>
				</div>
				<div id="currentAbilities">
					<table style="width: 100%;">
						<tr>
							<td>
								Name
							</td>
							<td>
								Description
							</td>
							<td>
							</td>
						</tr>
						@foreach ($survivor->abilities() as $a)
							<tr>
								<td>	
									{{ $a->Name }}
								</td>
								<td>
									{!! $a->Description !!}
								</td>
								<td>
									<input type="button"  value="Remove" onclick="removeAbility({{ $a->AbilityID}})"/>
								</td>
							</tr>
						@endforeach
						<tr>
							<td>
								<input type="text" id="newAbilityName"/>
							</td>
							<td>
								<textarea id="newAbilityDescription"></textarea>
							</td>
							<td>
								<input type="button"  value="New Ability" onclick="newAbility()"/>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<hr/>
			<div class="impairments">
				<div class="top">
					<div class="heading">
						<h3>Impairments</h3>
					</div>
					<div class="specificImpairments">
						<table>
							<tr>
								<td>
									Cannot Activate Weapons
								</td>
								<td>
									Cannot Activate 2H Weapons
								</td>
								<td>
									Cannot Activate +2 Str Gear
								</td>
								<td>
									Cannot Consume
								</td>
								<td>
									No Intimacy
								</td>
								<td>
									Skip Next Hunt
								</td>
								<td>
									Cannot Gain Survival
								</td>
								<td>
									Cannot Spend Survival
								</td>
								<td>
									Cannot Use Abilities or Fighting Arts
								</td>
							</tr>
							<tr>
								<td>
									@if($survivor->Cannot_Activate_Weapons == 1)
										{!! Form::checkbox('Survivor[Cannot_Activate_Weapons]', 1, true, ['class' => 'form-control']) !!}
									@else
										{!! Form::checkbox('Survivor[Cannot_Activate_Weapons]', 1, false, ['class' => 'form-control']) !!}
									@endif
								</td>
								<td>
									@if($survivor->Cannot_Activate_2H_Weapons == 1)
										{!! Form::checkbox('Survivor[Cannot_Activate_2H_Weapons]', 1, true, ['class' => 'form-control']) !!}
									@else
										{!! Form::checkbox('Survivor[Cannot_Activate_2H_Weapons]', 1, false, ['class' => 'form-control']) !!}
									@endif
								</td>
								<td>
									@if($survivor->Cannot_Activate_Plus2_Str_Gear == 1)
										{!! Form::checkbox('Survivor[Cannot_Activate_Plus2_Str_Gear]', 1, true, ['class' => 'form-control']) !!}
									@else
										{!! Form::checkbox('Survivor[Cannot_Activate_Plus2_Str_Gear]', 1, false, ['class' => 'form-control']) !!}
									@endif
								</td>
								<td>
									@if($survivor->Cannot_Consume == 1)
										{!! Form::checkbox('Survivor[Cannot_Consume]', 1, true, ['class' => 'form-control']) !!}
									@else
										{!! Form::checkbox('Survivor[Cannot_Consume]', 1, false, ['class' => 'form-control']) !!}
									@endif
									
								</td>
								<td>
									@if($survivor->No_Intimacy == 1)
										{!! Form::checkbox('Survivor[No_Intimacy]', 1, true, ['class' => 'form-control']) !!}
									@else
										{!! Form::checkbox('Survivor[No_Intimacy]', 1, false, ['class' => 'form-control']) !!}
									@endif
								</td>
								<td>
									@if($survivor->Skip_Hunt == 1)
										{!! Form::checkbox('Survivor[Skip_Hunt]', 1, true, ['class' => 'form-control']) !!}
									@else
										{!! Form::checkbox('Survivor[Skip_Hunt]', 1, false, ['class' => 'form-control']) !!}
									@endif
								</td>
								<td>
									@if($survivor->Cannot_Gain_Survival == 1)
										{!! Form::checkbox('Survivor[Cannot_Gain_Survival]', 1, true, ['class' => 'form-control']) !!}
									@else
										{!! Form::checkbox('Survivor[Cannot_Gain_Survival]', 1, false, ['class' => 'form-control']) !!}
									@endif
								</td>
								<td>
									@if($survivor->Cannot_Use_Survival == 1)
										{!! Form::checkbox('Survivor[Cannot_Use_Survival]', 1, true, ['class' => 'form-control']) !!}
									@else
										{!! Form::checkbox('Survivor[Cannot_Use_Survival]', 1, false, ['class' => 'form-control']) !!}
									@endif
								</td>
								<td>
									@if($survivor->Cannot_Use_Abilities == 1)
										{!! Form::checkbox('Survivor[Cannot_Use_Abilities]', 1, true, ['class' => 'form-control']) !!}
									@else
										{!! Form::checkbox('Survivor[Cannot_Use_Abilities]', 1, false, ['class' => 'form-control']) !!}
									@endif
								</td>
							</tr>
						</table>	
					</div>
				</div>
				<div id="currentImpairments">
					<table>
						<tr>
							<td>
								Name
							</td>
							<td>
								Description
							</td>
							<td>
							</td>
						</tr>
						@foreach ($survivor->impairments() as $i)
							<tr>
								<td>	
									{{ $i->Name }}
								</td>
								<td style="wordwrap:normal;">
									{!! $i->Description !!}
								</td>
								<td>
									<input type="button"  value="Remove" onclick="removeImpairment({{ $i->ImpairmentID}})"/>
								</td>
							</tr>
						@endforeach
						<tr>
							<td>
								<input type="text" id="newImpairmentName"/>
							</td>
							<td>
								<textarea id="newImpairmentDescription"></textarea>
							</td>
							<td>
								<input type="button"  value="New Impairment" onclick="newImpairment()"/>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<hr/>
			<div class="notes">
				<div class="top">
					<div class="heading">
						<h3>Notes</h3>
					</div>
				</div>
				<textarea disabled id="survivorNotes" rows="10" cols="100" maxlength="65534">{{ $survivor->Other_Notes }}</textarea>
				<input type="button" id="saveNotes" value="Edit Notes" onclick="editNotes(); return false;"/>
			</div>
		</div>
	{!! Form::close() !!}
</div>
@endsection
