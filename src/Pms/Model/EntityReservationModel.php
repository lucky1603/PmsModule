<?php
/**
 * EntityReservationModel.php
 * The model class which connects entity with the reservations for that object.
 * @author Sinisa Ristic <sinisa.ristic@gmail.com>
 * @date 15.12.2015.
 */

namespace Pms\Model;
use Pms\Model\EntityModel;
use Pms\Model\ReservationEntityModel;
use Zend\Db\Sql\Sql;

/**
 * EntityReservationModel class.
 */
class EntityReservationModel extends EntityModel
{
    /**
     * Beginning date for search period.
     * @var timestamp 
     */
    protected $startDate;
    
    /**
     * Ending date for the search period.
     * @var timestamp 
     */
    protected $endDate;
    
    /**
     * Reservation model collection.
     * @var type 
     */
    protected $reservations;
        
    /**
     * Constructor
     * @param \Zend\Db\Adapter\Adapter $dbAdapter Database adapter.
     * @param type $id Entity id.
     */
    public function __construct(\Zend\Db\Adapter\Adapter $dbAdapter, $id = NULL) {
        parent::__construct($dbAdapter, $id);        
    }
    
    /**
     * Sets the reservation period and gets the reservations for that period.
     * @param type $startDate
     * @param type $endDate
     */
    public function setPeriod($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->reservations = $this->initReservations();                        
    }
    
    /**
     * Override of parent.
     * @param type $id
     */
    public function setId($id) {
        parent::setId($id);
        $this->reservations = $this->initReservations();
    }
    
    /**
     * Returns the reservations array.
     * @return Reservations array.
     */
    public function getReservations()
    {
        if(empty($this->reservations))
        {
            $this->reservations = $this->initReservations();
        }
        return $this->reservations;
    }
    
    protected function initReservations()
    {
        $reservations = array();
                
        if(isset($this->id) && isset($this->startDate) && isset($this->endDate))
        {        
            $startDate = $this->startDate;
            if($this->time_resolution == 2)
            {
                $startDate = date('Y-m-d H:i:s', strtotime($startDate));
            }
            $endDate = $this->endDate;
            if($this->time_resolution == 2)
            {
                $endDate = date('Y-m-d H:i:s', strtotime($endDate));
            }
                   
            $date = $startDate;
            while(strtotime($date) <= strtotime($endDate))
            {
                $reservations[$date] = [
                    'status' => 'free',
                    'id' => null,
                ];
                if($this->time_resolution == 1)
                {
                    $date = date('Y-m-d', strtotime('+ 1 day', strtotime($date)));
                }
                else 
                {
                    $date = date('Y-m-d H:i:s', strtotime('+ 1 hour', strtotime($date)));
                }               
            }       
                        
            $sql = new Sql($this->dbAdapter);
            $select = $sql->select();     
            $select->from('reservation_entity')
                   ->where->NEST
                        ->NEST
                            ->lessThanOrEqualTo('date_to', $endDate)
                            ->AND
                            ->greaterThanOrEqualTo('date_from', $startDate)
                        ->UNNEST
                        ->OR
                        ->NEST
                            ->lessThan('date_from', $startDate)
                            ->AND
                            ->greaterThan('date_to', $startDate)
                        ->UNNEST
                        ->OR
                        ->NEST
                            ->lessThan('date_from', $endDate)
                            ->AND
                            ->greaterThan('date_to', $endDate)
                        ->UNNEST
                    ->UNNEST
                    ->AND
                    ->equalTo('entity_id', $this->id);
            
            $statement = $this->sql->prepareStatementForSqlObject($select);
            $results = $statement->execute();
            foreach($results as $row)
            {
//                $reservation = new ReservationEntityModel($this->dbAdapter);
//                $reservation->setData($row);
                
//                $start = date('Y-m-d', strtotime($reservation->date_from));
//                $end = date('Y-m-d', strtotime($reservation->date_to));
                
                if($this->time_resolution == 1)
                {
                    $increment_format = "+ 1 day";
                    $date_format = "Y-m-d";                    
                }
                else 
                {
                    $increment_format = "+ 1 hour";
                    $date_format = "Y-m-d H:i:s";                    
                }
                $start = date($date_format, strtotime($row['date_from']));
                $end = date($date_format, strtotime($row['date_to']));
                $increment = $increment_format;                    
                $reservation_id = $row['reservation_id'];                       
                $current = strtotime($startDate);                                                
                $counter = 0;
                while($current <= strtotime($endDate))
                {
                    if($current >= strtotime($start) && $current < strtotime($end))
                    {
                        $reservations[date($date_format, $current)]['status'] = 'reserved';
                        $reservations[date($date_format, $current)]['id'] = $reservation_id;
                        $reservations[date($date_format, $current)]['time_resolution'] = $this->time_resolution;
                        
                    }
                    else 
                    {
                        $reservations[date($date_format, $current)]['status'] = 'free';
                        $reservations[date($date_format, $current)]['id'] = null;
                        $reservations[date($date_format, $current)]['time_resolution'] = $this->time_resolution;
                    }
                    
                    $current = strtotime($increment, $current);
                    $counter++;
                }                                
            }
        }
        
        
//        \Zend\Debug\Debug::dump($reservations);
//        die();
        
        return $reservations;
    }
        
}
