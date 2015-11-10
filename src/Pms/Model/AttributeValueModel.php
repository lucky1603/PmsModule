<?php

namespace Pms\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;

class AttributeValueModel
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
    protected $entity_definition_id;
    protected $sql;
    protected $dirty = false;
    
    public function __construct(Adapter $adapter, $entityTableName='entity_definition') {
        $this->dbAdapter = $adapter;
        $this->tableName = $entityTableName;
        $this->sql = new Sql($this->dbAdapter);
    }
    
    public function setEntityDefinitionId($id)
    {
        $this->entity_definition_id = $id;
    }
    
    public function setAttributeId($id)
    {
        if(!$this->entity_definition_id)
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
        
        $this->getValue();
    }
    
    public function setData($data)
    {
        \Zend\Debug\Debug::dump($data);
        if(isset($data['attribute_id']))
        {
            $this->id = $data['attribute_id'];
        }
        
        if(isset($data['id']))
        {
            $this->id = $data['id'];
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
                   ->where(['entity_definition_id' => $this->entity_definition_id]);
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
    
    public function save()
    {
        if(isset($this->id))
        {
            \Zend\Debug\Debug::dump('AtModel:Save:Update');
            \Zend\Debug\Debug::dump($this);
            
            $update = $this->sql->update();
            $update->table($this->tableName.'_value_'.$this->type)
                    ->set([
                        'value' => $this->value,
                    ])
                    ->where([
                            'attribute_id' => $this->id,
                            'entity_definition_id' => $this->entity_definition_id,
                        ]);
            $statement = $this->sql->prepareStatementForSqlObject($update);
            $statement->execute();
        }
        else 
        {
            \Zend\Debug\Debug::dump('AtModel:Save:Insert');
            \Zend\Debug\Debug::dump($this);

            $insert = $this->sql->insert();
            $insert->into($this->tableName.'_value_'.$this->type)
                    ->values([
                        'attribute_id' => $this->id,
                        'entity_definition_id' => $this->entity_definition_id,
                        'value' => $this->value,
                    ]);
            $statement = $this->sql->prepareStatementForSqlObject($insert);
            $statement->execute();
        }
    }
    
    public function delete()
    {
        if(!isset($this->id))
            return;
        $delete = $this->sql->delete();
        $table = $this->tableName.'_value_'.$this->type;
        $delete->from($table)
                ->where(['id' => $this->id]);
        $statement = $this->sql->prepareStatementForSqlObject($delete);
        $statement->execute();
        unset($this->id);
    }
        
}

