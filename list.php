<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=<?echo $current_encoding;?>" />
	<title>Shuffle!</title>
	<link href="css/gen.css" type="text/css" rel="stylesheet" />
	<script type="text/javascript">
	<? sajax_show_javascript(); ?>

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
		x_on_post(r, l.root.value, l.currentdir.value, a.join('*'), li, refresh_player);
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
</head>

<body>
<h1>Shuffle!</h1>

<h2>Path</h2>
<div id="current-path">
<?= $generated_path ?>
</div>

<form id="listing" method="post">
    <input type="hidden" value="<?=$mp3root?>" name="root" />
    <input type="hidden" value="<?=$current_location?>" name="currentdir" />

    <?=$directory_section?>
    <?=$file_section?>

    <div id="buttons">
        <input type="submit" name="playAll" onclick="post('playAll', '<?=$current_location?>');return false;" value="PlayAll" />
        <input type="submit" name="playCurrent" onclick="post('playCurrent', '<?=$current_location?>');return false;" value="PlayCurrent" />
        <input type="submit" name="playSelected" onclick="post('playSelected', '<?=$current_location?>');return false;" value="PlaySelected" />
    </div>
</form>
</body>
</html>