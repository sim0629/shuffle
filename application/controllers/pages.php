<?php
class Pages extends CI_Controller {
    public function frame()
    {
        $this->load->view('frame.php');
    }

    public function view($page = 'home')
    {
        if( ! file_exists('application/views/pages/'.$page.'.php') )
        {
            show_404();
        }

        $data['title'] = ucfirst($page);

        $this->load->helper('html');
        $this->load->view('templates/header.php', $data);
        $this->load->view('pages/'.$page.'.php', $data);
        $this->load->view('templates/footer.php', $data);
    }
}
