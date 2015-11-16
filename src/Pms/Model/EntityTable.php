<?php
/**
 * @name EntityTable.php
 * @description Object model of the EntityTable object.
 * @author Sinisa Ristic <sinisa.ristic@gmail.com>
 * @date 11.11.2015.
 */

namespace Pms\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Exception;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;

/**
 * EntityTable class.
 */
class EntityTable 
{
    protected $tableGateway;
    
    /**
     * Constructor.
     * @param TableGateway $tableGateway
     */
    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    
    /**
     * Saves entity to the database.
     * @param \Pms\Model\Entity $entity
     * @throws Exception
     */
    public function saveEntity(Entity $entity)
    {
        $data = [
            'definition_id' => $entity->definition_id,
            'status' => $entity->status,
            'guid' => $entity->guid,
        ];
        
        $id = (int) $entity->id;
        if($id == 0)
        {
            \Zend\Debug\Debug::dump("inserting ...");
            $this->tableGateway->insert($data);
        }
        else 
        {
            \Zend\Debug\Debug::dump("updating ...");
            if($this->getEntity($id))
            {
                $this->tableGateway->update($data, ['id' => $id]);
            }
            else 
            {                
               throw new Exception("User IO doesn't exist!");            
            }
        }
    }
    
    /**
     * Fetches entity with the given id.
     * @param type $id
     * @throws Exception
     */
    public function getEntity($id)
    {
        $id=(int)$id;
        $resultSet = $this->tableGateway->select(['id' => $id]);
        $row = $resultSet->current();
        if(!$row)
        {
            throw new Exception('Entity '.$id.' not found!');
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
    
    /**
     * Fetches combined view of the own table joined to the definition table, in order to get the definition code.
     * @return type
     */
    public function fetchView()
    {
        $dbAdapter = $this->tableGateway->getAdapter();
        $sql = new Sql($dbAdapter);
        $select = $sql->select();
        $select->from('entity')
                ->join(['e' => 'entity_definition'], 'definition_id = e.id', ['code'])
                ->join(['s' => 'status'], 'status_id = s.id', ['SValue' => 'value'])
                ->order(['guid ASC']);
        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();
        return $results;
    }
    
    /**
     * Deletes entity with given id.
     * @param type $id
     */
    public function deleteEntity($id)
    {
        $this->tableGateway->delete(['id' => $id]);
    }
}
