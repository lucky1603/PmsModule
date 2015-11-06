<?php
/**
 * @name EntityDefinitionTable.php
 * @description Object model of the EntityDefinitionTable object.
 * @author Sinisa Ristic <sinisa.ristic@gmail.com>
 * @date 05.11.2015.
 */

namespace Pms\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Exception;

/**
 * Entity definition helper class.
 */
class EntityDefinitionTable 
{
    /**
     * Table gateway helper member.
     * @var type 
     */
    protected $tableGateway;
    
    /**
     * Constructor.
     * @param TableGateway $tableGateway
     */
    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    
    /**
     * Saves the object to the table.
     * @param \Pms\Model\EntityDefinition $entityDef
     * @throws Exception
     */
    public function saveEntityDefinition(EntityDefinition $entityDef)
    {
        $data = [
            'entity_type_id' => $entityDef->entity_type_id,
            'name' => $entityDef->name,
            'code' => $entityDef->code,                        
            'description' => $entityDef->description,
            'price' => $entityDef->price,
            'pax' => $entityDef->pax,
        ];
                
        $id = (int)$entityDef->id;
        
        if($id == 0) {
            $this->tableGateway->insert($data);
        }
        else {
            if($this->getEntityDefinition($id))
            {
                $this->tableGateway->update($data, ['id' => $id]);
            }
            else {
                throw new \Zend\Db\Exception("User IO doesn't exist!");
            }
        }    
    }
    
    /**
     * Gets attribute with the given id.
     * @param type $id
     * @return type
     * @throws Exception
     */
    public function getEntityDefinition($id)
    {
        $id = (int)$id;
        $resultSet = $this->tableGateway->select(['id' => $id]);
        $row = $resultSet->current();
        if(!$row)
        {
            throw new Exception("No entity definition with id = " . $id);
        }        
        return $row;
    }
    
    /**
     * Fetch all attributes.
     * @return type
     */
    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
    
    public function fetchView($id=NULL)
    {
        $dbAdapter = $this->tableGateway->getAdapter();        
        $sql = new \Zend\Db\Sql\Sql($dbAdapter);
        $select = $sql->select();
        $select->from(array('ed'  => 'entity_definition'))
                ->columns(['id', 'name', 'code', 'description'])
                ->join(array('et' => 'entity_type'), 'ed.entity_type_id = et.id', ['typename' => 'name']);
        if($id != NULL)
        {
            $select->where(array('ed.id' => $id));
        }                
                                        
        $statement = $sql->prepareStatementForSqlObject($select);
        $results1 = $statement->execute();
        
        $rows = array();
        do {
            $rows[] = $results1->current();
        } while($results1->next());
        
        return $rows;
    }
    
    public function deleteAttribute($id)
    {
        $this->tableGateway->delete(['id' => $id]);
    }
}

