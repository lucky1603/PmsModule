<?php

namespace Pms\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Adapter\Adapter;

class EntityDefinitionModel 
{
    public $id;   
    public $name;
    public $description;
    public $code;
    public $attributes;
    
    protected $sql;
    protected $dbAdapter;
    
    public function __construct(Adapter $dbAdapter, $id=NULL) {
        $this->dbAdapter = $dbAdapter;
        $this->sql = new Sql($dbAdapter);
        if(isset($id))
        {
            $this->setId($id);
        }
    }
    
    public function setId($id)
    {
        if(isset($this->id))
        {
            // Reset attributes if they were previously set.
            unset($this->attributes);
        }
        
        $this->id = $id;
        $select = $this->sql->select();
        $select->from('entity_definition')
                ->where(['id' => $id]);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();
        $row = $results->current();
        if(isset($row))
        {
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->code = $row['code'];
            $this->description = $row['description'];
            
            $this->getAttributes();
        }
    }
    
    public function getData()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,        
            'attributes' => $this->getAttributes(),
        ];
    }
    
    public function getAttributes()
    {
        if(isset($this->attributes))
        {
            return $this->attributes;
        }
        
        $this->attributes = array();
        $types = [
            'double',
            'character',
            'integer',
            'text',
            'timestamp',
            'boolean',            
        ];
        
        foreach($types as $type)
        {
            $table = "entity_definition_value_".$type;
            $select = $this->sql->select();
            $select->from($table)
                   ->join(['a' => 'attribute'], 'attribute_id = a.id', ['*'])
                   ->where(['entity_definition_id' => $this->id]);
            $statement = $this->sql->prepareStatementForSqlObject($select);
            $results = $statement->execute();
            if($results->count() > 0)
            {
                do 
                {
                    $row = $results->current();
                    $attribute = new AttributeModel($this->dbAdapter);
                    $attribute->setEntityTypeId($this->id);
                    $attribute->setData($row);
                    $this->attributes[] = $attribute;
                } while ($results->next());     
            }                                
        }
        
        return $this->attributes;
    }
}

