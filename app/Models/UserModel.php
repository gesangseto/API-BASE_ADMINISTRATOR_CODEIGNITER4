<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'user';
    protected $useAutoIncrement = true;
    protected $primaryKey = 'user_id';
    protected $allowedFields = ['user_name', 'user_email', 'user_password', 'section_id', 'status'];
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'flag_delete';
    // .. other member variables
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
        // OR $this->db = db_connect();
    }

    // public function insert_data($data = array())
    // {
    //     $this->db->table($this->table)->insert($data);
    //     return $this->db->insertID();
    // }

    // public function update_data($id, $data = array())
    // {
    //     $this->db->table($this->table)->update($data, array(
    //         "id" => $id,
    //     ));
    //     return $this->db->affectedRows();
    // }

    // public function delete_data($id)
    // {
    //     return $this->db->table($this->table)->delete(array(
    //         "id" => $id,
    //     ));
    // }

    public function get_data($param = array())
    {
        $sql = "
        SELECT * 
        FROM  $this->table AS a
        LEFT JOIN user_section AS b ON b.section_id = a.section_id
        LEFT JOIN user_department AS c ON c.department_id = b.department_id
        WHERE 1+1=2 ";
        foreach ($param as $key => $value) {
            if ($key != 'search' && $key != 'page' && $key != 'limit') {
                $sql .=  " AND $key = '$value' ";
            }
        }
        if (!empty($param['page']) && !empty($param['limit'])) {
            $offset = ($param['page'] - 1) * $param['limit'] ?? 1;
            $limit = $param['limit'];
            $sql .= " LIMIT $offset , $limit";
        }
        $query = $this->db->query($sql);
        return $query->getResult();
    }
}
