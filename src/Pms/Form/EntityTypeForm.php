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
        
        $this->add([
            'name' => 'name',
            'attributes' => [
                'type' => 'text',
                'required' => 'required'
            ],
            'options' => [
                'label' => 'Name',
            ],
        ]);
        
        $this->add([
            'name' => 'description',
            'attributes' => [
                'type' => 'textarea',
                'COLS' => 80,
                'ROWS' => 4,
            ],
            'options' => [
                'label' => 'Decription',
            ],
        ]);
        
        $select = new \Zend\Form\Element\Select('time_resolution');
        $select->setLabel("Time resolution");
        $select->setValueOptions([
            1 => "Days",
            2 => "Hours",
            3 => "Minutes",
        ]);
        $this->add($select);
        
        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'submit',
                'value' => 'Save',
                'id' => 'submitbutton',
                'class' => 'ui-button',
            ],
        ]);
    }
}

