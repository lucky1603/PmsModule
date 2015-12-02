<?php
/**
 * @name AttributeValueModel.php
 * @description Data model for attribute values.
 * @author Dragutin Jovanovic <gutindra@gmail.com>
 * @date 07.11.2015.
 */
namespace Pms\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;

/**
 * AttributeValueModel class.
 */
class AttributeValueModel
{
    public $id;
    public $label;
    public $code;
    public $type;
    public $sort_order;
    public $value;
    public $optionValues;
    public $unit;
    public $scope;
       
    protected $dbAdapter;
    protected $tableName;
    protected $value_id;
    protected $reference_id;
    protected $reference_field;
    protected $sql;
    protected $text;
    
    /**
     * Constructor.
     * @param Adapter $adapter
     * @param type $entityTableName
     */
    public function __construct(Adapter $adapter, $entityTableName='entity_definition', $reference_field='entity_definition_id') {
        $this->dbAdapter = $adapter;
        $this->tableName = $entityTableName;
        $this->reference_field = $reference_field;
        $this->sql = new Sql($this->dbAdapter);
    }
    
    /**
     * Sets the entity definition id from outside.
     * @param type $id
     */
    public function setReferenceId($id)
    {
        $this->reference_id = $id;
    }
    
    /**
     * Initialize from attribute model.
     * @param \Pms\Model\AttributeModel $attributeModel
     */
    public function from(AttributeModel $attributeModel)
    {
        $this->setData($attributeModel->getData());
    }
    
    /**
     * Connects model to the attribute with the given id.
     * @param type $id
     * @return type
     */
    public function setAttributeId($id)
    {
        if(!$this->reference_id)
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
    
    /**
     * Sets the object data.
     * @param type $data
     */
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
        
        if(isset($data['value_id']))
        {
            $this->value_id = $data['value_id'];
        }
        if(isset($data['code']))
        {
            $this->code = $data['code'];
        }
        if(isset($data['type']))
        {
            $this->type = $data['type'];
        }
        if(isset($data['sort_order']))
        {
            $this->sort_order = $data['sort_order'];
        }
        if(isset($data['unit']))
        {
            $this->unit = $data['unit'];
        }
        if(isset($data['label']))
        {
            $this->label = $data['label'];
        }
        if(isset($data['scope']))
        {
            $this->scope = $data['scope'];
        }        
        
        if(isset($data['option_values']))
        {
            $this->optionValues = $data['option_values'];            
        }
        
        if(isset($data['value']))
        {
           $this->value = $data['value'];
        }
        else 
        {
            if(isset($this->optionValues))
            {
                reset($this->optionValues);
                $first_key = key($this->optionValues);
                $this->value = $first_key;
                $this->text = $this->optionValues[$first_key];
            }
            
        }                              
    }
    
    /**
     * Gets the object data.
     * @return type
     */
    public function getData()
    {
        $data = [
            'id' => $this->id,
            'label' => $this->label,
            'code' => $this->code,
            'type' => $this->type,
            'sort_order' => $this->sort_order,        
            'value' => $this->value,
            'scope' => $this->scope,
            'unit'
        ];
        
        if(isset($this->optionValues))
        {
            $data['option_values'] = $this->optionValues;
        }
        return $data;
    }
    
    /**
     * Gets the attribute value.
     * @return type
     */
    public function getValue()
    {
        if(!isset($this->value))
        {
           $select = $this->sql->select(); 
           $select->from($this->tableName.'_value_'.$this->type)
                   ->columns(['value_id' => 'id', 'value'])
                   ->where([$this->reference_field => $this->reference_id]);
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
    
    /**
     * Gets the text for the given value.
     * @return string
     */
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
    
    /**
     * Sets value. TODO: Check if this is used at all?
     * @param type $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
    
    /**
     * Saves the attribute to the database.
     */
    public function save()
    {
        if(isset($this->value_id))
        {
            $update = $this->sql->update();
            $update->table($this->tableName.'_value_'.$this->type)
                    ->set([
                        'value' => $this->value,
                    ])
                    ->where([
                            'id' => $this->value_id,
                        ]);
            $statement = $this->sql->prepareStatementForSqlObject($update);
            $statement->execute();
        }
        else 
        {
            $insert = $this->sql->insert();
            $insert->into($this->tableName.'_value_'.$this->type)
                    ->values([
                        'attribute_id' => $this->id,
                        $this->reference_field => $this->reference_id,
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
    
    /**
     * Deletes the attribute from the database.
     * @return type
     */
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

