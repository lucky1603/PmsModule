<?php
/**
 * @name Attribute.php
 * @description Object model of the Attribute object.
 * @author Sinisa Ristic <sinisa.ristic@gmail.com>
 * @date 05.11.2015.
 */

namespace Pms\Model;

/**
 * Attribute class.
 */
class Attribute
{
    public $id;
    public $code;
    public $label;
    public $type;
    public $sort_order;
    public $unit;
    public $value;
    public $unique;
    public $nullable;
    
    /**
     * Feeds attribute params with externla values.
     * @param type $data
     */
    public function exchangeArray($data)
    {
        if(isset($data['id']))
        {
            $this->id = $data['id'];
        }
        
        $this->code = (isset($data['code'])) ? $data['code'] : null;
        $this->label = (isset($data['label'])) ? $data['label'] : null;
        $this->type = (isset($data['type'])) ? $data['type'] : null;
        $this->sort_order = (isset($data['sort_order'])) ? $data['sort_order'] : null;
        $this->unit = (isset($data['unit'])) ? $data['unit'] : null;
        
        if(!empty($data['nullable']))
        {
            $this->nullable = $data['nullable'];
        }
        
        if(!empty($data['unique']))
        {
            $this->nullable = $data['unique'];
        }
    }
    
    /**
     * Returns attribute parameter values.
     * @return type
     */
    public function getArrayCopy()
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'label' => $this->label,
            'type' => $this->type,
            'sort_order' => $this->sort_order,
            'unit' => $this->unit,
            'unique' => $this->nullable,
            'nullable' => $this->nullable,
        ];
    }
}

