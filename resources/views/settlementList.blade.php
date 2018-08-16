@extends('master')
@section('title', 'Settlements') 
@section('content')
<style>
	.top { overflow-y: auto; }
	.heading { overflow: hidden; width: 100%; }
	.heading h3 { font-weight: bold; font-size: 40px; margin: 20px 0px 0px 20px; float: left; }
	.newSettlement { float: right; margin: 20px 20px 0px 0px; }
	
	.settlementList { width: 100%; overflow-y: auto; }
	.settlementOuter { border: 1px solid rgb(170,170,170); min-width: calc(25% - 32px); max-width: calc(25% - 32px); width: calc(25% - 32px); margin: 10px; float: left; text-decoration: none; color: black; padding: 3px 5px;}
	.settlementHeader { overflow: hidden; width: 100%; text-align: center;  }
	.settlementName { font-weight: bold; font-size: 20px; }
	.population { float: right; }
	.settlementSurvival { float: left; }
	.survivalHeading { text-align: center; }
	.info { overflow-y: auto; }
	.settlementYear { float: right; }
	.survivors { overflow-y: auto; margin-top: 5px; }
	.survivor { float: left; width: calc(50% - 10px); border: 1px solid rgb(100,100,100); margin-left: 3px; margin-bottom: 3px; padding: 2px;}
	.survivorInfo { overflow-y: auto; width: 100%; }
	.survivorName { float: left; }
	.survivorXP { float: right; }
	.survivorStats { overflow-y: auto; margin-top: 3px; }
	.statHeaders, .stats { overflow-y: auto; }
	.statHeaders span { font-size: 10px; }
	.statHeaders span, .stats span { float: left; width: calc(100% / 6);  text-align: center; }
</style>
<div class="container">
	<div class="top">
		<div class="heading">
			<h3>Settlements</h3>
			<div class="newSettlement">
				<span class="button">
					<a href='/newgame'>New Game</a>
				</span>
			</div>
		</div>
	</div>
	<div class="settlementList">
		@foreach ($settlements as $settlement)
			<a href="{{ URL::asset('/settlement/'.$settlement->SettlementID) }}">
				<div class="settlementOuter">
					<div class="settlementHeader">
						<span class="settlementName">{{ $settlement->Settlement_Name }}</span>
						<span class="population">pop: {{ count($settlement->survivorsLiving) }}</span>
					</div>
					<div class="info">
						<div class="settlementSurvival">
							<div class="survivalHeading">Survival</div>
							<span class="departure">Dep {{ $settlement->Survival_Upon_Departure }}</span> /
							<span class="limit">{{ $settlement->Survival_Limit }} Lim</span>
						</div>
						<div class="settlementYear">
							<span class="year">Lantern Year: {{ $settlement->Current_Lantern_Year }}</span>
						</div>
					</div>
					<div class="survivors">
						@foreach ($settlement->survivorsTopFour as $survivor)
							<div class="survivor">
								<div class="survivorInfo">
									<span class="survivorName">
									@if($survivor->Saviour)
										(S)&nbsp;
									@endif
									{{ $survivor->Name }} 
									@if($survivor->Gender)
										(M)
									@else
										(F)
									@endif
									</span>
									<span class="survivorXP">{{ $survivor->Hunt_XP}} XP</span>
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
										<span class="movement">{{ $survivor->Movement}}</span>
										<span class="accuracy">{{ $survivor->Accuracy}}</span>
										<span class="strength">{{ $survivor->Strength}}</span>
										<span class="evasion">{{ $survivor->Evasion}}</span>
										<span class="luck">{{ $survivor->Luck}}</span>
										<span class="speed">{{ $survivor->Speed}}</span>
									</div>
								</div>
							</div>
						@endforeach
					</div>
				</div>
			</a>
		@endforeach
	</div>
</div>
@endsection