<?php

namespace Pms\Model;

class User 
{
    public $id;
    public $username;
    public $email;
    public $password;   
    public $role_id;
    
    public function setPassword($clear_password)
    {
        $this->password = md5($clear_password);
    }
    
    public function exchangeArray($data)
    {                
        if(isset($data['id']))
        {
            $this->id = $data['id'];
        }
        
        $this->username = (isset($data['username'])) ?
        $data['username'] : null;
        $this->email = (isset($data['email'])) ?
        $data['email'] : null;
        
        if (isset($data["password"]))
        {
            $this->setPassword($data["password"]);
        }
        
        $this->role_id = (isset($data['role_id'])) ? $data['role_id'] : null;
    }
    
    public function getArrayCopy()
    {                        
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password,
            'role_id' => $this->role_id,
        ];
    }
}


