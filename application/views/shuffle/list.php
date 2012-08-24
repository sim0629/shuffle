<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=<?php echo $current_encoding; ?>" />
	<title>Shuffle!</title>
	<script type="text/javascript">
	<!--? sajax_show_javascript(); ?-->

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
	<script type="text/javascript" src="<?php echo base_url('script/keyDownHandler.js') ?>"></script>
    <link type="text/css" rel="stylesheet" href="<?php echo base_url('css/bootstrap.css') ?>" />
</head>

<body>
<div class="container-fluid">
<div class="page-header">
    <h1>Shuffle!</h1>
</div>

<ul class="breadcrumb">
<?php echo $generated_path ?>
</ul>

<form method="post">
    <input type="hidden" value="<?php echo $mp3root ?>" name="root" />
    <input type="hidden" value="<?php echo $encoded_current_location ?>" name="currentdir" />

    <?php if(count($dirs) > 0): ?>
    <table class="table">
        <colgroup><col width="30px" /><col width="*" /><col width="30px" /></colgroup>
        <?php foreach($dirs as $filename): ?>
        <?php 
            $l = empty($current_location)?$filename:($current_location.'|'.$filename);
            $l = url_encode($l);
            $filename = url_encode($filename);
            // refresh_player('$phpself?d=$f&l=$current_location');return false;
        ?>
        <tr>
            <td><input type="checkbox" value="<?php echo $filename ?>" name="D:<?php echo $filename ?>" /></td>
            <td><a href="<?php echo site_url('list/'.$l) ?>"><?php echo url_decode($filename) ?></a></td>
            <td><a href="<?php echo site_url('play') ?>" target="player"><i class="icon-play"></i></a></td>
        </tr>
        <?php endforeach ?>
    </table>
    <?php endif ?>

    <?php if(count($files) > 0): ?>
    <table class="table">
        <colgroup><col width="30px" /><col width="*" /><col width="30px" /><col width="30px" /></colgroup>
        <?php foreach($files as $f): ?>
        <?php
            $pathinfo = pathinfo($f);
            if( !is_acceptable($pathinfo['extension']) ) continue;

            $filename = url_decode($pathinfo['filename']);
            $filepar = empty($current_location)?$filename:($current_location.'|'.$filename);
            $filepar = url_encode(stripslashes($filepar));
            $filepar = strtr($filepar, array('#'=>'%'.dechex(ord('#')), '&'=>'%26'));
        ?>
        <tr>
            <td><input type="checkbox" value="<?php echo $filepar ?>" name="F:<?php echo $filepar?>" /></td>
            <td><a onclick="refresh_player('<?php echo site_url('play/'.$filepar) ?>');return false;"><?php echo $filename ?></a></td>
            <td><a onclick="parent.player.add('<?php echo escape_singlequote(url_decode($pathinfo['filename'])) ?>','<?php echo escape_singlequote(convert_to($mp3url."/".$f)) ?>');return false;"><i class="icon-plus"></i></a></td>
            <td><a href="<?php echo convert_to($mp3url."/".$f) ?>"><i class="icon-download"></i></a></td>
        </tr>
        <?php endforeach ?>
    </table>
    <?php endif ?>

    <div>
        <input type="submit" name="playAll" onclick="post('playAll', '<?php echo $current_location ?>');return false;" value="PlayAll" />
        <input type="submit" name="playCurrent" onclick="post('playCurrent', '<?php echo $current_location ?>');return false;" value="PlayCurrent" />
        <input type="submit" name="playSelected" onclick="post('playSelected', '<?php echo $current_location ?>');return false;" value="PlaySelected" />
    </div>
</form>
</div>
</body>
</html>
