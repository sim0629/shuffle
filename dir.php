<?
// HEADER DEFINITION
$current_encoding = 'UTF-8';
header("Content-type: text/html; charset=$current_encoding");


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

{ // listing directory
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

	$s .= '<div data-role="page" id="directory">';
	$s .= "<div data-role='header'><h2>Directory</h2></div>\n";
	if( count($dirs) > 0 ) {
		//$s = $s.$menu;
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
			$s = $s." <a class=\"dir-name\" rel=\"externel\" href=\"dir.php?l=$l\">".urldecode($f)."</a>";
			//$s = $s." <a class=\"open-external\" onclick=\"refresh_player('$phpself?d=$f&l=$ll');return false;\">N</a></li>\n";
		}
		$s = $s."</ul>\n";
	} else {
		$s .= "No Data";
	}
	$s .= '</div>';

	include('view/dir.php');
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

function isAcceptable( $ext ) {
	$HTML5 = (ereg( '(Chrome|Safari)', $_SERVER['HTTP_USER_AGENT'] ));
	if( $HTML5 )
		return $ext == 'mp3' or $ext == 'ogg';
	else
		return $ext == 'mp3';
}

?>
