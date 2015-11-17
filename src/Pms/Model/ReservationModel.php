<?php
/**
 * @name ReservationModel - Interface class to the reservations table in database.
 * @author Sinisa Ristic <sinisa.ristic@gmail.com>
 * @date 17.11.2015.
 */
namespace Pms\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;

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
                ->where(['r.id' => $id]);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();        
        
        $row = $results->current();
//        \Zend\Debug\Debug::dump($row);
        $this->setData($row);        
        $entities = $this->getReservedEntities($id);
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
        
        $this->reservation_id = isset($data['reservation_id']) ? $data['reservation_id'] : null;
        $this->status = isset($data['status']) ? $data['status'] : null;
        $this->created_at = isset($data['created_at']) ? $data['created_at'] : null;
        $this->modified_at = isset($data['modified_at']) ? $data['modified_at'] : null;
        $this->client_id = isset($data['client_id']) ? $data['client_id'] : null;
        $this->status_id = isset($data['status_id']) ? $data['status_id'] : null;       
    }
    
    /**
     * Return object data to outside.
     * @return type
     */
    public function getData()
    {
        return [
            'reservation_id' => $this->reservation_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'modified_at' => $this->modified_at,
            'client_id' => $this->client_id,
            'status_id' => $this->status_id,
        ]; 
    }
    
    /**
     * Get entities associated with the reservation id.
     * @param type $id
     * @return type
     */
    public function getReservedEntities($id)
    {
        if(isset($this->reservedEntities))
        {
            return $this->reservedEntities;
        }
        
        $this->reservedEntities = array();
        $select = $this->sql->select();
        $select->from(['r' => 'reservation_entity'])
                ->join(['e' => 'entity'], 'r.entity_id = e.id', ['guid'])
                ->join(['c' => 'clients'], 'r.guest_id = c.id', ['first_name', 'last_name'])
                ->join(['ed' => 'entity_definition'], 'e.definition_id = ed.id', ['code'])
                ->where(['r.reservation_id' => $id]);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();
        foreach($results as $row)
        {
            $this->reservedEntities[$row['guid']] = $row;
        }
        
        return $this->reservedEntities;
    }
    
    /**
     * Fetch all reservations.
     * @return type
     */
    public function fetchAll()
    {
        $select = $this->sql->select();
        $select->from(['r' => 'reservations'])
                ->join(['c' => 'clients'], 'r.client_id = c.id', ['first_name', 'last_name']);
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
}
