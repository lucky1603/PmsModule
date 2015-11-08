<?php

namespace Pms\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;

class AttributeModel
{
    protected $dbAdapter;
    protected $tableName;
    protected $rows;
    
    public function __construct(Adapter $adapter, $entityTableName='entity_definition') {
        $this->dbAdapter = $adapter;
        $this->tableName = $entityTableName;
    }
    
    public function setId($id)
    {
        
    }
    
    public function setRefId($id)
    {
        $rows = $this->getAttributes($id, 'double');
        if($rows != FALSE)
            $this->rows = $rows;
        $rows = $this->getAttributes($id, 'boolean');
        if($rows != FALSE)
            $this->rows = array_merge($this->rows, $rows);
        $rows = $this->getAttributes($id, 'integer');
        if($rows != FALSE)
            $this->rows = array_merge($this->rows, $rows);
        $rows = $this->getAttributes($id, 'character');
        if($rows != FALSE)
            $this->rows = array_merge($this->rows, $rows);
        $rows = $this->getAttributes($id, 'text');
        if($rows != FALSE)
            $this->rows = array_merge($this->rows, $rows);
        $rows = $this->getAttributes($id, 'timestamp');
        if($rows != FALSE)
            $this->rows = array_merge($this->rows, $rows);                
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

