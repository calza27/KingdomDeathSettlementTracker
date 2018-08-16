@extends('master')
@section('title', 'Submit') 
@section('content')
<style>
	#form { overflow-y: auto; width: 100%; }
	.settlement { margin-bottom: 10px; }
	.survivors { margin-top: 30px; }
	.survivor { border: 1px solid rgb(170,170,170); min-width: calc(25% - 32px); max-width: calc(25% - 32px); width: calc(25% - 32px); margin: 10px; float: left; text-decoration: none; color: black; padding: 3px 5px; }
	.survivor div { margin: 5px; }
	.gender > select.small { width: 75px; }
	.info { overflow-y: auto; width: 90%; margin: 0 auto;}
	.info .section { float: left; min-width: calc(25% - 20px); max-width: calc(25% - 20px); width: calc(25% - 20px); text-decoration: none; color: black; margin: 3px 5px; text-align: center;}
</style>
<script type="text/javascript">
	function addSurvivor() {
		var survivors = document.getElementsByClassName("survivor");
		if(survivors.length != 4) {
			//Create new survivor div
			var newDiv = document.createElement('div');
			newDiv.className = 'survivor';
			newDiv.innerHTML = '<input type="button" value="Remove" onclick="removeSurvivor(this)" style="float: right;" /> <div class="name"> {!! Form::label("Name", "Name") !!} {!! Form::text("Survivor[][Name]", "", ["class" => "form-control"]) !!} </div> <div class="gender"> {!! Form::label("Gender", "Gender") !!} {!! Form::select("Survivor[][Gender]", array("1" => "Male", "0" => "Female"), "", array("class" => "small")) !!} </div>';
			document.getElementById('form').appendChild(newDiv);
			
			//Set name on the fields
			survivors = document.getElementsByClassName("survivor");
			var name = "Survivor[".concat(survivors.length).concat("][Name]");
			var gender = "Survivor[".concat(survivors.length).concat("][Gender]");
			survivor = survivors[survivors.length-1];
			survivor.getElementsByTagName('input')[1].setAttribute("name", name);
			survivor.getElementsByTagName('select')[0].setAttribute("name", gender);
			
			var counter = document.getElementById("count");
			counter.innerHTML = "".concat(survivors.length).concat("/4");
			
			if(survivors.length == 4) {
				var button = document.getElementById("addBtn");
				button.disabled = true;
			}
		}
	}
	
	function removeSurvivor(input) {
		var survivors = document.getElementsByClassName("survivor");
		if(survivors.length != 1) {
			document.getElementById('form').removeChild(input.parentNode);
			
			var counter = document.getElementById("count");
			counter.innerHTML = "".concat(survivors.length).concat("/4");
			
			if(survivors.length != 4) {
				var button = document.getElementById("addBtn");
				button.disabled = false;
			}
		}
	}
</script>
<div class="container">
	<div class="info">
		<div class="section">
			<p>
				Create up to 4 survivors to begin the game with. You'll give them names, and pick their gender.
			</p>
		</div>
		<div class="section">
			<p>
				These survivors will form the basis of your settlement once you have completed the initial encounter with them.
			</p>
		</div>
		<div class="section">
			<p>
				You may leave a survivor's name blank, but keep note, a survivor will gain 1 survival point when they're given a name.
			</p>
		</div>
		<div class="section">
			<p>
				You'll also need to give your new settlement a name.
			</p>
		</div>
	</div>
	{!! Form::model($settlement, ['action' => 'SettlementController@store']) !!}
		<div class="settlement">
			<div>
				{!! Form::text('SettlementID', $settlement->SettlementID, ['hidden']) !!}
				{!! Form::label('Settlement_Name', 'Settlement Name') !!}
				{!! Form::text('Settlement_Name', '', ['class' => 'form-control']) !!}
			</div>
		</div>
		<div class="survivors">
			<input type="button"  value="Add Survivor" onclick="addSurvivor()" id="addBtn"/>
			<span id="count">1/4</span>
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
				</div>
			</div>
			<div>
				<button class="btn btn-success" type="submit">Submit</button>
			</div>
		</div>
	{!! Form::close() !!}
@endsection