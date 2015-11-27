<?php

namespace Pms\Form;

use Zend\Form\Form;

class AttributeForm extends Form
{
    public function __construct($name = null, $options = array()) {
        parent::__construct('AttributeForm');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        
        // Initialize options field.
        $select = new \Zend\Form\Element\Select('type');
        $select->setLabel('Data type');    
        $select->setValueOptions([
            'boolean' => 'BOOLEAN',
            'character' => 'VARCHAR',
            'double' => 'DOUBLE', 
            'integer' => 'INTENGER', 
            'text' => 'TEXTAREA',
            'timestamp' => 'TIME',
        ]);        
        $this->add($select);
        
        $this->add([
            'name' => 'code',
            'attributes' => [
                'type' => 'text',
                'required' => 'required'
            ],
            'options' => [
                'label' => 'Code',
            ],
        ]);
        
        $this->add([
            'name' => 'label',
            'attributes' => [
                'type' => 'text',
                'required' => 'required'
            ],
            'options' => [
                'label' => 'Label',
            ],
        ]);
        
        $this->add([
            'name' => 'sort_order',
            'attributes' => [
                'type' => 'text',
            ],
            'options' => [
                'label' => 'Sort order',
            ],
        ]);
        
        $this->add([
            'name' => 'unit',
            'attributes' => [
                'type' => 'text',
            ],
            'options' => [
                'label' => 'Unit',
            ],
        ]);
        
        // Initialize options field.
        $unique = new \Zend\Form\Element\Select('unique');
        $unique->setLabel('Unique');    
        $unique->setValueOptions([
            false => 'FALSE',
            true => 'TRUE',
        ]);        
        $this->add($unique);
        
        // Initialize options field.
        $nullable = new \Zend\Form\Element\Select('nullable');
        $nullable->setLabel('Can be null');    
        $nullable->setValueOptions([
            false => 'FALSE',
            true => 'TRUE',
        ]);        
        $this->add($nullable);
        
        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'Submit', 
                'value' => 'Save',
                'id' => 'submitbutton'
            ],
        ]);
    }
}

