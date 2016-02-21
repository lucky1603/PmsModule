<?php
/**
 * @name BusinessModel.php
 * @author Sinisa Ristic <sinisa.ristic@gmail.com>
 * @date 12/02/2015.
 */
namespace Pms\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;

/**
 * BusinessModel class.
 */
class BusinessModel
{
    public $id;
    public $name;
    public $description;
    public $company_name;
    public $address;
    public $phone;
    public $email;
    public $contact_first_name;
    public $contact_last_name;
    public $user_id;
                
    protected $dbAdapter;    
    protected $initialized = false;
    
    /**
     * Constructor.
     * @param Adapter $dbAdapter
     */
    public function __construct(Adapter $dbAdapter) {
        $this->dbAdapter = $dbAdapter;
//        $this->init();
    }
    
    public function setUserId($user_id)
    {
        $table = new \Zend\Db\TableGateway\TableGateway('business', $this->dbAdapter);
        $results = $table->select(['user_id' => $user_id]);
        if($results->count() > 0)
        {

            $this->setData($results->current());
            $this->initialized = true;
        } 

    }
    
    /**
     * Sets model data.
     * @param type $data
     */
    public function setData($data)
    {
        if(isset($data['id']))
        {
            $this->id = $data['id'];
        }
        
        if(isset($data['name']))
        {
            $this->name = $data['name'];
        }
        if(isset($data['description']))
        {
            $this->description = $data['description'];
        }
        if(isset($data['company_name']))
        {
            $this->company_name = $data['company_name'];
        }
        if(isset($data['address']))
        {
            $this->address = $data['address'];
        }
        if(isset($data['phone']))
        {
            $this->phone = $data['phone'];
        }
        if(isset($data['email']))
        {
            $this->email = $data['email'];
        }
        if(isset($data['contact_first_name']))
        {
            $this->contact_first_name = $data['contact_first_name'];
        }
        if(isset($data['contact_last_name']))
        {
            $this->contact_last_name = $data['contact_last_name'];
        }     
        if(isset($data['user_id']))
        {
            $this->user_id = $data['user_id'];
        }
    }
    
    /**
     * Gets the model data.
     * @return type
     */
    public function getData()
    {
        $data =  [
            'name' => $this->name,
            'description' => $this->description,
            'company_name' => $this->company_name,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'contact_first_name' => $this->contact_first_name,
            'contact_last_name' => $this->contact_last_name,            
        ];
        
        if(isset($this->id))
        {
            $data['id'] = $this->id;
        }
        if(isset($this->user_id))
        {
            $data['user_id'] = $this->user_id;
        }
        return $data;
    }
    
    // Implementation of the binding interface for the binding wit form object.
    public function exchangeArray($data)
    {
        $this->setData($data);
    }
    
    public function getArrayCopy()
    {
        return $this->getData();
    }
    
    /**
     * Checks if the business is initialized.
     * @return type
     */
    public function isInitialized()
    {
        return $this->initialized;
    }
        
    /**
     * Saves the model to the database;
     */
    public function save()
    {
        $table = new \Zend\Db\TableGateway\TableGateway('business', $this->dbAdapter);
        
        if(isset($this->user_id))
        {
            $results = $table->select(['user_id' => $this->user_id]);
            if($results->count() > 0)
            {
                $data = $this->getData();
                if(isset($data['user_id']))
                {
                    unset($data['user_id']);
                }
                $table->update($this->getData(), ['user_id' => $this->user_id]);
                $this->initialized = true;
            }
            else {
                $table->insert($this->getData());
                $this->initialized = true;    
            }
            
        }               
        else 
        {
            $table->insert($this->getData());
            $this->initialized = true;
        }
    }
    
    /**
     * Initializes module data from the database.
     */
    protected function init()
    {
        $table = new \Zend\Db\TableGateway\TableGateway('business', $this->dbAdapter);
        $results = $table->select();
        if($results->count() > 0)
        {
            $this->setData($results->current());
            $this->initialized = true;
        }
    }
}
