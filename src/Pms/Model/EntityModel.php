<?php
/**
 * @name        EntityModel.php
 * @decription  Enables manipulation with all of the features conected to the 'entity' object
 *              as well as preview of the values belonging to the connected objects.
 * @author      Sinisa Ristic <sinisa.ristic@gmail.com>
 * @date        12.11.2015.
 */

namespace Pms\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;
use Zend\Debug\Debug;

/**
 * EntityModel class.
 */
class EntityModel
{
    public $id;
    public $guid;
    public $status;
    public $definition_id;
    public $status_id;
    public $entityDefinitionModel;
    public $attributes;
    
    protected $dbAdapter;
    protected $sql;
    
    /**
     * Constructor
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
    
    /**
     * Sets Id and initializes stuff connected with that id.
     * @param type $id
     */
    public function setId($id)
    {
        if(!isset($id))
        {
            return;
        }

        $id = (int)$id;
        $select = $this->sql->select();
        $select->from(['a' => 'entity'])
                ->join(['e' => 'entity_definition'], 'a.definition_id = e.id', ['code'])
                ->join(['s' => 'status'], 'a.status_id = s.id', ['SValue' => 'value'])
                ->where(['a.id' => $id]);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();
        $data = $results->current();

        // Set local data.
        $this->setData($data);
        
        // Update definition model.
        $this->getDefinitionModel()->setId($this->definition_id);
        
        // Init attribute values.
        $this->getAttributes();                
    }
    
    public function setEntityDefinitionId($definition_id)
    {
        $this->definition_id = $definition_id;
        $edefModel = new EntityDefinitionModel($this->dbAdapter);
        $edefModel->setId($definition_id);
        $typeId = $edefModel->entity_type_id;
        $etypeModel = new EntityTypeModel($this->dbAdapter);
        $etypeModel->setId($typeId);
        $attributes = $etypeModel->attributes;
        if(isset($attributes))
        {
            foreach($attributes as $attributeModel)
            {
                if($attributeModel->scope == 2)
                {
                    if(empty($this->attributes))
                    {
                        $this->attributes = array();
                    }
                    
                    $avalModel = new AttributeValueModel($this->dbAdapter, 'entity', 'entity_id');
                    $avalModel->from($attributeModel);
                    if(isset($this->id))
                    {
                        $avalModel->setReferenceId($this->id); 
                    }
                    $this->attributes[$avalModel->code] = $avalModel;
                }
            }
        }
    }
    
    /**
     * Sets the model data from outside.
     * @param type $data
     */
    public function setData($data)
    {
        if(isset($data['id']))
        {
            $this->id = $data['id'];
        }
        
        if(isset($data['definition_id']))
        {
            $this->definition_id = $data['definition_id'];
        }
        
        if(isset($data['guid']))
        {
            $this->guid = $data['guid'];
        }
        
        if(isset($data['status']))
        {
            $this->status = $data['status'];
        }
        
        if(isset($data['SValue']))
        {
            $this->status = $data['SValue'];
        }
        
        if(isset($data['status_id']))
        {
            $this->status_id = $data['status_id'];
        }            
                
        if(isset($data['attributes']))
        {
            $this->attributes = array();
            foreach($data['attributes'] as $attributeData)
            {
                $aModel = new AttributeValueModel($this->dbAdapter, 'entity', 'entity_id');
                $aModel->setData($attributeData);
                $this->attributes[$aModel->code] = $aModel;
            }
        }
    }
    
    /**
     * Returns the model data.
     * @return type
     */
    public function getData()
    {
        $data = [
            'id' => $this->id,
            'definition_id' => $this->definition_id,
            'guid' => $this->guid,
            'status' => $this->status,
            'status_id' => $this->status_id,
        ];
        
        if(isset($this->attributes))
        {
            $data['attributes'] = array();
            foreach($this->attributes as $attributeValueModel)
            {
                $data['attributes'][$attributeValueModel->code] = $attributeValueModel->getData();
            }
        }
        
        return $data;
    }
    /**
     * Returns the model of the belonging definition type.
     * @return type
     */
    public function getDefinitionModel()
    {
        if(isset($this->entityDefinitionModel))
        {
            return $this->entityDefinitionModel;
        }     
        
        $this->entityDefinitionModel = new EntityDefinitionModel($this->dbAdapter);
        if(isset($this->definition_id))
        {
            $this->entityDefinitionModel->setId($this->definition_id);
        }  
        
        return $this->entityDefinitionModel;
    }
    
    /**
     * Gets the attribute collection for the object.
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
            $table = "entity_value_".$type;
            $select = $this->sql->select();
            $select->from($table)
                   ->columns(['value_id' => 'id', 'attribute_id', 'value'])
                   ->join(['a' => 'attribute'], 'attribute_id = a.id', ['*'])
                   ->where(['entity_id' => $this->id,
                            'a.scope' => 2]);
            $statement = $this->sql->prepareStatementForSqlObject($select);
            $results = $statement->execute();
            
            if($results->count() > 0)
            {
                do 
                {
                    $row = $results->current();
                    $attribute = new AttributeValueModel($this->dbAdapter, 'entity', 'entity_id');
                    $attribute->setReferenceId($this->id);                    
                    $attribute->setData($row);
                    if($attribute->type == 'select')
                    {
                        $attribute->optionValues = array();
                        $select = $this->sql->select();
                        $select->from('attribute_option_values')
                                ->columns(['value', 'text'])
                                ->where(['attribute_id' => $attribute->id]);
                        $statement = $this->sql->prepareStatementForSqlObject($select);
                        $options = $statement->execute();
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
    
    public function save()
    {
        $dataToSave = $this->getData();        
        unset($dataToSave['id']);
        unset($dataToSave['attributes']);
        if(isset($this->id))
        {
            $update = $this->sql->update();
            $update->table('entity')
//                    ->set([
//                            'status' => $this->status,
//                            'guid' => $this->guid,
//                            'status_id' => $this->status_id,  
//                            'entity_type_id' => $this->entity_type_id,
//                        ])
                    ->set($dataToSave)
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
            $insert->into('entity')
                    ->values($dataToSave);
//                    ->values([
//                        'code' => $this->code,
//                        'name' => $this->name,
//                        'description' => $this->description,  
//                        'entity_type_id' => $this->entity_type_id,
//                    ]);
            $statement = $this->sql->prepareStatementForSqlObject($insert);
            $statement->execute();
            $select = $this->sql->select();
            $select->from('entity')
                   ->order('id DESC')
                   ->limit(1);
            $statement = $this->sql->prepareStatementForSqlObject($select);
            $results = $statement->execute();
            $this->id = $results->current()['id'];
            
            if(isset($this->attributes))
            {
                foreach($this->attributes as $attribute)
                {
                    $attribute->setReferenceId($this->id);
                    $attribute->save();
                }
            }
        }
    }
    
    // Model interface for binding with forms.
    public function exchangeArray($data)
    {
//        $this->setData($data);
        if(isset($data['id']))
        {
            $this->id = $data['id'];
            unset($data['id']);
        }
        
        if(isset($data['definition_id']))
        {
            $this->definition_id = $data['definition_id'];
            unset($data['definition_id']);
        }
        
        if(isset($data['guid']))
        {
            $this->guid = $data['guid'];
            unset($data['guid']);
        }
        
        if(isset($data['status']))
        {
            $this->status = $data['status'];
            unset($data['status']);
        }
        
        if(isset($data['status_id']))
        {
            $this->status_id = $data['status_id'];
            unset($data['status_id']);
        }  
        
        if(count($data) > 0)
        {
            foreach($data as $key=>$value)
            {
                if(isset($this->attributes) && array_key_exists($key, $this->attributes))
                {
                    $attribute = $this->attributes[$key];
                    $attribute->setValue($data[$key]);
                }
//                else 
//                {
//                    $attribute = new AttributeValueModel($this->dbAdapter);
//                    $attribute->setData($value);
//                }
            }
        }
        
    }
    
    public function getArrayCopy()
    {
        $data = [            
//            'id' => $this->id,
//            'definition_id' => $this->definition_id,
            'guid' => $this->guid,
            'status' => $this->status,
            'status_id' => $this->status_id,    
        ];
        
        if($this->attributes)
        {
            foreach($this->attributes as $attributeModel)
            {
                $data[$attributeModel->code] = $attributeModel->getData();
            }
        }
        
        return $data;
    }
}
