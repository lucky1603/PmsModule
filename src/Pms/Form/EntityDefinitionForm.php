<?php

namespace Pms\Form;

use Zend\Form\Form;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class EntityDefinitionForm extends Form 
{
    public function __construct($name = null, $options = array()) {
        parent::__construct('EntityDefinitionForm');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        
        // Initialize options field.
        $select = new \Zend\Form\Element\Select('entity_type_id');
        $select->setLabel('Entity type');
        $dbAdapter = $options['adapter'];
        $tableGateway = new TableGateway("entity_type", $dbAdapter, null, null);
        $s = new Select("entity_type");
        $results = $tableGateway->selectWith($s->columns(['id','name']))->toArray();
        $options = array();
        foreach($results as $result)
        {
            $options[$result['id']] = $result['name'];
        }
        $select->setValueOptions($options);
        $this->add($select);
        
        // Name.
        $this->add([
            'name' => 'name',
            'attributes' => [
                'type' => 'text',
                'required' => 'required'
            ],
            'options' => [
                'label' => 'Name' 
            ]
        ]);
        
        $this->add([
            'name' => 'code',
            'attributes' => [
                'type' => 'text',
                'required' => 'required'
            ],
            'options' => [
                'label' => 'Code' 
            ]
        ]);
        
        $this->add([
            'name' => 'description',
            'attributes' => [
                'type' => 'textarea',
            ],
            'options' => [
                'label' => 'Description' 
            ]
        ]);
        
        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'Submit', 
                'value' => 'Save',
                'id' => 'submitbutton'
            ],
        ]);
        
    }
}