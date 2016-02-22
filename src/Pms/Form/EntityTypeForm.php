<?php

namespace Pms\Form;

use Zend\Form\Form;
use Zend\Db\TableGateway\TableGateway;

class EntityTypeForm extends Form
{
    public function __construct($name = null) {
        parent::__construct('EntityTypeForm');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        $this->setAttribute('class', 'form-horizontal');
        $this->setAttribute('role', 'form');
        
        $this->add([
            'name' => 'user_id',
            'attributes' => [
                'type' => 'hidden',
            ]
        ]);
        
        $this->add([
            'name' => 'name',
            'attributes' => [
                'type' => 'text',
                'required' => 'required',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Name',
                'label_attributes' => [
                    'class' => 'control-label col-xs-2',
                ] 
            ],
        ]);
        
        $this->add([
            'name' => 'description',
            'attributes' => [
                'type' => 'textarea',
                'COLS' => 80,
                'ROWS' => 4,
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'Decription',
                'label_attributes' => [
                    'class' => 'control-label col-xs-2',
                ]
            ],
        ]);
        
        $select = new \Zend\Form\Element\Select('time_resolution');
        $select->setLabel("Time resolution");
        $select->setAttribute('class', 'form-control');
        $select->setLabelAttributes([
            'class' => 'control-label col-xs-2',
        ]);
        $select->setValueOptions([
            1 => "Hours",
            2 => "Days",
        ]);
        $this->add($select);
        
        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'submit',
                'value' => 'Save',
                'id' => 'submitbutton',
                'class' => 'ui-button form-control',
            ],
        ]);
    }
}

