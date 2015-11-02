<?php

namespace Pms\Form;

use Zend\InputFilter\InputFilter;

class LoginFilter extends InputFilter
{
    public function __construct() {
        $this->add([
            'name' => 'email',
            'required' => true,
            'validators' => [
                [
                    'name' => 'EmailAddress',
                    'options' => [
                        'domain' => true
                    ]
                ]
            ]
        ]);
        
        $this->add([
            'name' => 'password',
            'required' => true
        ]);
    }
}
