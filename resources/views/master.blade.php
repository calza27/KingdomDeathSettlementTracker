<html>
	<head>
		<meta name="_token" content="{!! csrf_token() !!}" /> 
		<title>@yield('title')</title>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
		<style>
			html { background-color: rgb(240,240,240); height: 100%; }
			body { width: 90%; background-color: #FFF; margin: 0 auto; overflow-y: auto; }
			.container { padding: 0px 20px 50px 20px; overflow-y: auto; }
			.titleHeader { max-height: 40px; text-align: center; }
			.titleHeader a { color: black; text-decoration: none; }
			nav { width: 100%; min-height: 50px; max-height: 50px; background-color: rgb(250,250,250); border-bottom: 1px solid rgb(245,245,245); margin-bottom: 5px; display: flex; }
			nav a { text-decoration: none; color: black;  float: left; width: calc(20%);  border: 1px solid rgb(240,240,240); text-align: center; line-height: 50px; }
			nav a:hover { background-color: rgb(240,240,240); }
			nav .navItem { min-height: 50px; max-height: 50px; font-weight: bold; font-size: 16px; }
			.button { background-image: linear-gradient(rgb(240, 240, 240) 0%, rgb(215, 215, 215) 100%); border-bottom: 1px solid rgb(123, 126, 128); border-left: 1px solid rgb(172, 172, 173); border-right: 1px solid rgb(172, 172, 173); border-top: 1px solid rgb(192, 192, 192); font-size: 17px; line-height: 34px; padding: 5px; }
			.button a { color: black; text-decoration: none; }
			select:not(.small) { width: 200px; }
			select.small { width: 50px; }
			div.shield { display: inline-block; line-height: 16px; height: 16px; width: 16px; clip-path: polygon(90% 10%, 90% 45%, 80% 80%, 50% 100%, 20% 80%, 10% 45%, 10% 10%); background-color: #000; color: #fff; text-align: center; }
			div.reaction { display: inline-block; line-height: 16px; height: 16px; width: 16px; clip-path: polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%); background-color: #000; color: #fff; text-align: center; }
			div.shield input { background: transparent; border: none; color: #fff; display: block; margin: 0 auto; text-align: center; width: 16px; line-height: 16px; }
			div.movement { display: inline-block; line-height: 16px; height: 16px; width: 16px; background-image: url("{{asset('movement.png')}}"); background-size: contain; }
			div.armWound { display: inline-block; line-height: 16px; height: 16px; width: 16px; background-image: url("{{asset('armWound.png')}}"); background-size: contain; }
			div.book { display: inline-block; line-height: 16px; height: 16px; width: 16px; background-image: url("{{asset('book.png')}}"); background-size: contain; }
			div.chestWound { display: inline-block; line-height: 16px; height: 16px; width: 16px; background-image: url("{{asset('chestWound.png')}}"); background-size: contain; }
			div.endeavour { display: inline-block; line-height: 16px; height: 16px; width: 16px; background-image: url("{{asset('endeavour.png')}}"); background-size: contain; }
			div.headWound { display: inline-block; line-height: 16px; height: 16px; width: 16px; background-image: url("{{asset('headWound.png')}}"); background-size: contain; }
			div.legWound { display: inline-block; line-height: 16px; height: 16px; width: 16px; background-image: url("{{asset('legWound.png')}}"); background-size: contain; }
			div.waistWound { display: inline-block; line-height: 16px; height: 16px; width: 16px; background-image: url("{{asset('waistWound.png')}}"); background-size: contain; }
			div.dead { display: inline-block; line-height: 16px; height: 16px; width: 16px; background-image: url("{{asset('dead.png')}}"); background-size: contain; }
			div.radioOn { display: inline-block; line-height: 16px; height: 16px; width: 16px; background-image: url("{{asset('radioOn.png')}}"); background-size: contain; }
			div.radioOff { display: inline-block; line-height: 16px; height: 16px; width: 16px; background-image: url("{{asset('radioOff.png')}}"); background-size: contain; }
			div.action { display: inline-block; line-height: 16px; height: 16px; width: 16px; background-image: url("{{asset('action.png')}}"); background-size: contain; }
			div.affinity { display: inline-block; line-height: 16px; height: 16px; width: 16px; text-align: center; }
			div.affinity.green { background: #0F0; color: #000; }
			div.affinity.red { background: #F00; color: #fff; }
			div.affinity.blue { background: #00F; color: #fff; }
			li { list-style-type: square; } 
			li.off { list-style-type: circle; } 
			li.on { list-style-type: disc; } 
		</style>
	</head>
	<body>
		<div class="titleHeader">
			<h1><a href="/">Kingdom Death Monster Settlement Tracker</a></h1>
		</div>
		@include('shared.navbar')
		@yield('content')
		<script type="text/javascript">
			$("select:not(.small)").select2();
		</script>
	</body>
</html>