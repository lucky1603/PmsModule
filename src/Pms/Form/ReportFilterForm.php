<?php

namespace Pms\Form;

use Zend\Form\Form;

class ReportFilterForm extends Form
{
    public function __construct($name = null, $options = array()) {
        parent::__construct("ReportFilter", $options);
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        
        $type = new \Zend\Form\Element\Select('type');
        $type->setLabel('Object Type');
        $this->add($type);
        
        $start = new \Zend\Form\Element\Date('start');
        $start->setLabel("From")
              ->setAttributes([
                'min'  => '2012-01-01',
                'max'  => '2020-01-01',
                'step' => '1', // days; default step interval is 1 day    
              ])
             ->setOption('format', 'Y-m-d');
        $this->add($start);
        
        $end = new \Zend\Form\Element\Date('end');
        $end->setLabel("To")
              ->setAttributes([
                'min'  => '2012-01-01',
                'max'  => '2020-01-01',
                'step' => '1', // days; default step interval is 1 day    
              ])
             ->setOption('format', 'Y-m-d');
        $this->add($end);
                      
        $this->add([
           'name' => 'submit',
           'attributes' => [
               'type' => 'Submit',
               'value' => 'Set',
               'id' => 'submit'
           ],
       ]);
    }
}

