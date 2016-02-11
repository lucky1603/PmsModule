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
        $this->setAttribute('class', 'form-horizontal');
        $this->setAttribute('role' , 'form');
        
        // First name.
        $this->add([
           'name' => 'first_name',
           'attributes' => [
               'type' => 'text',
               'class' => 'form-control',
//               'required' => 'required',
           ],
           'options' => [
               'label' => 'First Name',
               'label_attributes' => [
                    'class' => 'control-label col-xs-2',
                ]
           ]
        ]);
        
        // Last name.
        $this->add([
           'name' => 'last_name',
           'attributes' => [
               'type' => 'text',
               'class' => 'form-control',
//               'required' => 'required',
           ],
           'options' => [
               'label' => 'Last Name',
               'label_attributes' => [
                    'class' => 'control-label col-xs-2',
               ]
           ]
        ]);
        
        // Address1.
        $this->add([
           'name' => 'address1',
           'attributes' => [
               'type' => 'textarea',
               'required' => 'required',
               'ROWS' => 4,
               'COLS' => 40,
               'class' => 'form-control',
           ],
           'options' => [
               'label' => 'Addres1',
               'label_attributes' => [
                    'class' => 'control-label col-xs-2',
               ]
           ]
        ]);
        
        // Address2.
        $this->add([
           'name' => 'address2',
           'attributes' => [
               'type' => 'textarea',               
               'ROWS' => 4,
               'COLS' => 40,
               'class' => 'form-control',
           ],
           'options' => [
               'label' => 'Addres2',
               'label_attributes' => [
                    'class' => 'control-label col-xs-2',
               ]
           ]
        ]);
        
        // City.
        $this->add([
           'name' => 'city',
           'attributes' => [
               'type' => 'text',
               'class' => 'form-control',
//               'required' => 'required',
           ],
           'options' => [
               'label' => 'City',
               'label_attributes' => [
                    'class' => 'control-label col-xs-2',
               ]
           ]
        ]);
        
        // Zipcode
        $this->add([
           'name' => 'zipcode',
           'attributes' => [
               'type' => 'text',
               'class' => 'form-control',
//               'required' => 'required',
           ],
           'options' => [
               'label' => 'Zip Code',
               'label_attributes' => [
                    'class' => 'control-label col-xs-2',
               ]
           ]
        ]);
        
        // Country.
        $this->add([
           'name' => 'country',
           'attributes' => [
               'type' => 'text',
               'class' => 'form-control',
//               'required' => 'required',
           ],
           'options' => [
               'label' => 'Country',
               'label_attributes' => [
                    'class' => 'control-label col-xs-2',
               ]
           ]
        ]);
        
        // Phone.
        $this->add([
           'name' => 'phone',
           'attributes' => [
               'type' => 'text',
               'class' => 'form-control',
//               'required' => 'required',
           ],
           'options' => [
               'label' => 'Phone',
               'label_attributes' => [
                    'class' => 'control-label col-xs-2',
               ]
           ]
        ]);
        
        // Mobile.
        $this->add([
           'name' => 'mobile',
           'attributes' => [
               'type' => 'text',
               'class' => 'form-control',
           ],
           'options' => [
               'label' => 'Mobile',
               'label_attributes' => [
                    'class' => 'control-label col-xs-2',
               ]
           ]
        ]);
        
        // Fax.
        $this->add([
           'name' => 'fax',
           'attributes' => [
               'type' => 'text',
               'class' => 'form-control',
           ],
           'options' => [
               'label' => 'Fax',
               'label_attributes' => [
                    'class' => 'control-label col-xs-2',
               ]
           ]
        ]);
        
        // EMail.
        $this->add([
           'name' => 'email',
           'attributes' => [
               'type' => 'text',
               'size' => 40,
               'class' => 'form-control',
//               'required' => 'required',
           ],
           'options' => [
               'label' => 'E-Mail',
               'label_attributes' => [
                    'class' => 'control-label col-xs-2',
               ]
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
        $title->setAttribute('class', 'form-control');
        $title->setLabel('Title');
        $title->setLabelAttributes([
            'class' => 'control-label col-xs-2',
        ]);
        $this->add($title);
        
        // Submit
        $this->add([
           'name' => 'submit',
           'attributes' => [
               'type' => 'submit',
//               'required' => 'required',
               'value' => 'Save',
               'class' => 'ui-button form-control',
           ],
           'options' => [
               'label' => 'Save'
           ]
        ]);
    }
}

