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
    public $comment;
    public $clientName;
    public $reservedEntities;
            
    protected $dbAdapter;
    protected $sql;
    protected $max_entity_id = 0;
    
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
            $this->max_entity_id = 0;
            $this->reservedEntities = array();
            foreach($data['reservedEntities'] as $entity)
            {
                $entityModel = new ReservationEntityModel($this->dbAdapter);
                $entityModel->setData($entity);
                $entityModel->setReservationId($this->reservation_id);
                $this->reservedEntities[$entityModel->internal_id] = $entityModel;
                if($entityModel->internal_id > $this->max_entity_id)
                {
                    $this->max_entity_id = $entityModel->internal_id;
                }
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
        $this->getReservedEntities();
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
                    ->join(['et' => 'entity_type'], 'ed.entity_type_id=et.id', ['time_resolution'])
                    ->where(['r.reservation_id' => $this->id]);
            $statement = $this->sql->prepareStatementForSqlObject($select);
            $results = $statement->execute();
            foreach($results as $row)
            {
                $reModel = new ReservationEntityModel($this->dbAdapter);
                $reModel->setData($row);
                $this->reservedEntities[$row['id']] = $reModel;
                if($reModel->internal_id > $this->max_entity_id)
                {
                    $this->max_entity_id = $reModel->internal_id;
                }
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
        if(empty($reModel->internal_id))
        {
            $this->max_entity_id++;
            $reModel->internal_id = $this->max_entity_id;
        }
        
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
            
            // Unse entity collection, the table doesn't recognize it.
            unset($dataToUpdate['reservedEntities']);
            
            // Set mandatory entry values.
            $dataToUpdate['created_at'] = date('m/d/Y', time());
            $dataToUpdate['modified_at'] = date('m/d/Y', time());
            
            // Now insert.
//            $insert = $this->sql->insert();
//            $insert->into('reservations')
//                    ->values($dataToUpdate);
//            $statement = $this->sql->prepareStatementForSqlObject($insert);
//            $statement->execute();       
            
            // Now get the id of inserted row.
            $table = new TableGateway('reservations', $this->dbAdapter, null, null);
            $table->insert($dataToUpdate);
            //$this->id = $table->getLastInsertValue();         
                        
            $select = $this->sql->select();
            $select->from('reservations')
                    ->order(['id DESC'])
                    ->limit(1);
            $statement = $this->sql->prepareStatementForSqlObject($select);
            $results = $statement->execute();
            $row = $results->current();
            $this->id = $row['id'];
//            \Zend\Debug\Debug::dump("Row is...");
//            \Zend\Debug\Debug::dump($row);
//            \Zend\Debug\Debug::dump($this->id);
        }
        else 
        {
            $dataToUpdate = $this->getData();
            
            // Unse entity collection, the table doesn't recognize it.
            unset($dataToUpdate['reservedEntities']);
            
            // Set mandatory entry values.
//            $dataToUpdate['modified_at'] = date('m/d/Y', time());
            $dataToUpdate['modified_at'] = date('Y-m-d', time());
            
            // Now update.
            $update = $this->sql->update();
            $update->table('reservations')
                    ->set($dataToUpdate)
                    ->where(['id' => $this->id]);
            $statement = $this->sql->prepareStatementForSqlObject($update);
            $statement->execute();        
        }
                
        // Get belonging entities.
        $currentEntities = $this->getReservedEntities();
        \Zend\Debug\Debug::dump('currentEntities');
        foreach($currentEntities as $entity)
        {
            \Zend\Debug\Debug::dump($entity->internal_id);
        }
        
        if(isset($this->id))
        {
            // Get existing reservation entities from the database.
            $select = $this->sql->select();
            $select->from(['r' => 'reservation_entity'])
                    ->join(['e' => 'entity'], 'r.entity_id = e.id', ['guid'])
                    ->where(['reservation_id' => $this->id]);
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

            \Zend\Debug\Debug::dump("Keys to be deleted ... ");
            \Zend\Debug\Debug::dump($keysToDelete);

            // If there were any, delete them.
            if(count($keysToDelete) > 0)
            {
                $delete = $this->sql->delete();
                $delete->from('reservation_entity')
                        ->where->in('id', $keysToDelete);
                $statement = $this->sql->prepareStatementForSqlObject($delete);
                $statement->execute();
            }
        }
        
//        \Zend\Debug\Debug::dump('id is ..');
//        \Zend\Debug\Debug::dump($some_id);
                        

        \Zend\Debug\Debug::dump($this->id);
        // Now save the entities from the model. 
        if(null != $this->getReservedEntities())
        {
            foreach($this->reservedEntities as $rEntity)
            {
                $rEntity->setReservationId($this->id);
                $rEntity->save();
            }
        }
    }
    
    /**
     * Deletes the reservation from database.
     */
    public function delete($id)
    {
        if(!isset($id))
        {
            return;
        }
        
        $table = new TableGateway('reservations', $this->dbAdapter, null, null);
        $table->delete(['id' => $id]);
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
    
    /**
     * Calculates new internal id for the reserved entities.
     * @return type
     */
    public function getNewInternalId()
    {   
        $retVal = $this->max_entity_id + 1;
        return $retVal;
    }
    
    /**
     * Generates reservation id, which will be visible to user.
     */
    protected function generateReservationID()
    {
        $this->reservation_id = sprintf("%'.010d\n", $broj);
    }
}
