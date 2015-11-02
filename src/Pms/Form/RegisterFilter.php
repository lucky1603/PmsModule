<?php
/**
 * @name Register filter.
 * @description Filter for users registration.
 * @author Sinisa Ristic <sinisa.ristic@gmail.com>
 * @date 02.11.2015.
 */
namespace Pms\Form;

use Zend\InputFilter\InputFilter;

/**
 * Register filter class.
 */
class RegisterFilter extends InputFilter {
    /**
     * Constructor.
     */
    public function __construct()   
    {
        $this->add([
            'name' => 'username',
            'required' => true,
        ]);
        
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
        
        $this->add([
            'name' => 'confirm_password',
            'required' => true,
            'validators' => array(
                array(
                    'name'    => 'Identical',
                    'options' => array(
                        'token' => 'password',
                    ),
                ),
            ),
        ]);
    }
}

