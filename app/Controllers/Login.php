<?php

namespace App\Controllers;

use App\Models\UserModel;

class Login extends BaseController
{
    public function index()
    {
        $session = \Config\Services::session();
        if (!empty($_POST['user_email']) && !empty($_POST['user_password'])) {
            $model = new UserModel();
            $temp = $_POST;
            // $temp['user_password'] = hash_hmac('sha256', $_POST['user_password'], 'Initial-G');
            // print_r($temp);
            // return;
            $get_data = $model->get_data($temp);
            if (count($get_data) != 1) {
                $session->setFlashdata("error", "Email or Password does not exists");
                $session->setFlashdata($_POST);
                return redirect("login");
            } else {
                $get_data = json_encode($get_data[0]);
                $get_data = json_decode($get_data, true);
                $session->set($get_data);
                return redirect()->to(site_url());
            }
        }
        return view('Login');
    }
}
