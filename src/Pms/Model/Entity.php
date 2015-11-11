<?php
/**
 * @name Entity.php
 * @description Object model of the Entity object.
 * @author Sinisa Ristic <sinisa.ristic@gmail.com>
 * @date 11.11.2015.
 */

namespace Pms\Model;

/**
 * Entity class.
 */
class Entity 
{
    public $id;
    public $definition_id;
    public $status;
    public $guid;
    
    /**
     * Feeds attribute params with externla values.
     * @param type $data
     */
    public function exchangeArray($data)
    {
        if(isset($data['id']))
        {
            $this->id = $id;
        }
        
        $this->definition_id = isset($data['definition_id']) ? $data['definition_id'] : null;
        $this->status = isset($data['status']) ? $data['status'] : null;
        $this->guid = isset($data['guid']) ? $data['guid'] : null;
        
    }
    
    /**
     * Returns attribute parameter values.
     * @return type
     */
    public function getArrayCopy()
    {
        return [
            'id' => $this->id,
            'definition_id' => $this->definition_id,
            'status' => $this->status,
            'guid' => $this->guid,
        ];
    }
}
