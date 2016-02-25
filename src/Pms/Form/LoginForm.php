<?php

namespace Pms\Form;

use Zend\Form\Form;

class LoginForm extends Form
{
    public function __construct($name = null) {
        parent::__construct('Login');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        
        $this->add(['name' => 'email',
            'attributes' => array (
                'type' => 'text',
            ),
            'options' => array (
                'label' => 'E-Mail or Username',
            )
        ]);
        
        $this->add(['name' => 'password',
            'attributes' => array (
                'type' => 'password',
            ),
            'options' => array (
                'label' => 'Password',
            )
        ]);
        
        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'Submit', 
                'value' => 'Sign In',
                'id' => 'submitbutton'
            ],
        ]);
        
    }
}

