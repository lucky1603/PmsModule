<?php

namespace Pms\Form;
use Zend\Form\Form;
use Zend\Db\Sql\Sql;

class AvailabilityForm extends Form
{
    public function __construct($name = null, $options = array()) {
        parent::__construct('AvailabilityForm');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        
        // Initialize options field.
        $select = new \Zend\Form\Element\Select('entity_type_id');
        $select->setLabel('Select type');
        $dbAdapter = $options['adapter'];
        $sql = new Sql($dbAdapter);
        $sqlselect = $sql->select();
        $sqlselect->from('entity_type')
                ->columns(['id', 'name']);
        $statement = $sql->prepareStatementForSqlObject($sqlselect);
        $results = $statement->execute();        
        $options = array();
        foreach($results as $result)
        {
            $options[$result['id']] = $result['name'];
        }
        $select->setValueOptions($options);
        $select->setAttribute('id', 'entity_type_id');
        $select->setAttribute('class' , 'form-entry ui-button');
        $select->setLabelAttributes(['class' => 'form-entry ui-button']);
        $this->add($select);
        
        // datum_start
        $this->add([
            'name' => 'date_from',
            'attributes' => [
                'type' => 'text',
                'required' => 'required',
                'id' => 'date_from',
                'class' => 'reservation-date form-entry ui-button'
            ],
            'options' => [
                'label' => "From",
                'label_attributes' => [
                    'class' => 'form-entry ui-button',
                ],
            ],
        ]);
        
        // Show / hide attributes.
        $multiCheckbox = new \Zend\Form\Element\MultiCheckbox('multi-checkbox');
        $multiCheckbox->setLabel('Show/Hide Attributes ?');
        $multiCheckbox->setLabelAttributes(['class' => 'ui-button']);
        $multiCheckbox->setValueOptions(array(
                '0' => 'Apple',
                '1' => 'Orange',
                '2' => 'Lemon'
        ));
        $multiCheckbox->setLabelAttributes(['class' => 'form-entry']);
        $multiCheckbox->setAttribute('class', 'form-entry ui-button');
        $this->add($multiCheckbox);
        
        // Attributes to sort by.
        $sort = new \Zend\Form\Element\Select('sort');
        $sort->setLabel('Sort by attribute:');
        $sort->setLabelAttributes(['class' => 'form-entry']);
        $sort->setAttribute('class', 'form-entry');
        $this->add($sort);        
        
        // Submit.
        $this->add([
           'name' => 'submit',
           'attributes' => [
               'type' => 'submit',
               'value' => 'Refresh',
               'id' => 'submit', 
               'class' => 'ui-button',
               //'class' => 'form-entry'
           ],
       ]);   
    }
}
