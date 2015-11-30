<?php

namespace Pms\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Form\Element;


class ReservationEntityForm extends Form
{
    protected $dbAdapter;
    
    public function __construct($name = null, $options = array()) {        
       parent::__construct(isset($name) ? $name : 'ReservationEntity');
       $this->setAttribute('method', 'post');
       $this->setAttribute('enctype', 'multipart/form-data');
       
       $dbAdapter = isset($options['adapter']) ? $options['adapter'] : null;
       
       // datum_start
       $this->add([
           'name' => 'date_from',
           'attributes' => [
               'type' => 'text',
               'required' => 'required',
               'id' => 'date_from',
               'class' => 'reservation-date'
           ],
           'options' => [
               'label' => "From",
           ],
       ]);
       
       // datum_end
       $this->add([
           'name' => 'date_to',
           'attributes' => [
               'type' => 'text',
               'required' => 'required',
               'id' => 'date_to',
               'class' => 'reservation-date'
           ],
           'options' => [
               'label' => "To",
           ],
       ]);
                              
       // Guest (predlozi se nosilac rezervacije)
       $guest_id = new \Zend\Form\Element\Select('guest_id');
       $guest_id->setLabel('Guest');
       $guest_id->setAttribute('id', 'guest_id');
       if($dbAdapter)
       {
           $sql = new Sql($dbAdapter);
           $select = $sql->select();
           $select->from('clients');
           $statement = $sql->prepareStatementForSqlObject($select);
           $results = $statement->execute();
           $options = array();
           foreach($results as $row)
           {
               $options[$row['id']] = $row['first_name'].' '.$row['last_name'];
           }
           $guest_id->setValueOptions($options);
       }
       $this->add($guest_id);
       
       // definicija (izabrati definiciju)
       $tip = new \Zend\Form\Element\Select('entity_definition_id');
       $tip->setLabel('Entity Definition');
       $tip->setAttribute('id', 'entity_definition_id');
       if($dbAdapter)
       {
           $sql = new Sql($dbAdapter);
           $select = $sql->select();
           $select->from('entity_definition');
//                   ->where(['entity_type_id' => 1]);
           $statement = $sql->prepareStatementForSqlObject($select);
           $results = $statement->execute();
           $options = array();
           foreach($results as $row)
           {
               $options[$row['code']] = $row['name'];
           }
           $tip->setValueOptions($options);
       }
       $this->add($tip);
        
       // Broj (za postojeci tip i datume rezervacije, proveriti raspolozivost resursa
       $guid = new \Zend\Form\Element\Select('entity_id');
       $guid->setLabel('Entity Number');
       $guid->setAttribute('id', 'entity_id');
       if($dbAdapter)
       {
           $sql = new Sql($dbAdapter);
           $select = $sql->select();
           $select->from('entity');
           $statement = $sql->prepareStatementForSqlObject($select);
           $results = $statement->execute();
           $options = array();
           foreach($results as $row)
           {
               $options[$row['id']] = $row['guid'];
           }
           $guid->setValueOptions($options);
       }
       $this->add($guid);
       
       $this->add([
           'name' => 'submit',
           'attributes' => [
               'type' => 'submit',
               'value' => 'Save',
               'id' => 'submit'
           ],
       ]);     
    }
}

