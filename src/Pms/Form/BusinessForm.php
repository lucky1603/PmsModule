<?php

namespace Pms\Form;

use Zend\Form\Form;

class BusinessForm extends Form
{
    public function __construct($name = null, $options = array()) {
        parent::__construct('BusinessForm');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        
        $this->add([
            'name' => 'name',
            'attributes' => [
                'type' => 'text',
                'required' => 'required',
                'value' => 'Enter text here...',
            ],
            'options' => [
                'label' => 'Business Name',
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
            ],
            'options' => [
                'label' => 'Short Description',
            ],
        ]);
        
        $this->add([
            'name' => 'company_name',
            'attributes' => [
                'type' => 'text',
                'required' => 'required',
                'value' => 'Enter text here...',
                
            ],
            'options' => [
                'label' => 'Company Name'
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
            ],
            'options' => [
                'label' => 'Address'
            ],
        ]);
        
        $this->add([
            'name' => 'phone',
            'attributes' => [
                'type' => 'text',
                'required' => 'required',
                'value' => 'Enter text here...',
            ],
            'options' => [
                'label' => 'Phone'
            ],
        ]);
        
        $this->add([
            'name' => 'email',
            'attributes' => [
                'type' => 'text',
                'required' => 'required',
                'value' => 'Enter text here...',
            ],
            'options' => [
                'label' => 'E-Mail'
            ],
        ]);
        
        $this->add([
            'name' => 'contact_first_name',
            'attributes' => [
                'type' => 'text',
                'required' => 'required',
                'value' => 'Enter text here...',
            ],
            'options' => [
                'label' => 'Contact First Name'
            ],
        ]);
        
        $this->add([
            'name' => 'contact_last_name',
            'attributes' => [
                'type' => 'text',
                'required' => 'required',
                'value' => 'Enter text here...',
            ],
            'options' => [
                'label' => 'Contact Last Name'
            ],
        ]);
        
        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'submit',
                'value' => 'Save',
            ],            
        ]);
        
    }
}

