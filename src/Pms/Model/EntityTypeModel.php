<?php
/**
 * @name EntityTypeModel.php
 * @description Data model for entity type. 
 * @author Dragutin Jovanovic <gutindra@gmail.com>
 * @date 26.11.2015.
 */

namespace Pms\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Delete;

/**
 * EntityTypeModel class.
 */
class EntityTypeModel
{
    public $id;
    public $name;
    public $description;
    public $attributes;
    
    protected $sql;   
    protected $dbAdapter;
    protected $serviceLocator;
    protected $lastInternalId = -1;
    
    /**
     * Constructor.
     * @param Adapter $dbAdapter
     */
    public function __construct(Adapter $dbAdapter) {
        $this->dbAdapter = $dbAdapter;
        $this->sql = new Sql($dbAdapter);
    }
    
    /**
     * Initializes entity type model with entity type id.
     * @param type $id
     */
    public function setId($id)
    {
        $this->lastInternalId = -1;
        $adapter = $this->dbAdapter;
        $tableGateway = new \Zend\Db\TableGateway\TableGateway('entity_type', $this->dbAdapter);
        $results = $tableGateway->select(['id' => $id]);
        $row = $results->current();
        $this->setData($row);
        
        // Now set attributes
        $attTable = new \Zend\Db\TableGateway\TableGateway('entity_type_attribute', $this->dbAdapter);
        $resultset = $attTable->select();
        foreach($resultset as $row)
        {
            if($row['entity_type_id'] == $id)
            {
                if(empty($this->attributes))
                {
                    $this->attributes = array();
                }
                
                $attributeModel = new AttributeModel($this->dbAdapter);
                $attributeModel->setId($row['attribute_id']);
                $attributeModel->setEntityTypeId($row['entity_type_id']);
                $this->attributes[$attributeModel->internal_id] = $attributeModel;
                $this->lastInternalId = $attributeModel->internal_id;
            }
        }

    }
    
    /**
     * Sets the object data from outside.
     * @param type $data
     */
    public function setData($data)
    {
        $this->lastInternalId = -1;
        
        if(!empty($data['id']))
        {
            $this->id = $data['id'];
        }
        
        if(!empty($data['name']))
        {
            $this->name = $data['name'];
        }
        
        if(!empty($data['description']))
        {
            $this->description = $data['description'];
        }
        
        if(!empty($data['attributes']))
        {
            $this->attributes = array();
            $attributes = $data['attributes'];
            foreach($attributes as $attributeData)
            {               
                $attribute = new AttributeModel($this->dbAdapter);
                $attribute->setData($attributeData);                
                if(empty($attribute->internal_id))
                {
                    $attribute->internal_id = ++$this->lastInternalId;
                }
                else 
                {
                    $this->lastInternalId = $attribute->internal_id;
                }                
                $this->attributes[$attribute->internal_id] = $attribute;
            }
        }
    }

    /**
     * Returns the object data to the outside world.
     * @return type
     */
    public function getData()
    {
        $data = [
            'name' => $this->name,
            'description' => $this->description,            
        ];
        
        if(isset($this->id))
        {
            $data['id'] = $this->id;
        }
        
        if(isset($this->attributes))
        {
            $data['attributes'] = array();
            foreach($this->attributes as $attribute)
            {
                $data['attributes'][$attribute->internal_id] = $attribute->getData();                
            }
        }
        
        return $data;
    }
    
    /**
     * Adds new attribute model to the entity type model.
     * @param \Pms\Model\AttributeModel $attribute
     */
    public function addAttribute(AttributeModel $attribute)
    {
        if(empty($this->attributes))
        {
            $this->attributes = array();
        }
        
        if(empty($attribute->internal_id))
        {
            $attribute->internal_id = ++$this->lastInternalId;
        }
        else 
        {
            $this->lastInternalId = $attribute->internal_id;
        }
                
        $this->attributes[$attribute->internal_id] = $attribute;
    }
    
    /**
     * Removes the attribute type model from entity model.
     * @param type $id
     */
    public function removeAttribute($id)
    {
        if(isset($this->attributes))
        {
            unset($this->attributes[$id]);
        }        
    }
    
    /**
     * Saves the model data to the database.
     */
    public function save()
    {
        $dataToUpdate = $this->getData();
        unset($dataToUpdate['attributes']);
        $table = new \Zend\Db\TableGateway\TableGateway("entity_type", $this->dbAdapter);
        
        if(isset($this->id))
        {
            // update
            unset($dataToUpdate['id']);       
            $update = $this->sql->update();
            $update->table('entity_type')
                    ->set($dataToUpdate)
                    ->where->equalTo('id', $this->id);
            $statement = $this->sql->prepareStatementForSqlObject($update);
            $statement->execute();
        }
        else 
        {
            // insert
            unset($dataToUpdate['attributes']);
            $table->insert($dataToUpdate);
            
            $select = $this->sql->select();
            $select->from('entity_type')
                    ->order('id DESC')
                    ->limit(1);
            $results = $table->selectWith($select);
            $this->id = $results->current()['id'];
        }
        
        if(isset($this->attributes))
        {
            // first delete non existing keys in model.
            $table = new \Zend\Db\TableGateway\TableGateway('entity_type_attribute', $this->dbAdapter);
            $resultset = $table->select(['entity_type_id' => $this->id]);
            $deleteAttributes = array();
            foreach($resultset as $row)
            {
                if(!array_key_exists($row['attribute_id'], $this->attributes))
                {
                    $deleteAttributes[] = $row['id'];
                }
            }
            if(count($deleteAttributes) > 0)
            {
                \Zend\Debug\Debug::dump("The following keys will be deleted...");
                \Zend\Debug\Debug::dump($deleteAttributes);            
                $delete = $this->sql->delete();
                $delete->where->in('id', $deleteAttributes)
                        ->equalTo('entity_type_id', $this->id);            
                $table->delete($delete);
            }
                        
            foreach($this->attributes as $attributeModel)
            {
                $attributeModel->save();
                
                // Check entity_type_attribute table.
                $data = [
                    'attribute_id' => $attributeModel->id,
                    'entity_type_id' => $this->id,
                ];
                $results = $table->select($data);
                if($results->count() == 0)
                {
                    $table->insert($data);
                }
            }
        }
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

}