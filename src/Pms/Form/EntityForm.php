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
        $this->setAttribute('class', 'form-horizontal');
        $this->setAttribute('role', 'form');

        $dbAdapter = $options['adapter'];
        $sql = new Sql($dbAdapter);
        
//         Initialize options field.
        $select = new \Zend\Form\Element\Select('definition_id');
        $select->setLabel('Select type');
        $select->setLabelAttributes([
            'class' => 'control-label col-xs-2',
        ]);
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
        $select->setAttribute('id', 'definition_id');
        $select->setAttribute('class', 'form-control');
        //$select->setAttribute('disabled', 'true');
        $this->add($select);
                

        
        $this->add([
            'name' => 'guid',
            'attributes' => [
                'type' => 'text',
                'required' => 'required',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Number',
                'label_attributes' => [
                    'class' => 'control-label col-xs-2',
                ]
            ],
        ]);
        
        $status = new \Zend\Form\Element\Select('status_id');
        $status->setLabel('Set status');
        $status->setLabelAttributes([
            'class' => 'control-label col-xs-2',
        ]);
        $sqlselect1 = $sql->select();
        $sqlselect1->from('status')
                ->columns(['id', 'value']);
        $statement = $sql->prepareStatementForSqlObject($sqlselect1);
        $results = $statement->execute();        
        $options = array();
        foreach($results as $result)
        {
            $options[$result['id']] = $result['value'];
        }
        $status->setValueOptions($options);
        $status->setAttribute('id', 'status_id');
        $status->setAttribute('class', 'form-control');
        $this->add($status);
        
        $this->add([
            'name' => 'status',
            'attributes' => [
                'type' => 'text',
                'required' => 'required',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Status',
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
                'id' => 'submitbutton',
                'class' => 'form-control',
            ],
        ]);
    }
}