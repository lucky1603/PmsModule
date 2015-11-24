<?php
/**
 * @name ReservationModel - Interface class to the reservations table in database.
 * @author Sinisa Ristic <sinisa.ristic@gmail.com>
 * @date 17.11.2015.
 */
namespace Pms\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use Pms\Model\ReservationEntityModel;


/**
 * ReservationModel class.
 */
class ReservationModel 
{
    public $id;
    public $reservation_id;
    public $status;
    public $created_at;
    public $modified_at;
    public $client_id;
    public $status_id;
//    public $pax_a;
//    public $pax_y;
//    public $pax_c;
    public $comment;
    public $clientName;
    public $reservedEntities;
            
    protected $dbAdapter;
    protected $sql;
    
    /**
     * Constructor.
     * @param Adapter $dbAdapter
     */
    public function __construct(Adapter $dbAdapter) {
        $this->dbAdapter = $dbAdapter;
        $this->sql = new Sql($dbAdapter);
    }
    
    /**
     * Initializes the reservation data for the given id.
     * @param type $id
     */
    public function setId($id)
    {
        $select = $this->sql->select();
        $select->from(['r' => 'reservations'])
                ->join(['c' => 'clients'], 'r.client_id = c.id', ['first_name', 'last_name'])
                ->join(['s' => 'reservation_status'], 'r.status_id = s.id',['real_status' => 'statustext'])
                ->where(['r.id' => $id]);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();        
        
        $row = $results->current();
        $this->setData($row);     
        $entities = $this->getReservedEntities();
    }
    
    /**
     * Sets the object data from outside..
     * @param type $data
     */
    public function setData($data)
    {
        if(isset($data['id']))
        {
            $this->id = $data['id'];
        }
        
        if(isset($data['reservation_id']))
        {
            $this->reservation_id = $data['reservation_id'];
        }
               
        $this->created_at = isset($data['created_at']) ? $data['created_at'] : null;
        $this->modified_at = isset($data['modified_at']) ? $data['modified_at'] : null;
        $this->client_id = isset($data['client_id']) ? $data['client_id'] : null;
        
        // Set Status
        if(isset($data['status_id']))
        {
            $this->status_id = isset($data['status_id']) ? $data['status_id'] : null;       
            $tableGateway = new TableGateway('reservation_status', $this->dbAdapter);
            $result = $tableGateway->select(['id' => $this->status_id]);
            $row = $result->current();
            $this->status = $row['statustext'];    
        }
        
        // set entities
        if(isset($data['reservedEntities']))
        {            
            $this->reservedEntities = array();
            foreach($data['reservedEntities'] as $entity)
            {
                $entityModel = new ReservationEntityModel($this->dbAdapter);
                $entityModel->setData($entity);
                $entityModel->setReservationId($this->reservation_id);
                $this->reservedEntities[$entityModel->internal_id] = $entityModel;
            }
        }                
    }
    
    /**
     * Return object data to outside.
     * @return type
     */
    public function getData()
    {
        $data =  [
            'reservation_id' => $this->reservation_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'modified_at' => $this->modified_at,
            'client_id' => $this->client_id,
            'status_id' => $this->status_id,
        ]; 
        
        if(isset($this->id))
        {
            $data['id'] = $this->id;
        }
        
        $data['reservedEntities'] = [];
        foreach($this->reservedEntities as $entity)
        {
            $data['reservedEntities'][$entity->internal_id] = $entity->getData();
        }
        
        return $data;
    }
    
    /**
     * Get entities associated with the reservation id.
     * @param type $id
     * @return type
     */
    public function getReservedEntities()
    {
        if(isset($this->reservedEntities))
        {
            return $this->reservedEntities;
        }
        $this->reservedEntities = array();
        if($this->id)
        {
            $select = $this->sql->select();
            $select->from(['r' => 'reservation_entity'])
                    ->join(['e' => 'entity'], 'r.entity_id = e.id', ['guid'])
                    ->join(['c' => 'clients'], 'r.guest_id = c.id', ['first_name', 'last_name'])
                    ->join(['ed' => 'entity_definition'], 'e.definition_id = ed.id', ['ed_code' => 'code', 'ed_name' => 'name'])
                    ->where(['r.reservation_id' => $this->id]);
            $statement = $this->sql->prepareStatementForSqlObject($select);
            $results = $statement->execute();
            foreach($results as $row)
            {
//                \Zend\Debug\Debug::dump($row);
                $reModel = new ReservationEntityModel($this->dbAdapter);
                $reModel->setData($row);
                $this->reservedEntities[$row['id']] = $reModel;
            }
        }
                
        return $this->reservedEntities;
    }
        
    /**
     * Adds entity model to the entity collection.
     * @param ReservationEntityModel $reModel
     */
    public function addEntity(ReservationEntityModel $reModel)
    {
        $this->reservedEntities[$reModel->internal_id] = $reModel;
    }
    
    /**
     * Removes entity model from the collection.
     * @param type $guid
     */
    public function deleteEntity($id)
    {
        unset($this->reservedEntities[$id]);
    }
    
    /**
     * Saves the reservation to database.
     */
    public function save()
    {
        // Save main table.
        if(!isset($this->id))
        {                   
            $dataToUpdate = $this->getData();
            unset($dataToUpdate['reservedEntities']);
            \Zend\Debug\Debug::dump($dataToUpdate);
            
            $insert = $this->sql->insert();
            $insert->into('reservations')
                    ->values($dataToUpdate);
            $statement = $this->sql->prepareStatementForSqlObject($insert);
            $statement->execute();                            
        }
        else 
        {
            \Zend\Debug\Debug::dump('id is '.$this->id);
            $dataToUpdate = $this->getData();
            unset($dataToUpdate['reservedEntities']);
            \Zend\Debug\Debug::dump($dataToUpdate);
            
            $update = $this->sql->update();
            $update->table('reservations')
                    ->set($dataToUpdate)
                    ->where(['id' => $this->id]);
            $statement = $this->sql->prepareStatementForSqlObject($update);
            $statement->execute();        
        }
                
        // Get belonging entities.
        $currentEntities = $this->getReservedEntities();
        
        // Get existing reservation entities from the database.
        $select = $this->sql->select();
        $select->from(['r' => 'reservation_entity'])
                ->join(['e' => 'entity'], 'r.entity_id = e.id', ['guid']);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $entities = array();
        $results = $statement->execute();
        
        // Find entries in the database, that don't exist
        // in the model anymore and mark their id's.
        $keysToDelete = array();
        foreach($results as $entity)
        {
            $id = $entity['id'];
            if(!array_key_exists($id, $currentEntities))
            {
                $keysToDelete[] = $entity['id'];
            }
        }
               
        // If there were any, delete them.
        if(count($keysToDelete) > 0)
        {
            $delete = $this->sql->delete();
            $delete->from('reservation_entity')
                    ->where(['id' => $keysToDelete]);
        }
                        
        // Now save the entities from the model. 
        if(null != $this->getReservedEntities())
        {
            foreach($this->reservedEntities as $rEntity)
            {
//                \Zend\Debug\Debug::dump($rEntity);
                $rEntity->save();
            }
        }
    }
    
    /**
     * Deletes the reservation from database.
     */
    public function delete()
    {
        
    }
    
    /**
     * Fetch all reservations.
     * @return type
     */
    public function fetchAll()
    {
        $select = $this->sql->select();
        $select->from(['r' => 'reservations'])
                ->join(['c' => 'clients'], 'r.client_id = c.id', ['first_name', 'last_name'])
                ->join(['s' => 'reservation_status'], 'r.status_id = s.id', ['statustext'])
                ->order(['reservation_id ASC']);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $results = $statement->execute(); 
        $rows = array();
        foreach($results as $row)
        {
            $rows[] = $row;
        }
        
        return $rows;
    }
    
    // These two methods are for the compatibility sake with form binding.
    
    /**
     * Sets the object data from outside.
     * @param type $data
     */
    public function exchangeArray($data)
    {
         $this->setData($data);
    }
    
    /**
     * Return object data to outside.
     * @return type
     */
    public function getArrayCopy()
    {
        return $this->getData();
    }
    
    public function getNewInternalId()
    {
        $id = 0;
        if(isset($this->reservedEntities))
        {
            $count = count($this->reservedEntities);
            $last = $this->reservedEntities[$count -1];
            $id = $last['internal_id'];
        }
        return ++$id;
    }
}
