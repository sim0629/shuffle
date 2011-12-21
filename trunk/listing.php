<?
// HEADER DEFINITION
$current_encoding = 'UTF-8';
header("Content-type: text/html; charset=$current_encoding");
require_once "phplib/Sajax.php";
require_once "config.php";


// CONSTANT DEFINITION
$url		= './';

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
$filename	= LISTING_FILENAME;
$loadfile	= 'listing.xml.php';
$shuf		= (empty($_GET['sh']))?'false':'true';
$listPlay	= (!empty($_GET['r']) or !empty($_GET['sh']) or !empty($_GET['d']));
$supportMp3	= (ereg( '(Chrome|Safari)', $_SERVER['HTTP_USER_AGENT'] ));

if( !empty($_GET['d']) ) {
	if( $_GET['d'] == '..' ) {
		die("forbidden");
	}
	$mp3url = MUSIC_URL;
	$mp3path = MUSIC_LOCAL_PATH;
	if( $_GET['l'] != "" ) {
		$mp3path = MUSIC_LOCAL_PATH.$_GET['l'].'/';
		$mp3url = MUSIC_URL.$_GET['l'].'/';
	}
	$path = $mp3path.$_GET['d'];
	$url = $mp3url.$_GET['d'];
	$file_handle = fopen(LISTING_LOCAL_PATH, 'w') or die("Unable to create $filename");

	fwrite($file_handle, "<?xml version=\"1.0\"?>");
	fwrite($file_handle, "<playlist version=\"1\">\n <trackList>\n");
	listing($file_handle, $path, $url, $_GET['d']);
	fwrite($file_handle, " </trackList>\n</playlist>");

	fclose($file_handle);
}

if( !empty($_GET['mp3']) ) { // play mp3
	$s = stripslashes($_GET['mp3']);
	//$mp3 = encodeMultibyte(rawurlencode(iconv($current_encoding, 'UTF-8//IGNORE', MUSIC_URL.$_GET['mp3'].'.mp3')));
	$mp3 = encodeMultibyte(iconv($current_encoding, 'UTF-8//IGNORE', MUSIC_URL.$_GET['mp3'].'.mp3'));
	$s .= <<<HTMLSTART
<div id="mp3_div"><a href="http://www.macromedia.com/go/getflashplayer">Get the Flash Player</a> to see this player.</div>
        <script src="/app/mediaplayer/jwplayer.js"></script>
		<script type="text/javascript">
        jwplayer("mp3_div").setup({
            flashplayer: "/app/mediaplayer/player.swf",
                file: '$mp3',
                width: $mp3_width,
                events: {
                    onReady: function(){
                        jwplayer().play();
                    }
                }
            });
	</script>
HTMLSTART;
	include('swfplayer.html');
} else if( $listPlay ) {
	$s = '<div id="mp3_div"><a href="http://www.macromedia.com/go/getflashplayer">Get the Flash Player</a> to see this player.</div>
		<script type="text/javascript" src="'.$swfobject.'"></script>
		<script type="text/javascript">
		var so = new SWFObject("'.$swf_mp3.'","mediaplayer","'.$mp3_width.'","'.$mp3_height.'","7");
	so.addVariable("repeat","always");
	so.addVariable("width","'.$mp3_width.'");
	so.addVariable("height","'.$mp3_height.'");
	so.addVariable("displayheight","0");
	so.addVariable("file","'.$loadfile.'?'.time().'");
	so.addVariable("autostart","true");
	so.addVariable("shuffle","'.$shuf.'");
	so.addVariable("skin","simple.swf");
	so.addVariable("playlist","bottom");
	so.addVariable("playlistsize","380");
	so.addVariable("lightcolor","cc0022");
	so.addVariable("backcolor","eeeeee");
	so.addVariable("frontcolor","888888");
	so.addVariable("dock","false");
	so.write("mp3_div");
	</script>';
	include($supportMp3?'html5player.html':'swfplayer.html');
} else { // listing directory
	$phpself = $_SERVER['PHP_SELF'];
	$s = "<h1>Shuffle!</h1>\n";
	$mp3root = MUSIC_URL;
	$mp3url = MUSIC_URL;
	$mp3path = MUSIC_LOCAL_PATH;
	if($_GET['l'] != "") {
		$mp3url = MUSIC_URL.$_GET['l'];
		$mp3path = MUSIC_LOCAL_PATH.$_GET['l'];
	}
	$mp3url = stripslashes($mp3url);
	$mp3path = stripslashes($mp3path);
	$s = $s."<h2>Path</h2>\n<div id=\"current-path\">\n";
	$s = $s.generate_path($_GET['l'], $phpself)."\n";
	$s = $s."</div>\n";
	$dir_handle = opendir(urldecode($mp3path)) or die("Unable to open dir, " . urldecode($mp3path));
	$s = $s."<form id=\"listing\" method=\"post\">\n";
	$s = $s."<input type=\"hidden\" value=\"$mp3root\" name=\"root\" />\n";
	$s = $s."<input type=\"hidden\" value=\"{$_GET['l']}\" name=\"currentdir\" />\n";
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
	if( count($dirs) > 0 ) {
		$s = $s."<h2>Directory</h2>\n";
		$s = $s."<ul id=\"dir-list\">\n";
		foreach( $dirs as $f ) {
			if( $_GET['l'] != "" )
				$l = $_GET['l'].'/'.$f;
			else
				$l = $f;
			$l = str_replace('#', '%'.dechex(ord('#')), str_replace('&', '%26', $l));
			$ll = $_GET['l'];
			$f = urlencode($f);
			$s = $s." <li><input type=\"checkbox\" value=\"$f\" name=\"D:$f\" />";
			$s = $s." <a class=\"dir-name\" href=\"$phpself?l=$l\">".urldecode($f)."</a>";
			$s = $s." <a class=\"open-external\" onclick=\"refresh_player('$phpself?d=$f&l=$ll');return false;\">N</a></li>\n";
		}
		$s = $s."</ul>\n";
	}
	if( count($files) > 0 ) {
		$s = $s."<h2>Files</h2>\n";
		$s = $s."<ul id=\"file-list\">\n";
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
				$s = $s." <li><input type=\"checkbox\" value=\"$filepar\" name=\"F:$filepar\" />";
				$s = $s." <a class=\"open-external\" onclick=\"refresh_player('$phpself?mp3=$filepar');return false;\">$filename</a>\n";
				$s = $s." <a onclick=\"parent.player.add('".str_replace("'", "\\'", urldecode($pathinfo['filename']))."','".str_replace("'","\\'",convert_to($mp3url."/".$f))."');return false;\">A</a>\n";
				$s = $s.' <a href="'.convert_to($mp3url."/".$f).'">D</a></li>'."\n";
			}
		}
		$s = $s."</ul>\n";
	}

	$l = $_GET['l'];
	$s = $s."<div id=\"buttons\">\n";
	$s = $s."<input type=\"submit\" name=\"playAll\" onclick=\"post('playAll', '$l');return false;\" value=\"PlayAll\"\" />\n";
	$s = $s."<input type=\"submit\" name=\"playCurrent\" onclick=\"post('playCurrent', '$l');return false;\" value=\"PlayCurrent\" />\n";
	$s = $s."<input type=\"submit\" name=\"playSelected\" onclick=\"post('playSelected', '$l');return false;\" value=\"PlaySelected\" />\n";
	$s = $s."</div>\n";
	$s = $s."</form>\n";
	include('swfplayer.html');
}

// FUNCTION DEFINITION
function convert_to($in_str, $ch = '%') {
    $out_str = "";
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

function filename($path) {
    $pos = strrpos($path, "/");
    if( $pos === FALSE )
        return $path;
    else {
        return substr($path, $pos+1);
    }
}

function filename_intval($path) {
    return intval(filename($path));
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
                if( filename_intval($a) != 0 && filename_intval($b) != 0 )
                {
                    if( filename_intval($a) != filename_intval($b) )
                        return ( filename_intval($a) < filename_intval($b) ) ? -1 : 1;
                }
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
	$filename = LISTING_FILENAME;

	$mp3url = MUSIC_URL.$curdir;
	$mp3path = MUSIC_LOCAL_PATH.$curdir;

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
	array_push($s, "<a href=\"$phpself\">{$_SERVER["SERVER_NAME"]}</a>");
	if( $path != "" ) {
		$array = explode("/", $path);
		$acc = array();
		foreach( $array as $elem ) {
			array_push($acc, $elem);
			$ac = convert_to(implode("/", $acc));
			array_push($s, "<a href=\"$phpself?l=$ac\">$elem</a>");
		}
	}
	return implode("/", $s);
}

function isAcceptable( $ext ) {
	$HTML5 = (ereg( '(Chrome|Safari)', $_SERVER['HTTP_USER_AGENT'] ));
	if( $HTML5 )
		return $ext == 'mp3' or $ext == 'ogg';
	else
		return $ext == 'mp3';
}

function encodeMultibyte($str) {
    $out = '';
    for($i=0;$i<strlen($str);$i++) {
        if( ord($str[$i]) >= 0x80 ) {
            $out .= ('%'.dechex(ord($str[$i])));
        } else {
            $out .= $str[$i];
        }
    }
    return $out;
}

?>
