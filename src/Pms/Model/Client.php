<?php
/**
 * @name Client object. Client mapping to the database.
 * @author Dragutin Jovanovic <gutindra.gmail.com>
 * @date 16.11.2015.
 */
namespace Pms\Model;

/**
 * Client class.
 */
class Client
{
    public $id;
    public $first_name;
    public $last_name;
    public $address1;
    public $address2;
    public $city;
    public $zipcode;
    public $country;
    public $phone;
    public $mobile;
    public $fax;
    public $email;
    public $title;
    public $guest_class;
    public $user_id;
    
    /**
     * Sets the client data from outside.
     * @param type $data
     */
    public function exchangeArray($data)
    {
        if(isset($data['id']))
        {
            $this->id = $data['id'];
        }
        
        $this->first_name = isset($data['first_name']) ? $data['first_name'] : null;
        $this->last_name = isset($data['last_name']) ? $data['last_name'] : null;
        $this->address1 = isset($data['address1']) ? $data['address1'] : null;
        $this->address2 = isset($data['address2']) ? $data['address2'] : null;
        $this->city = isset($data['city']) ? $data['city'] : null;
        $this->zipcode = isset($data['zipcode']) ? $data['zipcode'] : null;
        $this->country = isset($data['country']) ? $data['country'] : null;
        $this->phone = isset($data['phone']) ? $data['phone'] : null;
        $this->mobile = isset($data['mobile']) ? $data['mobile'] : null;
        $this->fax = isset($data['fax']) ? $data['fax'] : null;
        $this->email = isset($data['email']) ? $data['email'] : null;
        $this->title = isset($data['title']) ? $data['title'] : null;
        $this->guest_class = isset($data['guest_class']) ? $data['guest_class'] : null;       
        $this->user_id = isset($data['user_id']) ? $data['user_id'] : null;
    }
    
    /**
     * Gets the client data to the outside.
     * @return type
     */
    public function getArrayCopy()
    {
        return [
            'id'=> $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'address1' => $this->address1,
            'address2' => $this->address2,
            'city' => $this->city,
            'zipcode' => $this->zipcode,
            'country' => $this->country,
            'phone' => $this->phone,
            'mobile' => $this->mobile,
            'fax' => $this->fax,
            'email' => $this->email,
            'title' => $this->title,
            'guest_class' => $this->guest_class,
            'user_id' => $this->user_id,
        ];
    }
}

