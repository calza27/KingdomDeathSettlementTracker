@extends('master')
@section('title', 'Glossary') 
@section('content')
<style>
	.top { overflow-y: auto; }
	.heading { float: left; }
	.searchBox { float: right; margin-top: 10px; }
	input { width: 200px; }
	table { margin: 0 auto; }
	table, table tbody, table tbody tr { min-width: 100%; width: 100%; max-width: 100%; }
	table thead tr th, table tbody tr td { text-align: left; border: 1px solid rgb(140,140,140); padding-left: 5px; }
	table tbody tr { background-color: rgb(240,240,240); }
	table tbody tr:nth-child(2n) { background-color: rgb(200,200,200); }
	table tr>:first-child { min-width: 10%; width: 10%; max-width: 10%; }
	table thead tr th, table tbody tr td:first-child { font-weight: bold; }
	table tbody tr td:last-child { padding-right: 5px; }
</style>
<div class="container">
	@if(isset($g))
		<div class="top">
			<div class="heading">
				<h3>Glossary</h3>
			</div>
			<div class="searchBox">
				<input type="text" id="search" placeholder="Search..."/>
			</div>
		</div>
		<table id="gear">
			<tr>
				<th>
					Name
				</th>
				<th>
					Origin
				</th>
			</tr>
			@foreach ($g as $gear)
				<tr>
					<td>{{ $gear->Name }}</td>
					@if(isset($gear->origin()->first()->Name))
						<td>{{ $gear->origin()->first()->Name }}</td>
					@elseif(null !== $gear->origin()->first()->location()->first())
						<td>{{ $gear->origin()->first()->location()->first()->Name }}</td>
					@elseif(null !== $gear->origin()->first()->quary()->first())
						<td>{{ $gear->origin()->first()->quary()->first()->Name }}</td>
					@elseif(null !== $gear->origin()->first()->nemesis()->first())
						<td>{{ $gear->origin()->first()->nemesis()->first()->Name }}</td>
					@else
						<td>No Known Origin</td>
					@endif
				</tr>
			@endforeach
		</table>
	@elseif(isset($fa) || isset($sfa) || isset($d))
		<div class="top">
			<div class="heading">
				<h3>Fighting Arts and Disorders</h3>
			</div>
			<div class="searchBox">
				<input type="text" id="search" placeholder="Search..."/>
			</div>
		</div>
		@if(isset($fa))
			<h4>Fighting Arts</h4>
			<table id="fightingArts">
				<tr>
					<th>
						Name
					</th>
					<th>
						Description
					</th>
				</tr>
				@foreach ($fa as $fightingArt)
					<tr>
						<td>{{ $fightingArt->Name }}</td>
						<td>{!! $fightingArt->Description !!}</td>
					</tr>
				@endforeach
			</table>
		@endif
		@if(isset($sfa))
			<h4>Secret Fighting Arts</h4>
			<table id="secretFightingArts">
				<tr>
					<th>
						Name
					</th>
					<th>
						Description
					</th>
				</tr>
				@foreach ($sfa as $secretFightingArt)
					<tr>
						<td>{{ $secretFightingArt->Name }}</td>
						<td>{!! $secretFightingArt->Description !!}</td>
					</tr>
				@endforeach
			</table>
		@endif
		@if(isset($d))
			<h4>Disorders</h4>
			<table id="disorders">
				<tr>
					<th>
						Name
					</th>
					<th>
						Description
					</th>
				</tr>
				@foreach ($d as $disorder)
					<tr>
						<td>{{ $disorder->Name }}</td>
						<td>{!! $disorder->Description !!}</td>
					</tr>
				@endforeach
			</table>
		@endif
	@elseif(isset($n) || isset($q))
		<div class="top">
			<div class="heading">
				<h3>Quaries and Nemesis</h3>
			</div>
			<div class="searchBox">
				<input type="text" id="search" placeholder="Search..."/>
			</div>
		</div>
		@if(isset($q))
			<h4>Quaries</h4>
			<table id="quaries">
				<tr>
					<th>
						Name
					</th>
				</tr>
				@foreach ($q as $quary)
					<tr>
						<td>{{ $quary->Name }}</td>
					</tr>
				@endforeach
			</table>
		@endif
		@if(isset($n))
			<h4>Nemesis</h4>
			<table id="nemesis">
				<tr>
					<th>
						Name
					</th>
				</tr>
				@foreach ($n as $nemesis)
					<tr>
						<td>{{ $nemesis->Name }}</td>
					</tr>
				@endforeach
			</table>
		@endif
	@elseif(isset($glossary))
		<div class="top">
			<div class="heading">
				<h3>Glossary</h3>
			</div>
			<div class="searchBox">
				<input type="text" id="search" placeholder="Search..."/>
			</div>
		</div>
		<table id="glossary">
			<tr>
				<th>
					Name
				</th>
				<th>
					Description
				</th>
			</tr>
			@foreach ($glossary as $entry)
				<tr>
					<td>{{ $entry->Name }}</td>
					<td>{{ $entry->Description }}</td>
				</tr>
			@endforeach
		</table>
	@else
		DATABASE ERROR!!!!
	@endif
</div>
<script>
	$("#search").on("keyup", function() {
		var value = $(this).val();

		$("table tr").each(function(index) {
			if (index !== 0) {

				$row = $(this);

				var id = $row.find("td:first").text();

				if (id.indexOf(value) !== 0) {
					$row.hide();
				}
				else {
					$row.show();
				}
			}
		});
	});
</script>
@endsection
