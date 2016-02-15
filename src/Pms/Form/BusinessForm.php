<?php

namespace Pms\Form;

use Zend\Form\Form;

class BusinessForm extends Form
{
    public function __construct($name = null, $options = array()) {
        parent::__construct('BusinessForm');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        $this->setAttribute('class', 'form-horizontal');
        $this->setAttribute('role', 'form');
        
        $this->add([
            'name' => 'name',
            'attributes' => [
                'type' => 'text',
                'required' => 'required',
                'value' => 'Enter text here...',
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'Business Name',
                'label_attributes' => [
                    'class' => 'control-label col-xs-2',
                ]
            ],
        ]);
        
        $this->add([
            'name' => 'description',
            'attributes' => [
                'type' => 'textarea',
                'required' => 'required',
                'COLS' => 80,
                'ROWS' => 4,
                'value' => 'Enter text here...',
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'Short Description',
                'label_attributes' => [
                    'class' => 'control-label col-xs-2',
                ]
            ],
        ]);
        
        $this->add([
            'name' => 'company_name',
            'attributes' => [
                'type' => 'text',
                'required' => 'required',
                'value' => 'Enter text here...',
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'Company Name',
                'label_attributes' => [
                    'class' => 'control-label col-xs-2',
                ]
            ],
        ]);
        
        $this->add([
            'name' => 'address',
            'attributes' => [
                'type' => 'textarea',
                'COLS' => 80,
                'ROWS' => 4,
                'required' => 'required',
                'value' => 'Enter text here...',
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'Address',
                'label_attributes' => [
                    'class' => 'control-label col-xs-2',
                ]
            ],
        ]);
        
        $this->add([
            'name' => 'phone',
            'attributes' => [
                'type' => 'text',
                'required' => 'required',
                'value' => 'Enter text here...',
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'Phone',
                'label_attributes' => [
                    'class' => 'control-label col-xs-2',
                ]
            ],
        ]);
        
        $this->add([
            'name' => 'email',
            'attributes' => [
                'type' => 'text',
                'required' => 'required',
                'value' => 'Enter text here...',
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'E-Mail',
                'label_attributes' => [
                    'class' => 'control-label col-xs-2',
                ]
            ],
        ]);
        
        $this->add([
            'name' => 'contact_first_name',
            'attributes' => [
                'type' => 'text',
                'required' => 'required',
                'value' => 'Enter text here...',
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'Contact First Name',
                'label_attributes' => [
                    'class' => 'control-label col-xs-2',
                ]
            ],
        ]);
        
        $this->add([
            'name' => 'contact_last_name',
            'attributes' => [
                'type' => 'text',
                'required' => 'required',
                'value' => 'Enter text here...',
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'Contact Last Name',
                'label_attributes' => [
                    'class' => 'control-label col-xs-2',
                ]
            ],
        ]);
        
        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'submit',
                'value' => 'Save',
                'class' => 'form-control'
            ],            
        ]);
        
    }
}

