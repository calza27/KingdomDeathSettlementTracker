@extends('master')
@section('title', 'Settlements') 
@section('content')
<style>
	.container { text-align: center;}
	.welcome { margin: 20px 0px; }
	.heading { text-transform: uppercase; font-weight: bold; font-size: 20px; }
	.center { width: 200px;  margin: 0 auto; }
	.newGame { float: left; }
	.settlementList { float: right; }
</style>
<div class="container">
	<div class="welcome">
		<span class="heading">Welcome to KDM settlement tracker!</span>
		<p>
			This app is deisgned to help you keep track of your settlement, surviviors, and acquired wares during a game of Kingdom Death Monster. Indiviudal games are managed by their respective settlement.
		</p>
		<p>
			Select one of the options below to get started!
		</p>
	</div>
	<div class="center">
		<div class="newGame">
			<span class="button">
				<a href='/newgame'>New Game</a>
			</span>
		</div>
		<div class="settlementList">
			<span class="button">
				<a href='/list'>View List</a>
			</span>
		</div>
	</div>
</div>
@endsection