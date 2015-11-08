<?php

namespace Pms\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;

class AttributeModel
{
    public $id;
    public $label;
    public $code;
    public $type;
    public $sort_order;
    public $value;
   
    protected $dbAdapter;
    protected $tableName;
    protected $value_id;
    protected $entity_type_id;
    protected $sql;
    protected $dirty = false;
    
    public function __construct(Adapter $adapter, $entityTableName='entity_definition') {
        $this->dbAdapter = $adapter;
        $this->tableName = $entityTableName;
        $this->sql = new Sql($this->dbAdapter);
    }
    
    public function setEntityTypeId($id)
    {
        $this->entity_type_id = $id;
    }
    
    public function setId($id)
    {
        if(!$this->entity_type_id)
            return;
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select();
        $select->from(array('a' => 'attribute'))
                ->where(['id' => $id]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();
        $row = $results->current();
        if($row)
        {
            $this->id = $row['id'];
            $this->label = $row['label'];
            $this->code = $row['code'];
            $this->type = $row['type'];
            $this->sort_order = $row['sort_order'];
        }
    }
    
    public function setData($data)
    {
        if(isset($data['attribute_id']))
        {
            $this->id = $data['attribute_id'];
        }
        
        $this->code = (isset($data['code'])) ? $data['code'] : null;
        $this->type = (isset($data['type'])) ? $data['type'] : null;
        $this->sort_order = (isset($data['sort_order'])) ? $data['sort_order'] : null;
        $this->unit = (isset($data['unit'])) ? $data['unit'] : null;
        $this->label = (isset($data['label'])) ? $data['label'] : null;
        if(isset($data['value']))
        {
           $this->value = $data['value'];
        }
    }
    
    public function getData()
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'code' => $this->code,
            'type' => $this->type,
            'sort_order' => $this->sort_order,        
            'value' => $this->value,
        ];
    }
    
    public function getValue()
    {
        if(!isset($this->value))
        {
           $select = $this->sql->select(); 
           $select->from($this->tableName.'_value_'.$this->type)
                   ->columns(['value'])
                   ->where(['entity_type_id' => $this->entity_type_id]);
           $statement = $this->sql->prepareStatementForSqlObject($select);
           $results = $statement->execute();
           $row = $results->current();
           if(isset($row))
           {
               $this->value = $row['value'];
           }
        }
        
        return $this->value;
    }
    
    public function setValue($value)
    {
        $this->value = $value;
        $this->dirty = true;
    }
    
    public function getCollection()
    {
        return $this->rows;
    }
    
    public function getAttributes($id, $datatype)
    {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select();
        $select->from(['u' => $this->tableName.'_value_'.$datatype])
                ->columns(['id', 'entity_definition_id', 'value'])
                ->join([
                            'a' => 'attribute'
                        ], 
                        'u.attribute_id = a.id', 
                        [
                            'code',
                            'label',
                            'type',
                            'sort_order',
                            'unit',
                        ]
                )
                ->where(['entity_definition_id' => $id]);
             
        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();        
        $rows = array();
        do {
            $rows[] = $results->current();
        } while($results->next());        
        return $rows;
    }
        
}

