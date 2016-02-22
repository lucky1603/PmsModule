<?php
/**
 * @name User registration form.
 * @description Form object which is used to handle the user registration.
 * @author Sinisa Ristic <sinisa.ristic@gmail.com>
 * @date 02.11.2015.
 */

namespace Pms\Form;

use Zend\Form\Form;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

/**
 * Register form class.
 */
class RegisterForm extends Form
{   
    /**
     * Constructor.
     * @param type $name
     */
    public function __construct($name = null, $options = array()) {
        parent::__construct('Register');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        $this->setAttribute('class', 'form-horizontal');
        $this->setAttribute('role', 'form');
              
        // Initialize options field.
        $select = new \Zend\Form\Element\Select('role_id');
        $select->setLabel('Select Role');
        $select->setLabelAttributes([
            'class' => 'control-label col-xs-2',
        ]);
        $dbAdapter = $options['adapter'];
        $tableGateway = new TableGateway("role", $dbAdapter, null, null);
        $s = new Select("role");
        $results = $tableGateway->selectWith($s->columns(['id','name']))->toArray();
        $options = array();
        foreach($results as $result)
        {
            $options[$result['id']] = $result['name'];
        }
        $select->setValueOptions($options);
        $select->setAttribute('class', 'form-control');
        $this->add($select);
        
        $this->add([
            'name' => 'user_id', 
            'attributes' => [
                'type' => 'hidden',
            ]
        ]);
        
        $this->add([
            'name' => 'username',
            'attributes' => [
                'type' => 'text',
                'required' => 'required',
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'Username',
                'label_attributes' => [
                    'class' => 'control-label col-xs-2',
                ]
            ],
        ]);
                
        $this->add([
            'name' => 'email',
            'attributes' => [
                'type' => 'email', 
                'required' => 'required',
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'Email',
                'label_attributes' => [
                    'class' => 'control-label col-xs-2',
                ]
            ],
            'filters' => [['name', 'stringTrim']],
            'validators' => [[
                'name' => 'EmailAddress',
                'options' => [
                    'messages' => [
                        \Zend\Validator\EmailAddress::INVALID_FORMAT => "Email address format is invalid!"
                    ]
                ]
                ],                
            ]
        ]);
        
        $this->add([
            'name' => 'password',
            'attributes' => [
                'type' => 'password', 
                'required' => 'required',
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'Password',
                'label_attributes' => [
                    'class' => 'control-label col-xs-2',
                ]
            ],            
        ]);
        
        $this->add([
            'name' => 'confirm_password',
            'attributes' => [
                'type' => 'password', 
                'required' => 'required',
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'Confirm Password',
                'label_attributes' => [
                    'class' => 'control-label col-xs-2',
                ]
            ],            
        ]);
        
        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'Submit', 
                'value' => 'Register',
                'id' => 'submitbutton',
                'class' => 'form-control'
            ],
        ]);
    }
       
}

