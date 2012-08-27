<?
// GET PARAMETERS
$current_location = empty($_GET['l'])?"":$_GET['l'];


if( preg_match('/\.\./', $current_location) || preg_match('/^\//', $current_location) ) {
    header('Location: http://www.google.com');
    exit;
}

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
sajax_export("on_post");
sajax_handle_client_request();


// IMPLEMENTATION
$filename	= LISTING_FILENAME;
$loadfile	= 'listing.xml.php';
$shuf		= (empty($_GET['sh']))?'false':'true';
$listPlay	= (!empty($_GET['r']) || !empty($_GET['sh']) || !empty($_GET['d']));
$supportMp3	= (ereg( '(Chrome|Safari)', $_SERVER['HTTP_USER_AGENT'] ));

if( !empty($_GET['d']) ) {
	if( $_GET['d'] == '..' ) {
		die("forbidden");
	}
	$mp3url = MUSIC_URL;
	$mp3path = MUSIC_LOCAL_PATH;
	if( !empty($current_location) ) {
		$mp3path = MUSIC_LOCAL_PATH.$current_location.'/';
		$mp3url = MUSIC_URL.$current_location.'/';
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
	//$mp3 = encode_multibyte(rawurlencode(iconv($current_encoding, 'UTF-8//IGNORE', MUSIC_URL.$_GET['mp3'].'.mp3')));
	$mp3 = convert_to(MUSIC_URL.$_GET['mp3'].'.mp3');
	$s .= <<<HTMLSTART
<div id="mp3_div"><a href="http://www.macromedia.com/go/getflashplayer">Get the Flash Player</a> to see this player.</div>
        <script src="/app/mediaplayer/jwplayer.js"></script>
		<script type="text/javascript">
        jwplayer("mp3_div").setup({
            flashplayer: "/app/mediaplayer/player.swf",
                file: '{$mp3}',
                width: {$mp3_width},
                events: {
                    onReady: function(){
                        jwplayer().play();
                    }
                }
            });
	</script>
HTMLSTART;
	include 'swfplayer.php';
} else if( $listPlay ) {
    $t = time();
	$s = <<<HTMLSTART
    <div id="mp3_div"><a href="http://www.macromedia.com/go/getflashplayer">Get the Flash Player</a> to see this player.</div>
    <script type="text/javascript" src="${swfobject}"></script>
    <script type="text/javascript">
        var so = new SWFObject("${swf_mp3}","mediaplayer","${mp3_width}","${mp3_height}","7");
        so.addVariable("repeat","always");
        so.addVariable("width","${mp3_width}");
        so.addVariable("height","${mp3_height}");
        so.addVariable("displayheight","0");
        so.addVariable("file","${loadfile}?${t}");
        so.addVariable("autostart","true");
        so.addVariable("shuffle","${shuf}");
        so.addVariable("skin","simple.swf");
        so.addVariable("playlist","bottom");
        so.addVariable("playlistsize","380");
        so.addVariable("lightcolor","cc0022");
        so.addVariable("backcolor","eeeeee");
        so.addVariable("frontcolor","888888");
        so.addVariable("dock","false");
        so.write("mp3_div");
    </script>
HTMLSTART;
	include $supportMp3?'html5player.html':'swfplayer.php';
} else { // listing directory
	$phpself = $_SERVER['PHP_SELF'];
	$mp3root = MUSIC_URL;
	$mp3url = MUSIC_URL.$current_location;
	$mp3path = MUSIC_LOCAL_PATH.$current_location;

	$mp3url = stripslashes($mp3url);
	$mp3path = stripslashes($mp3path);

	$generated_path = generate_path($current_location, $phpself);

	$dir_handle = opendir(urldecode($mp3path)) or die("Unable to open dir, " . urldecode($mp3path));
	$dirs = array();
	$files = array();
	while( $f = readdir($dir_handle) ) {
		if( $f != '.' && $f != '..' && is_dir($mp3path.'/'.$f) ) {
			array_push($dirs, $f);
		} else if( is_file($mp3path.'/'.$f) ) {
			$pathinfo = pathinfo($f);
			if( is_acceptable(strtolower($pathinfo['extension'])) ) {
				array_push($files, $f);
			}
		}
	}
	closedir($dir_handle);
	sort($dirs);
	sort($files);

    $directory_section = '';
	if( count($dirs) > 0 ) {
		$directory_section .= '<table class="table"><colgroup><col width="30px" /><col width="*" /><col width="30px" /></colgroup>';
		foreach( $dirs as $f ) {
			$l = empty($current_location)?$f:($current_location.'/'.$f);
			$l = str_replace('#', '%'.dechex(ord('#')), str_replace('&', '%26', $l));
			$ll = empty($current_location)?"":$current_location;
			$f = urlencode($f);

            $directory_section .= '<tr>';
			$directory_section .= "<td><input type=\"checkbox\" value=\"$f\" name=\"D:$f\" /></td>";
			$directory_section .= "<td><a class=\"dir-name\" href=\"$phpself?l=$l\">".urldecode($f)."</a></td>";
			$directory_section .= "<td><a class=\"open-external\" onclick=\"refresh_player('$phpself?d=$f&l=$ll');return false;\"><i class=\"icon-play\"></i></a></td>";
            $directory_section .= '</tr>';
		}
		$directory_section .= '</table>';
	}

    $file_section = '';
	if( count($files) > 0 ) {
		$file_section .= '<table class="table"><colgroup><col width="30px" /><col width="*" /><col width="30px" /><col width="30px" /></colgroup>';
		foreach( $files as $f ) {
			$pathinfo = pathinfo(urlencode($f));
			if( is_acceptable(strtolower($pathinfo['extension'])) ) {
				$filename = urldecode($pathinfo['filename']);
				$filepar = empty($current_location)?$filename:($current_location.'/'.$filename);
				$filepar = urlencode(stripslashes($filepar));
				$filepar = str_replace('#', '%'.dechex(ord('#')), str_replace('&', '%26', $filepar));
                $file_section .= '<tr>';
				$file_section .= "<td><input type=\"checkbox\" value=\"$filepar\" name=\"F:$filepar\" /></td>";
				$file_section .= "<td><a class=\"open-external\" onclick=\"refresh_player('$phpself?mp3=$filepar');return false;\">$filename</a></td>";
				$file_section .= "<td><a onclick=\"parent.player.add('".str_replace("'", "\\'", urldecode($pathinfo['filename']))."','".str_replace("'","\\'",convert_to($mp3url."/".$f))."');return false;\"><i class=\"icon-plus\"></i></a></td>";
				$file_section .= '<td><a href="'.convert_to($mp3url."/".$f).'"><i class="icon-download"></i></a></td>';
                $file_section .= '</tr>';
			}
		}
		$file_section .= '</table>';
	}

    $encoded_current_location = convert_to($current_location);

	include 'list.php';
}

// FUNCTION DEFINITION
function convert_to($in_str, $ch = '%') {
	$translate = array(
		'%' => '%' . dechex(ord('%')),
		'#' => '%' . dechex(ord('#')),
		'&' => '&amp;',
	);
	$in_str = strtr($in_str, $translate);

    $out_str = "";
	for ($i = 0; $i < strlen($in_str); $i++) {
		$int = ord($in_str[$i]);
		if ($int < 128) {
			$out_str = $out_str . chr($int);
		} else {
			$out_str = $out_str . $ch . dechex($int);
		}
	}
	return stripslashes($out_str);
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
		if( is_array($pathinfo) && !empty($pathinfo['extension']) && is_acceptable(strtolower($pathinfo['extension'])) ) {
			$translate = array(
				'&' => '&amp;',
				'#' => '%' . dechex(ord('#')),
			);
			fwrite($file_handle, "  <track>\n   <title>".strtr(urldecode($pathinfo['filename']), $translate)."</title>\n   <location>".convert_to($url.'/'.$file)."</location>\n  </track>\n");
		}
	}
}

function on_post($r, $rootdir, $curdir, $arr, $l) {
	$filename = LISTING_FILENAME;

    $curdir = rawurldecode($curdir);

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
			array_push($s, "<li><a href=\"$phpself?l=$ac\">$elem</a></li>");
		}
	}
	return implode('<li><span class="divider">/</span></li>', $s);
}

function is_acceptable( $ext ) {
	$HTML5 = (ereg( '(Chrome|Safari)', $_SERVER['HTTP_USER_AGENT'] ));
	if( $HTML5 )
		return $ext == 'mp3' || $ext == 'ogg';
	else
		return $ext == 'mp3';
}

function encode_multibyte($str) {
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

/* end of listing.php */
