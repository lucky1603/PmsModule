<?php

namespace Pms\Form;

use Zend\Form\Form;
use Zend\Db\Sql\Sql;

class ClientForm extends Form
{
    public function __construct($name = null, $options = array()) {
        parent::__construct('ClientForm');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        $adapter = $options['adapter'];
        
        // First name.
        $this->add([
           'name' => 'first_name',
           'attributes' => [
               'type' => 'text',
//               'required' => 'required',
           ],
           'options' => [
               'label' => 'First Name'
           ]
        ]);
        
        // Last name.
        $this->add([
           'name' => 'last_name',
           'attributes' => [
               'type' => 'text',
//               'required' => 'required',
           ],
           'options' => [
               'label' => 'Last Name'
           ]
        ]);
        
        // Address1.
        $this->add([
           'name' => 'address1',
           'attributes' => [
               'type' => 'textarea',
//               'required' => 'required',
           ],
           'options' => [
               'label' => 'Addres1'
           ]
        ]);
        
        // Address2.
        $this->add([
           'name' => 'address2',
           'attributes' => [
               'type' => 'textarea',               
           ],
           'options' => [
               'label' => 'Addres2'
           ]
        ]);
        
        // City.
        $this->add([
           'name' => 'city',
           'attributes' => [
               'type' => 'text',
//               'required' => 'required',
           ],
           'options' => [
               'label' => 'City'
           ]
        ]);
        
        // Zipcode
        $this->add([
           'name' => 'zipcode',
           'attributes' => [
               'type' => 'text',
//               'required' => 'required',
           ],
           'options' => [
               'label' => 'Zip Code'
           ]
        ]);
        
        // Country.
        $this->add([
           'name' => 'country',
           'attributes' => [
               'type' => 'text',
//               'required' => 'required',
           ],
           'options' => [
               'label' => 'Country'
           ]
        ]);
        
        // Phone.
        $this->add([
           'name' => 'phone',
           'attributes' => [
               'type' => 'text',
//               'required' => 'required',
           ],
           'options' => [
               'label' => 'Phone'
           ]
        ]);
        
        // Mobile.
        $this->add([
           'name' => 'mobile',
           'attributes' => [
               'type' => 'text',
           ],
           'options' => [
               'label' => 'Mobile'
           ]
        ]);
        
        // Fax.
        $this->add([
           'name' => 'fax',
           'attributes' => [
               'type' => 'text',
           ],
           'options' => [
               'label' => 'Fax'
           ]
        ]);
        
        // EMail.
        $this->add([
           'name' => 'email',
           'attributes' => [
               'type' => 'text',
//               'required' => 'required',
           ],
           'options' => [
               'label' => 'E-Mail'
           ]
        ]);
        
        // Title
        $title = new \Zend\Form\Element\Select('title_id');
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('title');
        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();
        $options = array();
        foreach($results as $row)
        {
            $options[$row['id']] = $row['title'];
        }
        $title->setValueOptions($options);
        $title->setLabel('Title');
        $this->add($title);
        
        // Submit
        $this->add([
           'name' => 'submit',
           'attributes' => [
               'type' => 'submit',
//               'required' => 'required',
               'value' => 'Save',
           ],
           'options' => [
               'label' => 'Save'
           ]
        ]);
    }
}

