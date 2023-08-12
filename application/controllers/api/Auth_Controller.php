<?php 

class Auth_Controller extends RestApi_Controller 
{
    function __construct() 
    {
        parent::__construct();
        $this->load->library('api_auth');
        $this->load->model('user_model');
    }

    function register()
    {
        $username = $this->input->post('name');
        $email = $this->input->post('email');
        $password = $this->input->post('password');

        $this->form_validation->set_rules('name','Name','trim|required|alpha|max_length[50]');
        $this->form_validation->set_rules('email','Email','trim|required|valid_email|is_unique[users.email]');
        $this->form_validation->set_rules('password','Password','trim|required');
        
        if($this->form_validation->run())
        {
            $data  = array(
                'name'=>$username,
                'email'=>$email,
                'password'=>sha1($password),
            );

            $this->user_model->registerUser($data);

            $response = array(
                'message' => 'Successfully Registerd',
            );

            return $this->response($response, 201);
        }
        else 
        {
            $response = array(
                'message' => 'fill all the required fields',
                'data'=> explode("\n", strip_tags(validation_errors()))
            );

            return $this->response($response, 400);
        }
    }

    function login() 
    {
        
        $email = $this->input->post('email');
        $password = $this->input->post('password');
       
        $this->form_validation->set_rules('email','Email','required');
        $this->form_validation->set_rules('password','Pasword','required');

        if($this->form_validation->run())
        {
            $data = array('email'=>$email,'password'=> sha1($password));
            $loginStatus = $this->user_model->checkLogin($data);

            if($loginStatus != false) 
            {
                $userId = $loginStatus->id;
                $bearerToken = $this->api_auth->generateToken($userId);

                $response = array(
                    'message' => 'Successfully Logged In',
                    'token'=> $bearerToken,
                );

                return $this->response($response, 200);
            }
            else 
            {
                $response = array(
                    'message' => 'Invalid Crendentials',
                );

                return $this->response($response, 400);
            }
        }
        else 
        {
            $response = array(
                'message' => 'Email Id and password is required',
            );

            return $this->response($response);
        }
    }

}