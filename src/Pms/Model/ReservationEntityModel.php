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
    public $date_start;
    public $date_end;
    public $first_name;
    public $last_name;
    public $ed_code;
    public $ed_name;
    public $guid;
    
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
    
    /**
     * Sets the reffering reservation id.
     * @param type $reservationId
     */
    public function setReservationId($reservationId)
    {
        $this->reservation_id = $reservationId;
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
        }
        
        $this->guest_id = isset($data['guest_id']) ? $data['guest_id'] : null;
        $this->entity_id = isset($data['entity_id']) ? $data['entity_id'] : null;
        $this->reservation_id = isset($data['reservation_id']) ? $data['reservation_id'] : null;
        $this->date_start = isset($data['date_start']) ? $data['date_start'] : null;
        $this->date_end = isset($data['date_end']) ? $data['date_end'] : null;
        
        if(isset($data['first_name']))
        {
            $this->first_name = $data['first_name'];
        }
        
        if(isset($data['last_name']))
        {
            $this->last_name = $data['last_name'];
        }
        
        if(isset($data['ed_code']))
        {
            $this->ed_code = $data['ed_code'];
        }
        
        if(isset($data['ed_name']))
        {
            $this->ed_name = $data['ed_name'];
        }
        
        if(isset($data['guid']))
        {
            $this->guid = $data['guid'];
        }
        
    }
    
    /**
     * Returns the object data to outside.
     * @return type
     */
    public function getData()
    {
        return [
            //'id' => $this->id,
            'guest_id' => $this->guest_id,
            'reservation_id' => $this->reservation_id,
            'entity_id' => $this->entity_id,
            'date_start' => $this->date_start,
            'date_end' => $this->date_end,
        ];
    }
    
    /**
     * Saves the entry to the table.
     * @return type
     */
    public function save()
    {
        if(!isset($this->reservation_id) || !isset($this->guest_id))
        {
            return;
        }
        
        if(!isset($id))
        {
            $insert = $this->sql->insert();
            $insert->into('reservation_entity')
                    ->values($this->getData());
            $statement = $this->sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
        }
        else
        {
            $update = $this->sql->update();
            $update->table('reservation_entity')
                    ->set($this->getData())
                    ->where(['id' => $id]);
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
}
