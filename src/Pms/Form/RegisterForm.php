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
              
        // Initialize options field.
        $select = new \Zend\Form\Element\Select('role_id');
        $select->setLabel('Select Role');
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
        $this->add($select);
        
        $this->add([
            'name' => 'username',
            'attributes' => [
                'type' => 'text',
                'required' => 'required'
            ],
            'options' => [
                'label' => 'Username',
            ],
        ]);
                
        $this->add([
            'name' => 'email',
            'attributes' => [
                'type' => 'email', 
                'required' => 'required'
            ],
            'options' => ['label' => 'Email'],
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
                'required' => 'required'
            ],
            'options' => ['label' => 'Password'],            
        ]);
        
        $this->add([
            'name' => 'confirm_password',
            'attributes' => [
                'type' => 'password', 
                'required' => 'required'
            ],
            'options' => ['label' => 'Confirm Password'],            
        ]);
        
        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'Submit', 
                'value' => 'Register',
                'id' => 'submitbutton'
            ],
        ]);
    }
       
}

