<?php
/**
 * @name AttributeModel.php
 * @author Sinisa Ristic <sinisa.ristic@gmail.com>
 * @date 25.11.2015.
 */
namespace Pms\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;

/**
 * AttributeModel class.
 */
class AttributeModel 
{
    public $id;
    public $internal_id;
    public $code;
    public $label;
    public $type;
    public $sort_order;
    public $unit;
    public $unique;
    public $nullable;
    public $optionValues;
    public $entity_type_id;
    
    protected $sql;
    
    /**
     * Constructor.
     * @param Adapter $dbAdapter
     */
    public function __construct(Adapter $dbAdapter) {
        $this->sql = new Sql($dbAdapter);
    }
    
    /**
     * Sets the reference to corresponding entity type.
     * @param type $id
     */
    public function setEntityTypeId($id)
    {
        $this->entity_type_id = $id;
    }
    
    /**
     * Sets current id.
     * @param type $id
     */
    public function setId($id)
    {
        $select = $this->sql->select();
        $select->from('attribute')
               ->where(['id' => $id]);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();
        $row = $results->current();
        
        $select = $this->sql->select();
        $select->from('attribute_option_values')
                ->where(['attribute_id' => $id]);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $options = $statement->execute();
        $optionEntries = array();
        foreach($options as $option)
        {
            $optionEntries[$option['value']] = $option;
        }
        $row['option_values'] = $optionEntries;
        $this->setData($row);
    }
    
    /**
     * Sets data from outside.
     * @param type $data
     */
    public function setData($data)
    {
        if(!empty($data['id']))
        {
            $this->id = $data['id'];
            $this->internal_id = $this->id;
        }        
        else if(!empty($data['internal_id']))
        {
            $this->internal_id = $data['internal_id'];
        }     
        if(!empty($data['entity_type_id']))
        {
            $this->entity_type_id = $data['entity_type_id'];
        }
        if(!empty($data['code']))
        {
            $this->code = $data['code'];
        }        
        if(!empty($data['label']))
        {
            $this->label = $data['label'];
        }        
        if(!empty($data['type']))
        {
            $this->type = $data['type'];
        }        
        if(!empty($data['sort_order']))
        {
            $this->sort_order = $data['sort_order'];
        }        
        if(!empty($data['unit']))
        {
            $this->unit = $data['unit'];
        }        
        if(!empty($data['value']))
        {
            $this->value = $data['value'];
        }        
        if(!empty($data['unique']))
        {
            $this->unique = $data['unique'];
        }
        if(!empty($data['$nullable']))
        {
            $this->$nullable = $data['$nullable'];
        }
        if(!empty($data['option_values']))
        {
            $this->optionValues = array();
            $optionValues = $data['option_values'];
            foreach($optionValues as $optionValue)
            {
                $this->optionValues[$optionValue['value']] =  $optionValue;
            }
        }
    }
    
    /**
     * Returns object data to outside world.
     * @return type
     */
    public function getData()
    {
        $data = [
            'id' => $this->id,      
            'internal_id' => $this->internal_id,
            'entity_type_id' => $this->entity_type_id,
            'code' => $this->code,
            'label' => $this->label,           
            'type' => $this->type,
            'sort_order' => $this->sort_order,
            'unit' => $this->unit,
            'unique' => $this->unique,
            'nullable' => $this->nullable,            
        ];
        
        if(!empty($this->optionValues))
        {
            $optionValues = array();
            foreach($this->optionValues as $key=>$value)
            {
                $optionValues[$key] = $value;
            }
            $data['option_values'] = $optionValues;
        }
                        
        return $data;
    }
    
    /**
     * Calls set data.
     * @param type $data
     */
    public function exchangeArray($data)
    {
        $this->setData($data);
    }
    
    /**
     * Calls get data.
     * @return type
     */
    public function getArrayCopy()
    {
        return $this->getData();
    }
    
    /**
     * save
     */
    public function save()
    {
        $dataToInsert = $this->getData();
        unset($dataToInsert['option_values']);
        unset($dataToInsert['internal_id']);
        unset($dataToInsert['entity_type_id']);
            
        if(empty($this->id))
        {            
            $insert = $this->sql->insert();
            $insert->into($table)
                   ->values($dataToInsert);
            $statement = $this->sql->prepareStatementForSqlObject($insert);
            $statement->execute();
            
            // Find id.
            $select = $this->sql->select();
            $select->from('entity_type')
                    ->order(['id DESC'])
                    ->limit(1);
            $statement = $this->sql->prepareStatementForSqlObject($select);
            $results = $statement->execute();
            $row = $results->current();
            $this->id = $row['id'];
        }
        else 
        {
            $update = $this->sql->update();
            $update->table('entity_type')
                    ->set($dataToInsert)
                    ->where(['id' => $this->id]);
            $statement = $this->sql->prepareStatementForSqlObject($update);
            $statement->execute();
        }
        
        // Find existing option values.
//        $select = $this->sql->select();
//        $select->from('option_values')
//                ->where(['attribute_id' => $this->id]);
//        $statement = $this->sql->prepareStatementForSqlObject($select);
//        $resultset = $statement->execute();
//        $options = array();
//        foreach($resultset as $row)
//        {
//            $options[$row['value']] = $row;
//        }
//        
        
    }
        
    /**
     * If the attribute has the option type
     * get the textual value representation.
     */
    public function getOptionValue()
    {
        
    }
}

