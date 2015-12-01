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
                'COLS' => 40,
                'ROWS' => 4,
            ],
            'options' => [
                'label' => 'Decription',
            ],
        ]);
        
        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'submit',
                'value' => 'Save',
                'id' => 'submitbutton'
            ],
        ]);
    }
}

