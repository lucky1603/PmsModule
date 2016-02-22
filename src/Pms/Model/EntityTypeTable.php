<?php
/**
 * @name EntityTypeTable.php
 * @description Object model of the EntityTypeTable object.
 * @author Sinisa Ristic <sinisa.ristic@gmail.com>
 * @date 03.11.2015.
 */

namespace Pms\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Exception;

/**
 * EntityTypeTable class.
 */
class EntityTypeTable 
{
    /**
     * Table gateway helper object.
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
     * Saves the entity type to the table.
     * @param EntityType $entityType
     * @throws Exception
     */
    public function saveEntityType(EntityType $entityType)
    {
        $data = [
            'name' => $entityType->name,
            'description' => $entityType->description,
        ];
        
        $id = (int)$entityType->id;        
        if($id == 0) {
            \Zend\Debug\Debug::dump('insert...');
            \Zend\Debug\Debug::dump($data);
            $this->tableGateway->insert($data);
        }
        else {
            if($this->getEntityType($id))
            {
                $this->tableGateway->update($data, ['id' => $id]);
            }
            else {
                throw new \Zend\Db\Exception("User IO doesn't exist!");
            }
        }        
    }
    
    /**
     * Gets the entity type based on the given id.
     * @param type $id
     * @return type
     * @throws Exception
     */
    public function getEntityType($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row = $rowset->current();
        if(!$row) {
            throw new \Zend\Db\Exception("Couldn't find row!");
        }
        return $row;
    }
    
    /**
     * Fetch all rows.
     * @return type
     */
    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
    
    /**
     * Fetch all entity types for the the given user.
     * @param type $user_id
     * @return type
     */
    public function fetchForUser($user_id)
    {
        $resultSet = $this->tableGateway->select(['user_id' => $user_id]);
        return $resultSet;
    }
    
    /**
     * 
     * @param type $userMail
     * @return type
     * @throws Exception
     */
    public function getEntityTypeByName($name)
    {
        $resultSet = $this->tableGateway->select(['name' => $name]);
        $row = $resultSet->current();
        if(! $row) {
            throw new Exception("Couldn't find row with " . $name . " name.");
        }
        return $row;
    }
    
    /**
     * Deletes the user
     * @param type $id
     */
    public function deleteEntityType($id)
    {
        $this->tableGateway->delete(['id' => $id]);
    }        
}

