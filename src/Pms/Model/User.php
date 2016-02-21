<?php
/**
 * @name User.php
 * @description Object model of the User object.
 * @author Sinisa Ristic <sinisa.ristic@gmail.com>
 * @date 02.11.2015.
 */

namespace Pms\Model;

/**
 * User class.
 */
class User 
{
    public $id;
    public $username;
    public $email;
    public $password;   
    public $role_id;
    public $first_name;
    public $last_name;
    public $address;
    public $city;
    public $country;
    public $pcode;
    public $phone;
    
    /**
     * Sets the password using the md5 algorithm.
     * @param type $clear_password
     */
    public function setPassword($clear_password)
    {
        $this->password = md5($clear_password);
    }
    
    /**
     * Setting of user data.
     * @param type $data
     */
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
        $this->first_name = (isset($data['first_name'])) ? $data['first_name'] : null;
        $this->first_name = (isset($data['last_name'])) ? $data['last_name'] : null;
        $this->first_name = (isset($data['address'])) ? $data['address'] : null;
        $this->first_name = (isset($data['city'])) ? $data['city'] : null;
        $this->first_name = (isset($data['country'])) ? $data['country'] : null;
        $this->first_name = (isset($data['pcode'])) ? $data['pcode'] : null;
        $this->first_name = (isset($data['phone'])) ? $data['phone'] : null;
        
    }
    
    /**
     * Getting of user data.
     * @return type
     */
    public function getArrayCopy()
    {                        
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password,
            'role_id' => $this->role_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'address' => $this->address,
            'city' => $this->city,
            'country' => $this->country,
            'pcode' => $this->pcode,
            'phone' => $this->phone,
        ];
    }
}


