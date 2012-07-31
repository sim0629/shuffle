<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function encode_multibyte($str)
{
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

function convert_to($in_str, $ch='%')
{
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

function generate_path($path)
{
    $acc_path = array();
    $path = stripslashes($path);
    $acc_path[] = "<a href=\"".site_url('list/')."\">{$_SERVER["SERVER_NAME"]}</a>";
    if( $path != "" ) {
        $array = explode("/", $path);
        $acc = array();
        foreach( $array as $current_path ) {
            $acc[] = $current_path;
            $full_path = url_encode(implode("|", $acc));
            $acc_path[] = "<a href=\"".site_url('list')."/$full_path\">$current_path</a>";
        }
    }
    return implode("/", $acc_path);
}

function is_acceptable($ext)
{
    $HTML5 = (preg_match( '/(Chrome|Safari)/', $_SERVER['HTTP_USER_AGENT'] ));
    $ext = strtolower($ext);
    if( $HTML5 ) return $ext == 'mp3' || $ext == 'ogg';
    else return $ext == 'mp3';
}

function url_encode($text)
{
    $translate = array('|'=>'/','&#40;'=>'(','&#41;'=>')');
    return rawurlencode(htmlspecialchars($text));
}

function url_decode($text)
{
    $translate = array('|'=>'/','&#40;'=>'(','&#41;'=>')');
    return strtr(htmlspecialchars_decode(rawurldecode($text)), $translate);
}

function escape_singlequote($text)
{
    return str_replace("'", "\\'", $text);
}

/* End of file shuffle.php */
/* Location: ./application/helpers/shuffle.php */
