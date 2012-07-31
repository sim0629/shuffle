<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$shuffle['music_url'] = "/music/";
$shuffle['music_local_path'] = "/home/hitel00000/Music/";

$shuffle['listing_filename'] = "listing_{$_SERVER['REMOTE_ADDR']}.xml";
$shuffle['listing_local_path'] = "/tmp/" . $shuffle['listing_filename'];
$shuffle['listing_url'] = $shuffle['listing_filename'];

$shuffle['playlist_folder'] = "playlist/{$_SERVER['REMOTE_ADDR']}/";

$shuffle['mp3_width'] = 400;
$shuffle['mp3_height'] = 400;

$config['shuffle'] = $shuffle;

/* End of file shuffle.php */
/* Location: ./application/config/shuffle.php */
