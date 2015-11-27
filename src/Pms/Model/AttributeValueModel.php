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
    public $optionValues;
       
    protected $dbAdapter;
    protected $tableName;
    protected $value_id;
    protected $entity_definition_id;
    protected $sql;
    protected $text;
    
    public function __construct(Adapter $adapter, $entityTableName='entity_definition') {
        $this->dbAdapter = $adapter;
        $this->tableName = $entityTableName;
        $this->sql = new Sql($this->dbAdapter);
    }
    
    public function setEntityDefinitionId($id)
    {
        $this->entity_definition_id = $id;
    }
    
    /**
     * Initialize from attribute model.
     * @param \Pms\Model\AttributeModel $attributeModel
     */
    public function from(AttributeModel $attributeModel)
    {
        $this->setData($attributeModel->getData());
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
        if(isset($data['attribute_id']))
        {
            $this->id = $data['attribute_id'];
        }
        
        if(isset($data['id']))
        {
            $this->id = $data['id'];
        }
        $this->value_id = (isset($data['value_id'])) ? $data['value_id'] : null;
        $this->code = (isset($data['code'])) ? $data['code'] : null;
        $this->type = (isset($data['type'])) ? $data['type'] : null;
        $this->sort_order = (isset($data['sort_order'])) ? $data['sort_order'] : null;
        $this->unit = (isset($data['unit'])) ? $data['unit'] : null;
        $this->label = (isset($data['label'])) ? $data['label'] : null;
        if(isset($data['value']))
        {
           $this->value = $data['value'];
        }
        if(isset($data['option_values']))
        {
            $this->optionValues = $data['option_values'];            
        }
        
        if(isset($this->optionValues))
        {
            $this->text = $this->optionValues[$this->value];
        }                
    }
    
    public function getData()
    {
        $data = [
            'id' => $this->id,
            'label' => $this->label,
            'code' => $this->code,
            'type' => $this->type,
            'sort_order' => $this->sort_order,        
            'value' => $this->value,
        ];
        
        if(isset($this->optionValues))
        {
            $data['option_values'] = $this->optionValues;
        }
        return $data;
    }
    
    public function getValue()
    {
        if(!isset($this->value))
        {
           $select = $this->sql->select(); 
           $select->from($this->tableName.'_value_'.$this->type)
                   ->columns(['value_id' => 'id', 'value'])
                   ->where(['entity_definition_id' => $this->entity_definition_id]);
           $statement = $this->sql->prepareStatementForSqlObject($select);
           $results = $statement->execute();
           $row = $results->current();
           if(isset($row))
           {
               $this->value = $row['value'];
               $this->value_id = $row['value_id'];
           }
        }
        
        return $this->value;
    }
    
    public function getText()
    {
        if($this->type == 'select')
        {
            return $this->optionValues[$this->value];
        }
        else 
        {
            return 'Error';
        }
    }
    
    public function setValue($value)
    {
        $this->value = $value;
    }
    
    public function getCollection()
    {
        return $this->rows;
    }
    
//    public function getAttributes($id, $datatype)
//    {
//        $sql = new Sql($this->dbAdapter);
//        $select = $sql->select();
//        $select->from(['u' => $this->tableName.'_value_'.$datatype])
//                ->columns(['id' => 'value_id', 'entity_definition_id', 'value'])
//                ->join([
//                            'a' => 'attribute'
//                        ], 
//                        'u.attribute_id = a.id', 
//                        [
//                            'code',
//                            'label',
//                            'type',
//                            'sort_order',
//                            'unit',
//                        ]
//                )
//                ->where(['entity_definition_id' => $id]);
//             
//        $statement = $sql->prepareStatementForSqlObject($select);
//        $results = $statement->execute();        
//        $rows = array();
//        do {
//            $rows[] = $results->current();
//        } while($results->next());        
//        return $rows;
//    }
    
    public function save()
    {
        if(isset($this->value_id))
        {
//            \Zend\Debug\Debug::dump('AtModel:Save:Update');            
//            \Zend\Debug\Debug::dump($this->value_id);
            
            $update = $this->sql->update();
            $update->table($this->tableName.'_value_'.$this->type)
                    ->set([
                        'value' => $this->value,
                    ])
                    ->where([
//                            'attribute_id' => $this->id,
//                            'entity_definition_id' => $this->entity_definition_id,
                            'id' => $this->value_id,
                        ]);
            $statement = $this->sql->prepareStatementForSqlObject($update);
            $statement->execute();
        }
        else 
        {
//            \Zend\Debug\Debug::dump('AtModel:Save:Insert');

            $insert = $this->sql->insert();
            $insert->into($this->tableName.'_value_'.$this->type)
                    ->values([
                        'attribute_id' => $this->id,
                        'entity_definition_id' => $this->entity_definition_id,
                        'value' => $this->value,
                    ]);
            $statement = $this->sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $select = $this->sql->select();
            $select->from($this->tableName.'_value_'.$this->type)
                   ->order('id DESC')
                   ->limit(1);
            $statement = $this->sql->prepareStatementForSqlObject($select);
            $results = $statement->execute();
            $this->value_id = $results->current()['id'];
        }
    }
    
    public function delete()
    {
        \Zend\Debug\Debug::dump("Entered delete...value_id=".$this->value_id);
        if(!isset($this->value_id))
            return;
        $delete = $this->sql->delete();
        $table = $this->tableName.'_value_'.$this->type;
        $delete->from($table)
                ->where(['id' => $this->value_id]);
        $statement = $this->sql->prepareStatementForSqlObject($delete);
        $statement->execute();
        unset($this->value_id);
    }
        
}

