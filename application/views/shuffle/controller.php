<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Shuffle!</title>
	<link href="<?php echo base_url('css/gen.css') ?>" type="text/css" rel="stylesheet" />
	<script type="text/javascript">
	function refresh_player(path) {
		if( parent.player )
			parent.player.location = path;
		else
			window.open(path, 'w', 'width=400,height=400,resizable=no');
	}

	function control(msg, s) {
		s = (typeof (s) != 'undefined') ? s : 'true';
		var p;
		if( parent.player ) 
			p = parent.player.document.getElementById('mediaplayer');
		else
			p = w.document.getElementById('mediaplayer');
		p.sendEvent(msg, s);
	}
	</script>
	<script type="text/javascript" src="<?php echo base_url('script/keyDownHandler.js') ?>"></script>
</head>

<body>
	<ul id="controller">
		<hr />
		<li><a onclick="control('prev');return false;" style="cursor:pointer;">Prev</a></li>
		<li><a onclick="control('play', '2');return false;" style="cursor:pointer;">Play/Pause</a></li>
		<li><a onclick="control('stop');return false;" style="cursor:pointer;">Stop</a></li>
		<li><a onclick="control('next');return false;" style="cursor:pointer;">Next</a></li>
		<hr />
		<li><a onclick="top.player.save();return false;" style="cursor:pointer;">리스트 저장</a></li>
		<li><a onclick="refresh_player('listing.php?r=1');return false;" style="cursor:pointer;">Resume</a></li>
		<li><a onclick="refresh_player('html5player.html');return false;" style="cursor:pointer;">HTML5</a></li>
		<hr />
	</ul>
</body>
</html>

