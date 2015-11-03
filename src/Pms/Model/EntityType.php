<?php
/**
 * @name EntityType.php
 * @description Object model of the EntityType object.
 * @author Sinisa Ristic <sinisa.ristic@gmail.com>
 * @date 03.11.2015.
 */

namespace Pms\Model;

/**
 * Entity type class.
 */
class EntityType
{
    public $id;
    public $name;
    public $description;
    
    /**
     * Updates the entity type with the data from outside.
     * @param type $array
     */
    public function exchangeArray($data)
    {
        if(isset($data['id']))
        {
            $this->id = $data['id'];
        }
        
        $this->name = (isset($data['name'])) ? $data['name'] : null;
        $this->description = (isset($data['description'])) ? $data['description'] : null;
    }
    
    /**
     * Updates the outside world with the data from entity type.
     * @return type
     */
    public function getArrayCopy()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}

