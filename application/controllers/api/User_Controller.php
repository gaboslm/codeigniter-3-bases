<?php 

class User_Controller extends RestApi_Controller 
{
    function __construct()
    {
        parent::__construct();
        $this->load->library('api_auth');
        $this->load->helper(array('form', 'url')); 

        if($this->api_auth->isNotAuthenticated())
        {
            $err = array(
                'message'=>'unauthorised',
            );

            $this->response($err, 401);
        }
    }

    function user()
    {
        $userId = $this->api_auth->getUserId();

        $this->load->model('user_model');

        $profileData = $this->user_model->getProfile($userId);
        
        $response = array(
            'message'=>'Successfully',
            'data'=> $profileData
        );

        $this->response($response, 200);
    }

    function image(){

        $userId = $this->api_auth->getUserId();

        $config['upload_path']   = 'uploads/'; 
        $config['allowed_types'] = '*'; 
        $config['max_size']      = 10000;
        // $config['encrypt_name']  = TRUE;
        
        $this->upload->initialize($config);

        if (!$this->upload->do_upload('image')) {
            $response = array(
                'message' => "Image Couldn't be Uploaded.",
                'errors' => strip_tags($this->upload->display_errors()),
            );

            $this->response($response, 400);
        }else { 
            $updata = $this->upload->data();
            $imageRoute = $config['upload_path'].$updata['raw_name'].$updata['file_ext'];
            
            $this->load->model('user_model');
            $this->user_model->uploadImage($userId, $imageRoute);
            
            $response = array(
                'message' => 'Image Uploaded Successfully.',
                'image' => $imageRoute
            );

            $this->response($response, 200);
        } 

    }
}