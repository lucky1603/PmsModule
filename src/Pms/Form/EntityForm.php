<?php

namespace Pms\Form;

use Zend\Form\Form;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;

class EntityForm extends Form
{
    public function __construct($name = null, $options=array()) {
        parent::__construct('EntityForm');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        
        // Initialize options field.
        $select = new \Zend\Form\Element\Select('definition_id');
        $select->setLabel('Select type');
        $dbAdapter = $options['adapter'];
        $sql = new Sql($dbAdapter);
        $sqlselect = $sql->select();
        $sqlselect->from('entity_definition')
                ->columns(['id', 'code']);
        $statement = $sql->prepareStatementForSqlObject($sqlselect);
        $results = $statement->execute();        
        $options = array();
        foreach($results as $result)
        {
            $options[$result['id']] = $result['code'];
        }
        $select->setValueOptions($options);
        $this->add($select);
        
        $this->add([
            'name' => 'guid',
            'attributes' => [
                'type' => 'text',
                'required' => 'required'
            ],
            'options' => [
                'label' => 'Number',
            ],
        ]);
        
        $this->add([
            'name' => 'status',
            'attributes' => [
                'type' => 'text',
                'required' => 'required',
            ],
            'options' => [
                'label' => 'Status',
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