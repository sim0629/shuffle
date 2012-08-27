<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width" />
	<meta http-equiv="Content-type" content="text/html; charset=<?echo $current_encoding;?>" />
	<title>Shuffle!</title>
	<script type="text/javascript">
	<? sajax_show_javascript(); ?>

	function refresh_player(path) {
		if( parent.player )
			parent.player.location.href = path;
		else {
			window.open(path, 'player', 'width=400,height=420,resizable=no');
        }
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
	</script>
	<script type="text/javascript" src="script/keyDownHandler.js"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js"></script>
	<link href="css/bootstrap.css" type="text/css" rel="stylesheet" />
    <style>
    body { padding-top: 45px; }
    </style>
</head>

<body>
<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container-fluid">
            <div class="brand">Shuffle!</div>
            <ul class="nav pull-right">
                <li><a class="menu-all">All</a></li>
                <li><a class="menu-current">Current</a></li>
                <li><a class="menu-selected">Selected</a></li>
            </ul>
        </div>
    </div>
</div>

<div class="container-fluid">
<ul class="breadcrumb">
<?php echo $generated_path ?>
</ul>

<form method="post" id="listing">
    <input type="hidden" value="<?=$mp3root?>" name="root" />
    <input type="hidden" value="<?=$encoded_current_location?>" name="currentdir" />

    <?=$directory_section?>
    <?=$file_section?>

    <div>
    </div>
</form>

</div>

<script>
$(function() {
    var current_location = '<?php echo str_replace("'", "\\'", $current_location); ?>';

    $('.menu-all').click(function(e) {
        post('playAll', current_location);
        e.preventDefault();
    });

    $('.menu-current').click(function(e) {
        post('playCurrent', current_location);
        e.preventDefault();
    });

    $('.menu-selected').click(function(e) {
        post('playSelected', current_location);
        e.preventDefault();
    });

    $('a').css('cursor', 'pointer');
});
</script>
</body>
</html>
