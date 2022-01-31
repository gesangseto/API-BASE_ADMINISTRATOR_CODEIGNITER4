<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;
use Config\Database;

class User extends BaseController
{
    use ResponseTrait;
    // get all product
    public function index()
    {
        $param = $_GET;
        $model = new UserModel();
        $data = $model->get_data($param);
        return $this->respond($data, 200);
    }

    // create a product
    public function create()
    {
        $model = new UserModel();
        $data = [
            'user_name' => $this->request->getPost('user_name'),
            'user_email' => $this->request->getPost('user_email'),
            'user_password' => $this->request->getPost('user_password'),
            'section_id' => $this->request->getPost('section_id'),
            'status' => $this->request->getPost('status'),
        ];
        $data = json_decode(file_get_contents("php://input"));
        //$data = $this->request->getPost();
        $model->insert($data);
        $response = [
            'status'   => 201,
            'error'    => null,
            'messages' => [
                'success' => 'Data Saved'
            ]
        ];

        return $this->respondCreated($data, 201, 'Data Saved');
    }

    // update product
    public function update($id = null)
    {
        $model = new UserModel();
        $json = $this->request->getJSON();
        if ($json) {
            $data = [
                'user_name' => $json->user_name,
            ];
        } else {
            $input = $this->request->getRawInput();
            $data = [
                'user_name' => $input['user_name'],
            ];
        }
        // Insert to Database
        $model->update($id, $data);
        $response = [
            'status'   => 200,
            'error'    => null,
            'messages' => [
                'success' => 'Data Updated'
            ]
        ];
        return $this->respond($response);
    }

    // delete product
    public function delete($id = null)
    {
        $model = new UserModel();
        $data = $model->find($id);
        if ($data) {
            $model->delete($id);
            $response = [
                'status'   => 200,
                'error'    => null,
                'messages' => [
                    'success' => 'Data Deleted'
                ]
            ];

            return $this->respondDeleted($response);
        } else {
            return $this->failNotFound('No Data Found with id ' . $id);
        }
    }
}
