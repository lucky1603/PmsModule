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
        $this->setAttribute('class', 'form-horizontal');
        $this->setAttribute('role', 'form');
        
        // Initialize options field.
        $select = new \Zend\Form\Element\Select('entity_type_id');
        $select->setLabel('Entity type');
        $select->setLabelAttributes([
            'class' => 'control-label col-sm-2'
        ]);
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
        $select->setAttribute('class', 'form-control');
        $this->add($select);
        
        // Name.
        $this->add([
            'name' => 'name',
            'attributes' => [
                'type' => 'text',
                'required' => 'required',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Name',
                'label_attributes' => [
                    'class' => 'control-label col-sm-2'
                ]
            ]
        ]);
        
        $this->add([
            'name' => 'code',
            'attributes' => [
                'type' => 'text',
                'required' => 'required',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Code',
                'label_attributes' => [
                    'class' => 'control-label col-sm-2'
                ]
            ]
        ]);
        
        $this->add([
            'name' => 'description',
            'attributes' => [
                'type' => 'textarea',
                'COLS' => 40,
                'ROWS' => 4,
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Description',
                'label_attributes' => [
                    'class' => 'control-label col-sm-2'
                ]
            ]
        ]);
        
        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'Submit', 
                'value' => 'Save',
                'id' => 'submitbutton',
                'class' => 'ui-button form-control',
            ],
        ]);
        
    }
}