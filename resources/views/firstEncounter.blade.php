@extends('master')
@section('title', 'Settlements') 
@section('content')
<style>
	.welcomeEncounter { overflow-y: auto; width: 90%; margin: 0 auto;}
	.welcomeEncounter .section { float: left; min-width: calc(33% - 30px); max-width: calc(33% - 30px); width: calc(33% - 30px); text-decoration: none; color: black; margin: 3px 5px; text-align: center;}
	.tutorial { text-align: center; margin-bottom: 5px; }
	.survivors { overflow-y: auto; margin-top: 5px; }
	.survivor { float: left; width: calc(50% - 13px); border: 3px solid rgb(100,100,100); margin-left: 3px; margin-bottom: 10px; padding: 2px;}
	.topHead { overflow-y: auto; }
	.topHead>:not(:last-child) { float: left; }
	.topHead>:last-child { float: right; width: auto; }
	.survivorInfo { overflow-y: auto; width: 100%; }
	.survivorName { float: left; font-weight: bold; font-size: 24px; }
	.survivorXP { float: right; }
	.survival { width: auto; height: auto; overflow-y: auto; }
	.survivalValue { float: left; }
	.survivalActions { float: right; }
	
	.formInput { border: 1px solid black; overflow-y: auto; }
	.field { display: inline-block; width: auto; margin: 0 auto; }
	.adjusters { float: right; margin: 0;}
	input[type=text][readonly] { border: none; text-align: center; width: 16px; }
	
	.survivorStats { overflow-y: auto; margin: 3px 5px 5px 5px; }	
	input[type=button] { display: block; }
	.statHeaders, .stats { overflow-y: auto; }
	.stats > span:not(:last-child), .injuries > span:not(:last-child) { margin-right: 3px; }
	.statHeaders span { font-size: 10px; }
	.statHeaders span, .stats span { float: left; width: calc(100% / 6 - 5px);  text-align: center; }
	
	.survivorInjuries { overflow-y: auto; margin: 3px 5px 5px 5px; }
	.injury { float: right; margin: 0; }
	.injuryHeaders, .injuries { overflow-y: auto; }
	.injuryHeaders span { font-size: 10px; }
	.injuryHeaders span, .injuries span { float: left; width: calc(100% / 6 - 5px);  text-align: center; }
	
	.heading { float: left; }
	.faad, .knowledge, .impairments, .abilities { margin: 3px 5px 5px 5px; border: 1px solid black; border-radius: 5px; overflow-y: auto; }
	.faad > div, .knowledge > div { float: left; width: calc(33% - 2px); }
	.fightingArts, .secretFightingArts, .courage, .understanding { border-right: 1px solid black; }
	
	.noAbilities { float: right; }
	.noAbilities table tr td, .specificImpairments table tr td { text-align: center; }
</style>
<script type="text/javascript">
	function add(input) {
		var container = input.parentNode.parentNode;
		var inputField = container.getElementsByTagName('input')[0];
		inputField.setAttribute('value', inputField.value++);
	}
	
	function subtract(input) {
		var container = input.parentNode.parentNode;
		var inputField = container.getElementsByTagName('input')[0];
		inputField.setAttribute('value', inputField.value--);
	}
	
	function toggleDead(input) {
		var COD = input.parentNode.parentNode.getElementsByTagName('td')[1].getElementsByTagName('input')[0];
		COD.disabled = !input.checked;
	}
	
	function newAbility(input) {
		var ab = input.parentNode.getElementsByTagName('table')[0];
		var newTR = document.createElement('tr');
		var numElems = ab.getElementsByTagName('tr').length - 1;
		newTR.className = 'newAb';
		newTR.innerHTML = '<td style="width: 150px;"><input style="width: 150px;" class="form-control" name="Survivor[Ability][').concat(numElems).concat('][Name]" value ="" type="text"/></td><td style="width: 85%;"><input style="width: 85%;" class="form-control" name="Survivor[Ability][').concat(numElems).concat('][Description]" value ="" type="text"/></td><td><input type="button"  value="Remove" onclick="removeAbility(this)"/></td>');
		ab.appendChild(newTR);
	}
	
	function removeAbility(input) {
		var td = input.parentNode;
		var tr = td.parentNode;
		var table = tr.parentNode;
		table.removeChild(tr);
	}
	
	function newImpairment(input) {
		var imp = input.parentNode.getElementsByTagName('table')[0];
		var newTR = document.createElement('tr');
		var numElems = imp.getElementsByTagName('tr').length - 1;
		newTR.className = 'newImp';
		newTR.innerHTML = '<td style="width: 150px;"><input style="width: 150px;" class="form-control" name="Survivor[Impairment][').concat(numElems).concat('][Name]" value ="" type="text"/></td><td style="width: 85%;"><input style="width: 85%;" class="form-control" name="Survivor[Impairment][').concat(numElems).concat('][Description]" value ="" type="text"/></td><td><input type="button"  value="Remove" onclick="removeImpairment(this)"/></td>');
		imp.appendChild(newTR);
	}
	
	function removeImpairment(input) {
		var td = input.parentNode;
		var tr = td.parentNode;
		var table = tr.parentNode;
		table.removeChild(tr);
	}
</script>
<div class="container">
	<div class="welcomeEncounter">
		<div class="section">
			<p>
				Welcome to your first encounter. Here you will see the stats for each survivor.
			</p>
		</div>
		<div class="section">
			<p>
				 Any time you suffer a permanent decrease to a stat, or earn a permanent increase to a stat, record it here.
			</p>
		</div>
		<div class="section">
			<p>
				Any temporary changes, mark in your play area with one of the temporary tokens provided by the game.
			</p>
		</div>
	</div>
	<div class="tutorial">
		Start by filling in the <strong>armour</strong> value for each survivor. For the first encounter, this should be <strong>1 armour in the waist</strong>, and <strong>0 in all other locations.</strong>
	</div>
	{!! Form::model($survivors, ['action' => 'NewGameController@endencounter']) !!}
		<div>
			@foreach ($survivors as $indexKey => $survivor)
				<div class="survivor">
					{!! Form::text('Survivor['.$indexKey.'][ID]', $survivor->SurvivorID, ['class' => 'form-control', 'style' => 'display:none']) !!}
					<div class="topHead">
						<div class="survivorInfo">
							<span class="survivorName">
							@if($survivor->Name != null)
								{{ $survivor->Name }} 
							@else
								{!! Form::text('Survivor['.$indexKey.'][Name]', '',  ['class' => 'form-control']) !!}	
							@endif
							@if($survivor->Gender)
								(M)
							@else
								(F)
							@endif
							</span>
							<span class="survivorXP">{{ $survivor->Hunt_XP }} XP</span>
						</div>
						<div class="survivorDead">
							<table>
								<tr>
									<td> Dead </td>
									<td> Cause of Death </td>
								</tr>
								<tr>
									<td>
										{!! Form::checkbox('Survivor['.$indexKey.'][Dead]', 1, false, ['class' => 'form-control', 'onclick' => 'toggleDead(this)']) !!}
									</td>
									<td>
										{!! Form::text('Survivor['.$indexKey.'][CauseOfDeath]', '',  ['class' => 'form-control', 'disabled']) !!}
									</td>
								</tr>
							</table>
						</div>
						<div class="survival">
							<div class="survivalValue">
								<div>
									Survival
								</div>
								<div class="survivorSurvival formInput">
									<div class="field">
										{!! Form::text('Survivor['.$indexKey.'][Survival]', $survivor->Survival, ['class' => 'form-control', 'readonly']) !!}
									</div>
									<div class="adjusters">
										<input type="button"  value="+" onclick="add(this)"/>
										<input type="button"  value="-" onclick="subtract(this)"/>
									</div>
								</div>
							</div>
							<div class="survivalActions">
								<ul>
									<li class="@if($survivor->Dodge) on @else off @endif">Dodge</li>
									<li class="@if($survivor->Encourage) on @else off @endif">Encourage</li>
									<li class="@if($survivor->Surge) on @else off @endif">Surge</li>
									<li class="@if($survivor->Dash) on @else off @endif">Dash</li>
									<li class="@if($survivor->Endure) on @else off @endif">Endure</li>
								</ul>
							</div>
						</div>
					</div>
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
									{!! Form::text('Survivor['.$indexKey.'][Movement]', $survivor->Movement, ['class' => 'form-control', 'readonly']) !!}
								</div>
								<div class="adjusters">
									<input type="button"  value="+" onclick="add(this)"/>
									<input type="button"  value="-" onclick="subtract(this)"/>
								</div>
							</span>
							<span class="accuracy formInput">
								<div class="field">
									{!! Form::text('Survivor['.$indexKey.'][Accuracy]', $survivor->Accuracy, ['class' => 'form-control', 'readonly']) !!}
								</div>
								<div class="adjusters">
									<input type="button"  value="+" onclick="add(this)"/>
									<input type="button"  value="-" onclick="subtract(this)"/>
								</div>
							</span>
							<span class="strength formInput">
								<div class="field">
									{!! Form::text('Survivor['.$indexKey.'][Strength]', $survivor->Strength, ['class' => 'form-control', 'readonly']) !!}
								</div>
								<div class="adjusters">
									<input type="button"  value="+" onclick="add(this)"/>
									<input type="button"  value="-" onclick="subtract(this)"/>
								</div>
							</span>
							<span class="evasion formInput">
								<div class="field">
									{!! Form::text('Survivor['.$indexKey.'][Evasion]', $survivor->Evasion, ['class' => 'form-control', 'readonly']) !!}
								</div>
								<div class="adjusters">
									<input type="button"  value="+" onclick="add(this)"/>
									<input type="button"  value="-" onclick="subtract(this)"/>
								</div>
							</span>
							<span class="luck formInput">
								<div class="field">
									{!! Form::text('Survivor['.$indexKey.'][Luck]', $survivor->Luck, ['class' => 'form-control', 'readonly']) !!}
								</div>
								<div class="adjusters">
									<input type="button"  value="+" onclick="add(this)"/>
									<input type="button"  value="-" onclick="subtract(this)"/>
								</div>
							</span>
							<span class="speed formInput">
								<div class="field">
									{!! Form::text('Survivor['.$indexKey.'][Speed]', $survivor->Speed, ['class' => 'form-control', 'readonly']) !!}
								</div>
								<div class="adjusters">
									<input type="button"  value="+" onclick="add(this)"/>
									<input type="button"  value="-" onclick="subtract(this)"/>
								</div>
							</span>
						</div>
					</div>
					<div class="survivorInjuries">
						<div class="injuryHeaders">
							<span class="insanity">
								Insanity
							</span>
							<span class="head">
								Head
							</span>
							<span class="arms">
								Arms
							</span>
							<span class="body">
								Body
							</span>
							<span class="waist">
								Waist
							</span>
							<span class="legs">
								Legs
							</span>
						</div>
						<div class="injuries">
							<span class="insanity formInput">
								<div class="field">
									{!! Form::text('Survivor['.$indexKey.'][Insanity]', $survivor->Insanity, ['class' => 'form-control', 'readonly']) !!}
								</div>
								<div class="adjusters">
									<input type="button"  value="+" onclick="add(this)"/>
									<input type="button"  value="-" onclick="subtract(this)"/>
								</div>
							</span>
							<span class="head formInput">
								<div class="field">
									<div class="shield">{!! Form::text('Survivor['.$indexKey.'][HeadArmour]', 		$survivor->Head_Armour, ['class' => 'form-control', 'readonly']) !!}</div>
								</div>
								<div class="injury">
									L {!! Form::checkbox('Survivor['.$indexKey.'][HeadMajor]', 1, false, ['class' => 'form-control']) !!}
								</div>
								<div class="adjusters">
									<input type="button"  value="+" onclick="add(this)"/>
									<input type="button"  value="-" onclick="subtract(this)"/>
								</div>
							</span>
							<span class="arms formInput">
								<div class="field">
									<div class="shield">{!! Form::text('Survivor['.$indexKey.'][ArmArmour]', $survivor->Arm_Armour, ['class' => 'form-control', 'readonly']) !!}</div>
								</div>
								<div class="injury">
									<div>
										L {!! Form::checkbox('Survivor['.$indexKey.'][ArmsMinor]', 1, false, ['class' => 'form-control']) !!}
									</div>
									<div>
										H {!! Form::checkbox('Survivor['.$indexKey.'][ArmsMajor]', 1, false, ['class' => 'form-control']) !!}
									</div>
								</div>
								<div class="adjusters">
									<input type="button"  value="+" onclick="add(this)"/>
									<input type="button"  value="-" onclick="subtract(this)"/>
								</div>
							</span>
							<span class="body formInput">
								<div class="field">
									<div class="shield">{!! Form::text('Survivor['.$indexKey.'][BodyArmour]', $survivor->Body_Armour, ['class' => 'form-control', 'readonly']) !!}</div>
								</div>
								<div class="injury">
									<div>
										L {!! Form::checkbox('Survivor['.$indexKey.'][BodyMinor]', 1, false, ['class' => 'form-control']) !!}
									</div>
									<div>
										H {!! Form::checkbox('Survivor['.$indexKey.'][BodyMajor]', 1, false, ['class' => 'form-control']) !!}
									</div>
								</div>
								<div class="adjusters">
									<input type="button"  value="+" onclick="add(this)"/>
									<input type="button"  value="-" onclick="subtract(this)"/>
								</div>
							</span>
							<span class="waist formInput">
								<div class="field">
									<div class="shield">{!! Form::text('Survivor['.$indexKey.'][WaistArmour]', $survivor->Waist_Armour, ['class' => 'form-control', 'readonly']) !!}</div>
								</div>
								<div class="injury">
									<div>
										L {!! Form::checkbox('Survivor['.$indexKey.'][WaistMinor]', 1, false, ['class' => 'form-control']) !!}
									</div>
									<div>
										H {!! Form::checkbox('Survivor['.$indexKey.'][WaistMajor]', 1, false, ['class' => 'form-control']) !!}
									</div>
								</div>
								<div class="adjusters">
									<input type="button"  value="+" onclick="add(this)"/>
									<input type="button"  value="-" onclick="subtract(this)"/>
								</div>
							</span>
							<span class="legs formInput">
								<div class="field">
									<div class="shield">{!! Form::text('Survivor['.$indexKey.'][LegArmour]', $survivor->Leg_Armour, ['class' => 'form-control', 'readonly']) !!}</div>
								</div>
								<div class="injury">
									<div>
										L {!! Form::checkbox('Survivor['.$indexKey.'][LegsMinor]', 1, false, ['class' => 'form-control']) !!}
									</div>
									<div>
										H {!! Form::checkbox('Survivor['.$indexKey.'][LegsMajor]', 1, false, ['class' => 'form-control']) !!}
									</div>
								</div>
								<div class="adjusters">
									<input type="button"  value="+" onclick="add(this)"/>
									<input type="button"  value="-" onclick="subtract(this)"/>
								</div>
							</span>
						</div>
					</div>
					<div class="faad">
						<div class="fightingArts">
							<div class="heading">
								<h3>Fighting Arts</h3>
							</div>
							<div class="fa">
								@foreach ($survivor->fightingArts() as $indexKey => $fa)
									<p>{{ $fa->Name }}</p>
								@endforeach
							</div>
						</div>
						<div class="secretFightingArts">
							<div class="heading">
								<h3>Secret Fighting Arts</h3>
							</div>
							<div class="sfa">
								@foreach ($survivor->secretFightingArts() as $indexKey => $sfa)
									<p>{{ $sfa->Name }}</p>
								@endforeach
							</div>
						</div>
						<div class="disorders">
							<div class="heading">
								<h3>Disorders</h3>
							</div>
							<div class="dis">
								@foreach ($survivor->disorders() as $indexKey => $d)
									<p>{{ $d->Name }}</p>
								@endforeach
							</div>
						</div>
					</div>
					<div class="knowledge">
						<div class="courage">
							<div class="heading">
								<h3>Courage</h3>
							</div>
						</div>
						<div class="understanding">
							<div class="heading">
								<h3>Understanding</h3>
							</div>
						</div>
						<div class="weaponsProficiency">
							<div class="heading">
								<h3>Weapon Proficiency</h3>
							</div>
						</div>
					</div>
					<div class="abilities">
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
									{!! Form::checkbox('Survivor['.$indexKey.'][Cannot_Use_Abilities]', 1, false, ['class' => 'form-control']) !!}
									</td>
								</tr>
							</table>
						</div>
						<div class="newAbilities">
							<input type="button"  value="New Ability" onclick="newAbility(this)"/>
							<table>
								<thead>
									<td style="width: 150px;">
										Name
									</td>
									<td style="width: 85%;">
										Description
									</td>
									<td>
									</td>
								</tr>
							</table>
						</div>
						<div class="currentAbilities">
							@foreach ($survivor->abilities() as $indexKey => $a)
								<p>{{ $a->Description }}</p>
							@endforeach
						</div>
					</div>
					<div class="impairments">
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
								</tr>
								<tr>
									<td>
										{!! Form::checkbox('Survivor['.$indexKey.'][Cannot_Activate_Weapons]', 1, false, ['class' => 'form-control']) !!}
									</td>
									<td>
										{!! Form::checkbox('Survivor['.$indexKey.'][Cannot_Activate_2H_Weapons]', 1, false, ['class' => 'form-control']) !!}
									</td>
									<td>
										{!! Form::checkbox('Survivor['.$indexKey.'][Cannot_Activate_Plus2_Str_Gear]', 1, false, ['class' => 'form-control']) !!}
									</td>
									<td>
										{!! Form::checkbox('Survivor['.$indexKey.'][Cannot_Consume]', 1, false, ['class' => 'form-control']) !!}
									</td>
									<td>
										{!! Form::checkbox('Survivor['.$indexKey.'][No_Intimacy]', 1, false, ['class' => 'form-control']) !!}
									</td>
								</tr>
							</table>	
						</div>
						<div class="newDisorders">
							<!-- Have this info save into an array or something on click, with the option to remove a disorder. Disorders locked in at end of combat are saved to character -->
							{!! Form::select('Survivor['.$indexKey.'][Disorder]', $disorders) !!}
						</div>
						<div class="currentImpairments">
							@foreach ($survivor->impairments() as $indexKey => $i)
								<p>{{ $i->Description }}</p>
							@endforeach
						</div>
					</div>
				</div>
			@endforeach
		</div>
		<button class="btn btn-success" type="submit">Continue</button>
	{!! Form::close() !!}
</div>
@endsection