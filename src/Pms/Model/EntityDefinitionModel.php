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
    public $entity_type_id;
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
            $this->entity_type_id = $row['entity_type_id'];
            
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
            'entity_type_id' => $this->entity_type_id,
            'attributes' => $this->getAttributes(),
        ];
    }
    
    public function setData($data)
    {
        if(isset($data['id']))
        {
            $this->id = $data['id'];
            unset($data['id']);
        }
        
        $this->entity_type_id = (isset($data['entity_type_id'])) ? $data['entity_type_id'] : null;
        unset($data['entity_type_id']);
        $this->name = (isset($data['name'])) ? $data['name'] : null;
        unset($data['name']);
        $this->code = (isset($data['code'])) ? $data['code'] : null;
        unset($data['code']);
        $this->description = (isset($data['description'])) ? $data['description'] : null;
        unset($data['description']);
        $this->price = (isset($data['price'])) ? $data['price'] : null;
        unset($data['price']);
        $this->pax = (isset($data['pax'])) ? $data['pax'] : null;
        unset($data['pax']);
 
        if(count($data))
        {
            foreach($data as $key=>$value)
            {
                if(isset($this->attributes) && array_key_exists($key, $this->attributes))
                {
                    $attribute = $this->attributes[$key];
                    $attribute->setValue($data[$key]);
                }                                
            }
        }        
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
                    $attribute = new AttributeValueModel($this->dbAdapter);
                    $attribute->setEntityDefinitionId($this->id);
                    $attribute->setData($row);
                    $this->attributes[$attribute->code] = $attribute;
                } while ($results->next());     
            }                                
        }
        
        return $this->attributes;
    }
    
    public function save()
    {
        if($this->id != NULL)
        {
            // Save entity definition data.
            $update = $this->sql->update();
            $update->table('entity_definition')
                    ->set([
                            'code' => $this->code,
                            'name' => $this->name,
                            'description' => $this->description,  
                            'entity_type_id' => $this->entity_type_id,
                        ])
                    ->where(['id' => $this->id]);
            $statement = $this->sql->prepareStatementForSqlObject($update);
            $results = $statement->execute();
               
            // Save attributes.
            foreach ($this->attributes as $attribute)
            {
                $attribute->save();
            }                        
        }
        else 
        {
            $insert = $this->sql->insert();
            $insert->into('entity_definition')
                    ->set([
                        'code' => $this->code,
                        'name' => $this->name,
                        'description' => $this->description,  
                        'entity_type_id' => $this->entity_type_id,
                    ]);
            $statement = $this->sql->prepareStatementForSqlObject($insert);
            $insert->execute();
            
            if(isset($this->attributes))
            {
                foreach($this->attributes as $attribute)
                {
                    $attribute->save();
                }
            }
        }
    }
    
    // These two are for the sake of compatibility with zend form-model relationship.
    public function getArrayCopy()
    {
        return $this->getData();
    }
    
    public function exchangeArray($data)
    {
        $this->setData($data);
    }
}

