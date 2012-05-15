<?
// HEADER DEFINITION
$current_encoding = 'UTF-8';
header("Content-type: text/html; charset=$current_encoding");
require_once("phplib/Sajax.php");


// CONSTANT DEFINITION
$url		= './';

$mp3url		= '/music/';
$mp3path	= '/home/hitel00000/Music/';

$swfurl		= $url.'swf/';
$swfobject	= $swfurl.'swfobject.js';
$swf_mp3	= $swfurl.'player.swf';

$mp3_width	= 400;
$mp3_height	= 400;
$s = "";


$sajax_request_type = "POST";
sajax_init();
sajax_export("onpost");
sajax_handle_client_request();


// IMPLEMENTATION
$filename	= 'listing.xml';
$loadfile	= 'listing.xml.php';
$shuf		= (empty($_GET['sh']))?'false':'true';
$listPlay	= (!empty($_GET['r']) or !empty($_GET['sh']) or !empty($_GET['d']));

if( !empty($_GET['d']) ) {
	if( $_GET['d'] == '..' ) {
		die("forbidden");
	}
	if( $_GET['l'] != "" ) {
		$mp3path = $mp3path.$_GET['l'].'/';
		$mp3url = $mp3url.$_GET['l'].'/';
	}
	$path = $mp3path.$_GET['d'];
	$url = $mp3url.$_GET['d'];
	$file_handle = fopen('/tmp/'.$filename, 'w') or die("Unable to create $filename");

	fwrite($file_handle, "<?xml version=\"1.0\"?>");
	fwrite($file_handle, "<playlist version=\"1\">\n <trackList>\n");
	listing($file_handle, $path, $url, $_GET['d']);
	fwrite($file_handle, " </trackList>\n</playlist>");

	fclose($file_handle);
}

if( !empty($_GET['mp3']) ) { // play mp3
	$s = stripslashes($_GET['mp3']);
	$mp3 = iconv($current_encoding, 'UTF-8//IGNORE', $mp3url.$_GET['mp3'].'.mp3');
	$s .= '<div id="mp3_div"><a href="http://www.macromedia.com/go/getflashplayer">Get the Flash Player</a> to see this player.</div>
		<script type="text/javascript" src="'.$swfobject.'"></script>
		<script type="text/javascript">
		var so = new SWFObject("'.$swf_mp3.'","mediaplayer","'.$mp3_width.'","'.$mp3_height.'","7");
	so.addVariable("width","'.$mp3_width.'");
	so.addVariable("height","20");
	so.addVariable("file","'.convert_to($mp3, "%").'");
	so.addVariable("autostart","true");
	so.addVariable("skin","simple.swf");
	so.addVariable("playlist","bottom");
	so.addVariable("playlistsize","380");
	so.write("mp3_div");
	</script>';
	include('swfplayer.html');
} else if( $listPlay ) {
	include('html5player.html');
} else { // listing directory
	$phpself = $_SERVER['PHP_SELF'];

	$mp3root = $mp3url;
	if($_GET['l'] != "") {
		$mp3url = $mp3url.$_GET['l'];
		$mp3path = $mp3path.$_GET['l'];
	}
	$mp3url = stripslashes($mp3url);
	$mp3path = stripslashes($mp3path);

	$menu = <<<MENU
	<div class="ui-grid-b">
		<div class="ui-block-a">
			<a href="#directory" data-role="button">Dir</a>
		</div>
		<div class="ui-block-b">
			<a href="#file" data-role="button">File</a>
		</div>
		<div class="ui-block-c">
			<a href="#path" data-role="button">Path</a>
		</div>
	</div>
MENU;

	$dir_handle = opendir(urldecode($mp3path)) or die("Unable to open dir, " . urldecode($mp3path));
	$dirs = array();
	$files = array();
	while( $f = readdir($dir_handle) ) {
		if( $f != '.' && $f != '..' && is_dir($mp3path.'/'.$f) ) {
			array_push($dirs, $f);
		} else if( is_file($mp3path.'/'.$f) ) {
			$pathinfo = pathinfo($f);
			if( isAcceptable(strtolower($pathinfo['extension'])) ) {
				array_push($files, $f);
			}
		}
	}
	closedir($dir_handle);

	sort($dirs);
	sort($files);

	if( count($files) > 0 ) {
		$s .= '<div data-role="page" id="file">';
		$s = $s."<div data-role='header'><h2>Files</h2></div>\n";
		$s = $s.$menu;
		$s = $s."<ul id=\"file-list\" data-role='listview'>\n";
		foreach( $files as $f ) {
			$pathinfo = pathinfo(urlencode($f));
			if( isAcceptable(strtolower($pathinfo['extension'])) ) {
				$filename = urldecode($pathinfo['filename']);
				if( $_GET['l'] != "" )
					$filepar = $_GET['l'].'/'.$filename;
				else
					$filepar = $filename;
				$filepar = urlencode(stripslashes($filepar));
				$filepar = str_replace('#', '%'.dechex(ord('#')), str_replace('&', '%26', $filepar));
				$s = $s." <li>";
				$s = $s." <a class=\"open-external\" onclick=\"refresh_player('$phpself?mp3=$filepar');return false;\">$filename</a>\n";
				$s = $s." <a onclick=\"parent.player.add('".urldecode($pathinfo['filename'])."','".convert_to($mp3url."/".$f)."');return false;\">A</a></li>\n";
			}
		}
		$s = $s."</ul>\n";
		$s .= '</div>';
	}

	if( count($dirs) > 0 ) {
		$s .= '<div data-role="page" id="directory">';
		$s = $s."<div data-role='header'><h2>Directory</h2></div>\n";
		$s = $s.$menu;
		$s = $s."<ul id=\"dir-list\" data-role=\"listview\">\n";
		foreach( $dirs as $f ) {
			if( $_GET['l'] != "" )
				$l = $_GET['l'].'/'.$f;
			else
				$l = $f;
			$l = str_replace('#', '%'.dechex(ord('#')), str_replace('&', '%26', $l));
			$ll = $_GET['l'];
			$f = urlencode($f);
			$s = $s." <li>";
			$s = $s." <a class=\"dir-name\" rel=\"externel\" href=\"$phpself?l=$l\">".urldecode($f)."</a>";
			$s = $s." <a class=\"open-external\" onclick=\"refresh_player('$phpself?d=$f&l=$ll');return false;\">N</a></li>\n";
		}
		$s = $s."</ul>\n";
		$s .= '</div>';
	}

	$s .= '<div data-role="page" id="path">';
	$s .= '<div data-role="header"><h2>Path</h2></div>';
	$s = $s.$menu;
	$s = $s.generate_path($_GET['l'], $phpself)."\n";
	$s .= '</div>';

	include('view/mobile.php');
}

// FUNCTION DEFINITION
function convert_to($in_str, $ch = '%') {
	for ($i = 0; $i < strlen($in_str); $i++) {
		$int = ord($in_str[$i]);
		if ($int < 128) {
			$out_str = $out_str . chr($int);
		} else {
			$out_str = $out_str . $ch . dechex($int);
		}
	}
	return str_replace('#', '%'.dechex(ord('#')), str_replace('&', '&amp;', stripslashes($out_str)));
}

function decode($str) {
	return stripslashes(urldecode($str));
}

class comp_mixed_dir {
	static private $path;
	static public function set($p) {
		self::$path = $p;
	}
	static public function mixed_dir($a, $b) {
		$a = self::$path . '/' . $a;
		$b = self::$path . '/' . $b;
		if(is_dir($a)) {
			if(is_dir($b)) {
				return ( $a < $b ) ? -1 : 1;
			} else {
				return -1;
			}
		} else {
			if(is_dir($b)) {
				return 1;
			} else {
				return ( $a < $b ) ? -1 : 1;
			}
		}
	}
}

function listing($file_handle, $path, $url, $rel, $prune = false) {
	$path = stripslashes($path);
	$dir_handle = opendir($path) or die("Unable to open $path");
	$files = array();

	while( $f = readdir($dir_handle) ) {
		array_push($files, $f);
	}
	closedir($dir_handle);
	comp_mixed_dir::set($path);
	usort($files, array("comp_mixed_dir", "mixed_dir"));

	foreach( $files as $file ) {
		if( $rel != '' && $rel[strlen($rel)-1] != '/' ) $rel = $rel.'/';
		if( !$prune && $file != '.' && $file != '..' && is_dir($path.'/'.$file) ) {
			listing($file_handle, $path.'/'.$file, $url.'/'.$file, $rel.$file);
		}
		$pathinfo = pathinfo(urlencode($file));
		if( isAcceptable(strtolower($pathinfo['extension'])) ) {
			fwrite($file_handle, "  <track>\n   <title>".str_replace('#', '%'.dechex(ord('#')), str_replace('&', '&amp;', urldecode($pathinfo['filename'])))."</title>\n   <location>".convert_to($url.'/'.$file)."</location>\n  </track>\n");
		}
	}
}

function onpost($r, $rootdir, $curdir, $arr, $l) {
	$filename = 'listing.xml';

	global $mp3url, $mp3path;
	$mp3url = $mp3url.$curdir;
	$mp3path = $mp3path.$curdir;

	if( $r == 'playAll' ) {
		$file_handle = fopen('/tmp/'.$filename, 'w') or die("Unable to create $filename");

		fwrite($file_handle, "<?xml version=\"1.0\"?>");
		fwrite($file_handle, "<playlist version=\"1\">\n <trackList>\n");
		listing($file_handle, $mp3path, $mp3url, $l);
		fwrite($file_handle, " </trackList>\n</playlist>");

		fclose($file_handle);
	} else if( $r == 'playCurrent' ) {
		$file_handle = fopen('/tmp/'.$filename, 'w') or die("Unable to create $filename");

		fwrite($file_handle, "<?xml version=\"1.0\"?>");
		fwrite($file_handle, "<playlist version=\"1\">\n <trackList>\n");
		listing($file_handle, $mp3path, $mp3url, $l, true);
		fwrite($file_handle, " </trackList>\n</playlist>");

		fclose($file_handle);
	} else if( $r == 'playSelected' ) {
		$arr = split('\*', $arr);
		$file_handle = fopen('/tmp/'.$filename, 'w') or die("Unable to create $filename");

		fwrite($file_handle, "<?xml version=\"1.0\"?>");
		fwrite($file_handle, "<playlist version=\"1\">\n <trackList>\n");
		foreach( $arr as $idx => $key ) {
			if( is_string($key) and $key[1] == ':' ) {
				$v = urldecode(substr($key, 2));
				if( $key[0] == 'D' ) {
					listing($file_handle, $mp3path.'/'.$v, $mp3url.'/'.$v, $v);
				} else if( $key[0] == 'F' ) {
					fwrite($file_handle, "  <track>\n   <title>$v</title>\n   <location>".convert_to($rootdir.$v).".mp3</location>\n  </track>\n");
				}
			}
		}
		fwrite($file_handle, " </trackList>\n</playlist>");

		fclose($file_handle);
	}
	return $_SERVER['PHP_SELF'].'?r=1';
}

function generate_path($path, $phpself) {
	$s = array();
	$path = stripslashes($path);
	array_push($s, "<a rel=\"externel\" href=\"m.php?l=\">{$_SERVER["SERVER_NAME"]}</a>");
	if( $path != "" ) {
		$array = explode("/", $path);
		$acc = array();
		foreach( $array as $elem ) {
			array_push($acc, $elem);
			$ac = convert_to(implode("/", $acc));
			array_push($s, "<a rel=\"externel\" href=\"m.php?l=$ac\">$elem</a>");
		}
	}
	function wrap($a) { return "<li>$a</li>"; }
	return '<ul data-role="listview">' . implode("", array_map("wrap", $s)) . '</ul>';
}

function isAcceptable( $ext ) {
	$HTML5 = (ereg( '(Chrome|Safari)', $_SERVER['HTTP_USER_AGENT'] ));
	if( $HTML5 )
		return $ext == 'mp3' or $ext == 'ogg';
	else
		return $ext == 'mp3';
}

?>