<?php

namespace Pms\Form;

use Zend\Form\Form;

class AttributeForm extends Form
{
    public function __construct($name = null, $options = array()) {
        parent::__construct('AttributeForm');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        $this->setAttribute('class', 'form-horizontal');
        $this->setAttribute('role', 'form');

        
        // Initialize options field.
        $select = new \Zend\Form\Element\Select('type');
        $select->setLabel('Data type');    
        $select->setLabelAttributes([
            'class' => 'control-label col-xs-2',
        ]);
        $select->setValueOptions([
            'boolean' => 'BOOLEAN',
            'character' => 'VARCHAR',
            'double' => 'DOUBLE', 
            'integer' => 'INTEGER', 
            'text' => 'TEXTAREA',
            'timestamp' => 'TIME',
            'select' => 'SELECT',
        ]);        
        $select->setAttribute('class', 'attr-type form-control');
        $this->add($select);
        
        // Initialize scope field.
        $scope = new \Zend\Form\Element\Select('scope');
        $scope->setLabel('Accessibility');    
        $scope->setLabelAttributes([
            'class' => 'control-label col-xs-2',
        ]);
        $scope->setValueOptions([
            0 => 'ALL',
            1 => 'DEFINITION',
            2 => 'OBJECT',
        ]);        
        $scope->setAttribute('class', 'scope form-control');
        $this->add($scope);
        
        $this->add([
            'name' => 'code',
            'attributes' => [
                'type' => 'text',
                'required' => 'required',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Code',
                'label_attributes' => [
                    'class' => 'control-label col-xs-2',
                ],
            ],
        ]);
        
        $this->add([
            'name' => 'label',
            'attributes' => [
                'type' => 'text',
                'required' => 'required',
				'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Label',
				'label_attributes' => [
					'class' => 'control-label col-xs-2',
				],
            ],
        ]);
        
        $this->add([
            'name' => 'sort_order',
            'attributes' => [
                'type' => 'text',
				'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Sort order',
				'label_attributes' => [
					'class' => 'control-label col-xs-2',
				],
            ],
        ]);
        
        $this->add([
            'name' => 'unit',
            'attributes' => [
                'type' => 'text',
				'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Unit',
				'label_attributes' => [
					'class' => 'control-label col-xs-2',
				],
            ],
        ]);
        
        // Initialize options field.
        $unique = new \Zend\Form\Element\Select('unique');
        $unique->setLabel('Unique');    
        $unique->setLabelAttributes(['class' => 'control-label col-xs-2']);
        $unique->setValueOptions([
            false => 'FALSE',
            true => 'TRUE',
        ]);        
	$unique->setAttribute('class', 'form-control');
        $this->add($unique);
        
        // Initialize options field.
        $nullable = new \Zend\Form\Element\Select('nullable');
        $nullable->setLabel('Can be null');    
        $nullable->setLabelAttributes(['class' => 'control-label col-xs-2']);
        $nullable->setValueOptions([
            false => 'FALSE',
            true => 'TRUE',
        ]);        
	$nullable->setAttribute('class', 'form-control');
        $this->add($nullable);
        
        // Hidden field for the option count
        $this->add([
            'name' => 'counter',
            'attributes' => [
                'type' => 'hidden',
                'id' => 'counter',
            ]
        ]);
        
        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'Submit', 
                'value' => 'Save',
                'id' => 'submitbutton',
				'class' => 'ui-button form-control',
            ],
        ]);
    }
}

