@extends('master')
@section('title', 'New Survivor') 
@section('content')
<style>
	#form { overflow-y: auto; width: 100%; }
	.survivors { margin-top: 30px; }
	.survivor { border: 1px solid rgb(170,170,170); min-width: calc(25% - 32px); max-width: calc(25% - 32px); width: calc(25% - 32px); margin: 10px; float: left; text-decoration: none; color: black; padding: 3px 5px; }
	.survivor div { margin: 5px; }
	.gender > select.small { width: 75px; }
	.info { overflow-y: auto; width: 90%; margin: 0 auto;}
	.info .section { float: left; min-width: calc(25% - 20px); max-width: calc(25% - 20px); width: calc(25% - 20px); text-decoration: none; color: black; margin: 3px 5px; text-align: center;}
</style>
<script>
	function addSurvivor() {
		var survivors = document.getElementsByClassName("survivor");
		//Create new survivor div
		var newDiv = document.createElement('div');
		newDiv.className = 'survivor';
		newDiv.innerHTML = '<input type="button" value="Remove" onclick="removeSurvivor(this)" style="float: right;" /> <div class="name"> {!! Form::label("Name", "Name") !!} {!! Form::text("Survivor[][Name]", "", ["class" => "form-control"]) !!} </div> <div class="gender"> {!! Form::label("Gender", "Gender") !!} {!! Form::select("Survivor[0][Gender]", array("1" => "Male", "0" => "Female"), "", array("class" => "small")) !!} </div><div class="saviour">{!! Form::label("Saviour", "Saviour") !!} {!! Form::checkbox("Survivor[][Saviour]") !!}</div>';
		document.getElementById('form').appendChild(newDiv);
		
		//Set name on the fields
		survivors = document.getElementsByClassName("survivor");
		var name = "Survivor[".concat(survivors.length).concat("][Name]");
		var gender = "Survivor[".concat(survivors.length).concat("][Gender]");
		var saviour = "Survivor[".concat(survivors.length).concat("][Saviour]");
		survivor = survivors[survivors.length-1];
		survivor.getElementsByTagName('input')[1].setAttribute("name", name);
		survivor.getElementsByTagName('select')[0].setAttribute("name", gender);
		survivor.getElementsByTagName('input')[2].setAttribute("name", saviour);
		
		var counter = document.getElementById("count");
		counter.innerHTML = survivors.length
	}
	
	function removeSurvivor(input) {
		var survivors = document.getElementsByClassName("survivor");
		if(survivors.length != 1) {
			document.getElementById('form').removeChild(input.parentNode);
			
			var counter = document.getElementById("count");
			counter.innerHTML = survivors.length;
		}
	}
</script>
<div class="container">
	{!! Form::model($settlement, ['action' => 'SurvivorController@addSurvivors']) !!}
		Father {!! Form::select('Father', [null=>''] + $males) !!}
		Mother {!! Form::select('Mother', [null=>''] + $females) !!}
		Set Surname {!! Form::text('Surname', '', ['class' => 'form-control']) !!}
		{!! Form::text('SettlementID', $settlement->SettlementID, ['hidden']) !!}
		<div class="survivors">
			<input type="button"  value="Add Additional Survivor" onclick="addSurvivor()" id="addBtn"/>
			<span id="count">1</span>
			<div id="form">
				<div class="survivor">
					<div class="name">
						{!! Form::label('Name', 'Name') !!}
						{!! Form::text('Survivor[0][Name]', '', ['class' => 'form-control']) !!}
					</div>
					<div class="gender">
						{!! Form::label('Gender', 'Gender') !!}
						{!! Form::select('Survivor[0][Gender]', array('1' => 'Male', '0' => 'Female'), '', array('class' => 'small')) !!}
					</div>
					<div class="saviour">
						{!! Form::label('Saviour', 'Saviour') !!}
						{!! Form::checkbox('Survivor[0][Saviour]') !!}
					</div>
				</div>
			</div>
			<div>
				<button class="btn btn-success" type="submit">Confirm</button>
			</div>
		</div>
	{!! Form::close() !!}
</div>
@endsection