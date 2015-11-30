<?php
/**
 * @name Entity definition model object.
 * @author Sinisa Ristic <sinisa.ristic@gmail.com> 
 * @date 10.11.2015.
 */

namespace Pms\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;

/**
 * EntityDefinitionModel class.
 */
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
    protected $attributesToDelete = array();
    protected $oldAttributes;
    
    /**
     * Constructor.
     * @param Adapter $dbAdapter
     * @param type $id
     */
    public function __construct(Adapter $dbAdapter, $id=NULL) {
        $this->dbAdapter = $dbAdapter;
        $this->sql = new Sql($dbAdapter);
        if(isset($id))
        {
            $this->setId($id);
        }
    }
    
    public function setEntityType($entity_type_id)
    {
        // Check the entity_type table.
        $etTable = new \Zend\Db\TableGateway\TableGateway('entity_type', $this->dbAdapter);
        $resultset = $etTable->select(['id' => $entity_type_id]);
        if($resultset->count() == 0)
        {
            return;
        }
        $etModel = new EntityTypeModel($this->dbAdapter);
        $etModel->setId($entity_type_id);
        $attributes = $etModel->attributes;
        
        // Delete old attributes.
        // Deletion takes place only the first time, because only 
        // then the attributes have the values from the table. 
        if(isset($this->attributes) && empty($this->oldAttributes))
        {
            $this->oldAttributes = $this->attributes;            
        }
        $this->attributes = array();        
        foreach($attributes as $attribute)
        {
            $avModel = new AttributeValueModel($this->dbAdapter);
            //$avModel->setData($attribute->getData());
            $avModel->from($attribute);
            $avModel->setEntityDefinitionId($this->id);
            $this->attributes[$attribute->code] = $avModel;
        }
        $this->entity_type_id = $entity_type_id;                        
    }
    
    /**
     * Binds the entity definition model to the given id.
     * @param type $id
     */
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
    
    /**
     * Gets content of entity definition model.
     * @return type
     */
    public function getData()
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,       
            'entity_type_id' => $this->entity_type_id,
        ];
        
        if(isset($this->attributes))
        {
            $data['attributes'] = array();
            foreach($this->attributes as $attribute)
            {
                $data['attributes'][$attribute->code] = $attribute->getData();
            }
        }
        return $data;
    }
    
    /**
     * Sets content of entity definition model.
     * @param type $data
     */
    public function setData($data)
    {
        if(isset($data['id']))
        {
            $this->id = $data['id'];
        }
        
        if(isset($data['entity_type_id']))
        {
            $this->entity_type_id = $data['entity_type_id'];
        }
        if(isset($data['name']))
        {
            $this->name = $data['name'];
        }
        if(isset($data['code']))
        {
            $this->code = $data['code'];
        }
        if(isset($data['description']))
        {
            $this->description = $data['description'];
        }
        
        if(isset($data['attributes']))
        {
            $this->attributes = array();
            foreach($data['attributes'] as $attributeData)
            {
                $aModel = new AttributeValueModel($this->dbAdapter);
                $aModel->setData($attributeData);
                $this->attributes[$aModel->code] = $aModel;
            }
        }
                    
    }
    
    /**
     * Gets the attribute value collection.
     * @return type
     */
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
            'select',
        ];
        
        foreach($types as $type)
        {
            $table = "entity_definition_value_".$type;
            $select = $this->sql->select();
            $select->from($table)
                   ->columns(['value_id' => 'id', 'attribute_id', 'value'])
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
                    if($attribute->type == 'select')
                    {
                        $attribute->optionValues = array();
                        $optionsTable = new \Zend\Db\TableGateway\TableGateway('attribute_option_values', $this->dbAdapter);
                        $select = $this->sql->select();
                        $select->columns(['value', 'text'])
                               ->where(['attribute_id' => $attribute->id]);
                        $options = $optionsTable->select($select);
                        foreach($options as $option)
                        {
                            $attribute->optionValues[$option['value']] = $option['text'];
                        }
                    }
                    
                    $this->attributes[$attribute->code] = $attribute;
                } while ($results->next());     
            }                                
        }
        
        return $this->attributes;
    }
    
    /**
     * Saves the entity definition data together with attribute value data.
     */
    public function save()
    {
        \Zend\Debug\Debug::dump("About to be saved...");
        \Zend\Debug\Debug::dump($this->getData());
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
                    ->values([
                        'code' => $this->code,
                        'name' => $this->name,
                        'description' => $this->description,  
                        'entity_type_id' => $this->entity_type_id,
                    ]);
            $statement = $this->sql->prepareStatementForSqlObject($insert);
            $statement->execute();
            $select = $this->sql->select();
            $select->from('entity_definition')
                   ->order('id DESC')
                   ->limit(1);
            $statement = $this->sql->prepareStatementForSqlObject($select);
            $results = $statement->execute();
            $this->id = $results->current()['id'];
            
            if(isset($this->attributes))
            {
                foreach($this->attributes as $attribute)
                {
                    $attribute->setEntityDefinitionId($this->id);
                    $attribute->save();
                }
            }
        }
        
        if(count($this->attributesToDelete) > 0)
        {
            \Zend\Debug\Debug::dump("There is something to be deleted...");
            foreach($this->attributesToDelete as $key)
            {
                \Zend\Debug\Debug::dump("Deleting ... ".$key);
                $this->attributes[$key]->delete();
                unset($this->attributes[$key]);
            }
            
            $this->attributesToDelete = array();            
        }
        
        // Delete invalid entries.
        if(isset($this->oldAttributes))
        {
            foreach($this->oldAttributes as $oldAttribute)
            {
                $key = $oldAttribute->code;
                if(!array_key_exists($key, $this->attributes))
                {
                    $oldAttribute->delete();
                }
            }
        }
    }
    
    /**
     * Sets attribute value.
     * @param type $attributeCode
     * @param type $value
     */
    public function setAttributeValue($attributeCode, $value)
    {
        if(!array_key_exists($attributeCode, $this->attributes))
        {
            $this->addAttribute($attributeCode);
        }
        
        $attModel = $this->attributes[$attributeCode];
        $attModel->setValue($value);
    }
    
    /**
     * Gets the attribute value.
     * @param type $attributeCode
     * @return type
     */
    public function getAttributeValue($attributeCode)
    {
        if(array_key_exists($attributeCode, $this->attributes))
        {
            $attModel = $this->attributes[$attributeCode];
            return $attModel->setValue($value);
        }
    }
    
    /**
     * Adds attribute model.
     * @param type $attributeCode
     */
    public function addAttribute($attributeCode)
    {
        if(!array_key_exists($attributeCode, $this->attributes))
        {
            \Zend\Debug\Debug::dump('Wants to add attribute...' . $attributeCode);
            $attModel = new AttributeValueModel($this->dbAdapter);
            $attModel->setEntityDefinitionId($this->id);
            $select = $this->sql->select();
            $select->from('attribute')
                    ->where(['code' => $attributeCode]);
            $statement = $this->sql->prepareStatementForSqlObject($select);
            $results = $statement->execute();
            $row = $results->current();
            if(isset($row))
            {
                \Zend\Debug\Debug::dump($row);
                $attModel->setData($row);
                $this->attributes[$row['code']] = $attModel;
            }
        }
    }
    
    /** 
     * Deletes attribute model. It actually moves it to the collection of attributes, marked for the deletion.
     * Once when the save is called, these attributes will be deleted.
     * @param type $attributeCode
     */
    public function deleteAttribute($attributeCode)
    {
        \Zend\Debug\Debug::dump('want to delete attribute ... ' . $attributeCode);
        if(!array_key_exists($attributeCode, $this->attributesToDelete))
        {
            $this->attributesToDelete[$attributeCode] = $attributeCode;
        }
    }
    
    // These two are for the sake of compatibility with zend form-model relationship ex. $form->bind($model);$form->setData($data).
    /**
     * Returns the data content to outside.
     * @return type
     */
    public function getArrayCopy()
    {
        return $this->getData();
    }
    
    /**
     * Sets data content from outside.
     * @param type $data
     */
    public function exchangeArray($data)
    {
//        $this->setData($data);
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
//        $this->price = (isset($data['price'])) ? $data['price'] : null;
//        unset($data['price']);
//        $this->pax = (isset($data['pax'])) ? $data['pax'] : null;
//        unset($data['pax']);
 
        if(count($data))
        {
            foreach($data as $key=>$value)
            {
                if(isset($this->attributes) && array_key_exists($key, $this->attributes))
                {
                    $attribute = $this->attributes[$key];
                    $attribute->setValue($data[$key]);
                }
                else 
                {
                    $attribute = new AttributeValueModel($this->dbAdapter);
                    $attribute->setData($value);
                }
            }
        }        
    }
}

