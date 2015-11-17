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
        $entities = $this->getReservedEntities($id);
        $ownData = array();

        return [
            'fields' => $results->current(),
            'entities' => $entities,
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
}
