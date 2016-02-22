<?php
/**
 * @name Rezervation form.
 * @description Form object which is used to handle the user registration.
 * @author Sinisa Ristic <sinisa.ristic@gmail.com>
 * @date 17.11.2015.
 */

namespace Pms\Form;

use Zend\Form\Form;
use Zend\Db\Sql\Sql;

class ReservationForm extends Form
{
    public function __construct($name = null, $options = array()) {
        parent::__construct('ReservationForm');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        $this->setAttribute('class', 'form-horizontal');
        $this->setAttribute('role', 'form');
        
        $this->add([
            'name' => 'user_id', 
            'attributes' => [
                'type' => 'hidden',
            ]
        ]);
                
        // Initialize client field.
        $client = new \Zend\Form\Element\Select('client_id');
        $client->setLabel('Client');
        $client->setLabelAttributes([
            'class' => 'control-label col-xs-2',
        ]);
        $dbAdapter = $options['adapter'];
        $sql = new Sql($dbAdapter);
        $select = $sql->select();
        $select->from('clients')
               ->columns(['id', 'first_name', 'last_name']);
        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();
        $options = array();
        foreach($results as $row)
        {
            $options[$row['id']] = $row['first_name'] . ' ' . $row['last_name'];
        }
        
        $client->setValueOptions($options);
        $client->setAttribute('id', 'client_id');      
        $client->setAttribute('class', 'form-control');
        $this->add($client);
        
        $status = new \Zend\Form\Element\Select('status_id');
        $status->setLabel('Reservation Status');
        $status->setLabelAttributes([
            'class' => 'control-label col-xs-2',
        ]);
        $select = $sql->select();
        $select->from('reservation_status');
        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();
        $options = array();
        foreach($results as $row)
        {
            $options[$row['id']] = $row['statustext'];
        }        
        $status->setAttribute('id', 'status_id');
        $status->setAttribute('class', 'form-control');
        $status->setValueOptions($options);
//        $status->setAttribute('style', [
//            'width' => '100px',
//        ]);
        
        $this->add($status);
        
        $this->add([
            'name' => 'created_at',
            'attributes' => [
                'type' => 'text', 
                'id' => 'submitbutton',
                'hidden' => true,
            ],
        ]);
        
        $this->add([
            'name' => 'modified_at',
            'attributes' => [
                'type' => 'text', 
                'id' => 'submitbutton',
                'hidden' => true,
            ],
        ]);
        
        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'Submit', 
                'value' => 'Save',
                'id' => 'submitbutton',
                'class' => 'form-control',
            ],
        ]);
    }
}