<?php
/**
 * @name Entity definition object.
 * @author Sinisa Ristic <sinisa.ristic@gmail.com> 
 * @date 05.11.2015.
 */

namespace Pms\Model;

class EntityDefinition
{
    public $id;
    public $entity_type_id;
    public $name;
    public $code;
    public $description;
    public $price;
    public $pax;
    
    public function exchangeArray($data)
    {
        if(isset($data['id']))
        {
            $this->id = $data['id'];
        }
        
        $this->entity_type_id = (isset($data['entity_type_id'])) ? $data['entity_type_id'] : null;
        $this->name = (isset($data['name'])) ? $data['name'] : null;
        $this->code = (isset($data['code'])) ? $data['code'] : null;
        $this->description = (isset($data['description'])) ? $data['description'] : null;
        $this->price = (isset($data['price'])) ? $data['price'] : null;
        $this->pax = (isset($data['pax'])) ? $data['pax'] : null;
    }
    
    public function getArrayCopy()
    {
        return [
            'id' => $this->id,
            'entity_type_id' => $this->entity_type_id,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'price' => $this->price,
            'pax' => $this->pax,
        ];
    }
}

