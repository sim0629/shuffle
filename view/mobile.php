<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=<?echo $current_encoding;?>" />
	<title>Shuffle!</title>
	<link rel="stylesheet" href="/jquery-mobile/compiled/jquery.mobile-1.0b1pre.min.css" />
	<script src="/jquery-mobile/js/jquery.js"></script>
	<script src="/jquery-mobile/compiled/jquery.mobile-1.0b1pre.min.js"></script>
</head>

<body>
<? echo $s; ?>
	<script type="text/javascript">
	function refresh_player(path) {
		if( parent.player )
			parent.player.location.href = path;
		else
			window.open(path, 'w', 'width=400,height=420,resizable=no');
	}
	function post(r, li) {
		var l = document.getElementById('listing');
		var a = new Array();
		var idx = 0;
		for( i=0; i<l.length; i++ ) {
			if( l[i].checked ) {
				a[idx++] = l[i].name;
			}
		}
		sajax_request_type = "POST";
		x_onpost(r, l.root.value, l.currentdir.value, a.join('*'), li, refresh_player);
	}

	window.onload = function() {
		var a = document.getElementsByTagName("a");
		for( var i in a ) {
			if( a[i].style )
				a[i].style.cursor = 'pointer';
		}
	};
	</script>
	<script type="text/javascript" src="script/keyDownHandler.js"></script>
</body>
</html>
