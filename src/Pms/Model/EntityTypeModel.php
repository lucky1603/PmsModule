<?php

namespace Pms\Model;

use \Zend\Db\Adapter\Adapter;
use \Zend\Db\Sql\Sql;

class EntityTypeModel 
{
    public $id;
    public $name;
    public $description;
    public $attributes = array();
    
    protected $sql;    
    
    public function __construct(Adapter $dbAdapter) {
        $this->sql = new Sql($dbAdapter);
    }
    
    public function setData($data)
    {
        if(!empty($data['id']))
        {
            $this->id = $data['id'];
        }
        
        if(!empty($data['name']))
        {
            $this->name = $data['name'];
        }
        
        if(!empty($data['description']))
        {
            $this->description = $data['description'];
        }
        
        if(!empty($data['attributes']))
        {
            $attributes = $data['attributes'];
            foreach($attributes as $attributeData)
            {
                $attribute = new Attribute();
                $attribute->setData($attributeData);
                $this->attributes[]
            }
        }
    }
}