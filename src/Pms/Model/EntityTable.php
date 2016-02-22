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
            'status_id' => $entity->status_id,
        ];
        
        $id = (int) $entity->id;
        if($id == 0)
        {
            $this->tableGateway->insert($data);
        }
        else 
        {
            if($this->getEntity($id))
            {
                $this->tableGateway->update($data, ['id' => $id]);
            }
            else 
            {                
               throw new Exception("Entity doesn't exist!");            
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
    public function fetchView($typeId = NULL, $user_id = NULL)
    {
        $dbAdapter = $this->tableGateway->getAdapter();
        $sql = new Sql($dbAdapter);
        $select = $sql->select();
        $select->from('entity')
                ->join(['e' => 'entity_definition'], 'definition_id = e.id', ['code', 'TypeId' => 'entity_type_id'])
                ->join(['s' => 'status'], 'status_id = s.id', ['SValue' => 'value'])
                ->join(['et' => 'entity_type'], 'e.entity_type_id=et.id', ['user_id']);

        $select->order(['guid ASC']);
        
        if(isset($typeId))
        {
            $select->where(['e.entity_type_id' => $typeId]);
        }
        
        if(isset($user_id))
        {
            $select->where(['et.user_id' => $user_id]);
        }
        
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
