<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Shuffle extends CI_Controller {
	public function index($current_location = "")
	{
        $data = array();

        $data['current_encoding'] = 'UTF-8';
        $config = $this->config->item('shuffle');

        $current_location = url_decode($current_location);

        $phpself = $this->input->server('PHP_SELF');
        $mp3root = $config['music_url'];
        $mp3url = $config['music_url'].$current_location;
        $mp3path = $config['music_local_path'].$current_location;

        $mp3url = stripslashes($mp3url);
        $mp3path = stripslashes($mp3path);

        $data['mp3root'] = $mp3root;
        $data['mp3url'] = $mp3url;

        $data['generated_path'] = generate_path($current_location);

        $dir_handle = opendir($mp3path) or die("Unable to open dir, " . $mp3path);
        $dirs = array();
        $files = array();
        while( $f = readdir($dir_handle) ) {
            if( $f != '.' && $f != '..' && is_dir($mp3path.'/'.$f) ) {
                array_push($dirs, $f);
            } else if( is_file($mp3path.'/'.$f) ) {
                $pathinfo = pathinfo($f);
                if( is_acceptable($pathinfo['extension']) ) {
                    array_push($files, $f);
                }
            }
        }
        closedir($dir_handle);
        sort($dirs);
        sort($files);

        $data['dirs'] = $dirs;
        $data['files'] = $files;

        $encoded_current_location = encode_multibyte($current_location);
        $data['current_location'] = strtr($current_location, array('/'=>'|'));
        $data['encoded_current_location'] = $encoded_current_location;

		$this->load->view('shuffle/list', $data);
	}

    public function controller()
    {
        $data = array();
        $this->load->view('shuffle/controller', $data);
    }

    public function save()
    {
    }

    public function play($play = "")
    {
        $config = $this->config->item('shuffle');

        $play = strtr($play, array('|'=>'/'));
        $mp3 = encode_multibyte($config['music_url'].url_decode($play).'.mp3');

        $data = array(
            'title' => $mp3,
            'play' => $mp3,
            'mp3_width' => $config['mp3_width'],
        );

        $this->load->helper('html');
        $this->load->view('templates/header', $data);
        $this->load->view('shuffle/play', $data);
        $this->load->view('templates/footer', $data);
    }

    public function playlist()
    {
        $support_mp3 = (preg_match( '/(Chrome|Safari)/', $_SERVER['HTTP_USER_AGENT'] ));

        $data = array(
            'title' => 'playlist',
            'shuf' => 'true',
            'css' => (!$support_mp3),
        );
        $data = array_merge($data, $this->config->item('shuffle'));

        $this->load->helper('html');
        $this->load->view('templates/header', $data);
        $this->load->view($support_mp3?'shuffle/playlist_html5':'shuffle/playlist_swf', $data);
        $this->load->view('templates/footer', $data);
    }
}

/* End of file shuffle.php */
/* Location: ./application/controllers/shuffle.php */
