<?php
/**
 * @name ReservationEntityModel.php
 * @description Data model which handles the entities associated with a reservation.
 * @author Dragutin Jovanovic <gutindra.gmail.com>
 * @date 18.11.2015.
 */
namespace Pms\Model;

use Zend\Debug\Debug;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;

/**
 * ReservationEntityModel class.
 */
class ReservationEntityModel 
{
    public $id;
    public $guest_id;
    public $entity_id;
    public $reservation_id;
    public $date_from;
    public $date_to;
    public $first_name;
    public $last_name;
    public $ed_code;
    public $ed_name;
    public $guid;
    public $entity_definition_id;
    public $time_resolution;
    
    public $internal_id;
    
    protected $sql;
    
    /**
     * Constructor.
     * @param Adapter $adapter
     * @param type $id
     */
    public function __construct(Adapter $adapter, $id=NULL) {
        $this->sql = new Sql($adapter);
        if(isset($id))
        {
            $this->setId($id);
        }
    }
    
    public function setInternalId($id)
    {
        $this->internal_id = $id;
    }
    
    /**
     * Sets the reffering reservation id.
     * @param type $reservationId
     */
    public function setReservationId($reservationId)
    {
        $this->reservation_id = $reservationId;
    }
    
    public function getDuration()
    {
        $time_from = strtotime($this->date_from);
        $time_to = strtotime($this->date_to);
                
        $retval = array();
        switch($this->time_resolution)
        {
            case 1: /* hours */
                $retval['value'] = ($time_to - $time_from) / (60 * 60);
                $retval['type'] = 'hour(s)';
                break;
            default:
                $retval['value'] = ($time_to - $time_from) / (60 * 60 * 24);
                $retval['type'] = 'days(s)';
                break;
        }
        
        return $retval;
    }
        
    /**
     * Sets object data from outside.
     * @param type $id
     */
    public function setData($data)
    {
        if(isset($data['id']))
        {
            $this->id = $data['id'];
            $this->internal_id = $this->id;
        }
        else if(isset($data['internal_id']))
        {
            $this->internal_id = $data['internal_id'];
        }
                                
        $this->guest_id = isset($data['guest_id']) ? $data['guest_id'] : null;
        $this->entity_id = isset($data['entity_id']) ? $data['entity_id'] : null;
        $this->reservation_id = isset($data['reservation_id']) ? $data['reservation_id'] : null;
                
        if(isset($data['date_from']))
        {
            $this->date_from = $data['date_from'];
        }                
        if(isset($data['date_to']))
        {
            $this->date_to = $data['date_to'];
        }                
                             
        if(isset($data['guid']))
        {
            $this->guid = $data['guid'];
        }
        
        if(isset($data['time_resolution']))
        {
            $this->time_resolution = $data['time_resolution'];
        }
        
        $select = $this->sql->select();
        $select->from(['e' => 'entity'])
               ->columns(['id', 'guid'])
               ->join(['ed' => 'entity_definition'], 'e.definition_id=ed.id', ['ed.code' => 'code', 'ed.name' => 'name'])
               ->where(['e.id' => $this->entity_id]);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();
        $row = $results->current();
        $this->entity_definition_id = $row['ed.code'];        
        $this->guid = $row['guid'];
        $this->ed_code = $row['ed.code'];
        $this->ed_name = $row['ed.name'];
        
        $select = $this->sql->select();
        $select->from('clients')
               ->columns(['first_name','last_name'])
               ->where(['id' => $this->guest_id]);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();
        $row = $results->current();
        $this->first_name = $row['first_name'];
        $this->last_name = $row['last_name'];        
    }
    
    /**
     * Returns the object data to outside.
     * @return type
     */
    public function getData()
    {
        $data = [
            //'id' => $this->id,
            'internal_id' => $this->internal_id,
            'guest_id' => $this->guest_id,
            'reservation_id' => $this->reservation_id,
            'entity_id' => $this->entity_id,
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
            'entity_definition_id' => $this->entity_definition_id,
            'guid' => $this->guid,
            'ed_name' => $this->ed_name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
        ];
        
        if(isset($this->id))
        {
            $data['id'] = $this->id;
        }
        
        return $data;
    }
    
    /**
     * Saves the entry to the table.
     * @return type
     */
    public function save()
    {
        if(!isset($this->reservation_id) || !isset($this->guest_id))
        {
            Debug::dump('reservation '.$this->reservation_id.' guest '.$this->guest_id);
            return;
        }
        
        if(!isset($this->id))
        {
            $insert = $this->sql->insert();
            $data = $this->getData();
            
            // Unset unwanted entries.
            unset($data['internal_id']);
            unset($data['entity_definition_id']);
            unset($data['guid']);
            unset($data['ed_name']);
            unset($data['first_name']);
            unset($data['last_name']);            
            
            // Set the mandatory entry values.
            $data['date_start'] = 0;
            $data['date_end'] = 0;
                       
            $insert->into('reservation_entity')
                    ->values($data);
            $statement = $this->sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
        }
        else
        {
            $data = $this->getData();
            
            // Unset unwanted entries.
            unset($data['internal_id']);
            unset($data['entity_definition_id']);
            unset($data['guid']);
            unset($data['ed_name']);
            unset($data['first_name']);
            unset($data['last_name']);            
            
            // Set mandatory entry values.
            $data['date_start'] = 0;
            $data['date_end'] = 0;      
            
//            Debug::dump("Updating...");
//            Debug::dump($data);
            
            $update = $this->sql->update();
            $update->table('reservation_entity')
                    ->set($data)
                    ->where(['id' => $this->id]);
            $statement = $this->sql->prepareStatementForSqlObject($update);
            $results = $statement->execute();
        }
    }
    
    /**
     * Deletes the entry from the table.
     */
    public function delete()
    {
        if(!isset($id))
        {
            return;
        }
        
        $delete = $this->sql->delete();
        $delete->from('reservation_entity')
                ->where(['id' => $id]);
        $statement = $this->sql->prepareStatementForSqlObject($delete);
        $results = $statement->execute();        
    }
    
    public function getArrayCopy()
    {
        return $this->getData();
    }
    
    public function exchangeArray($data)
    {
        $this->setData($data);
    }
}
