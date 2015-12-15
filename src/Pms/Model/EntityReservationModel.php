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
            $date = $this->startDate;
            while(strtotime($date) <= strtotime($this->endDate))
            {
                $reservations[$date] = 'free';
                $date = date('Y-m-d', strtotime('+ 1 day', strtotime($date)));
            }
            
//            \Zend\Debug\Debug::dump($reservations);
//            die();
            
            $sql = new Sql($this->dbAdapter);
            $select = $sql->select();     
            $select->from('reservation_entity')
                   ->where->NEST
                        ->NEST
                            ->lessThanOrEqualTo('date_to', $this->endDate)
                            ->AND
                            ->greaterThanOrEqualTo('date_from', $this->startDate)
                        ->UNNEST
                        ->OR
                        ->NEST
                            ->lessThan('date_from', $this->startDate)
                            ->AND
                            ->greaterThan('date_to', $this->startDate)
                        ->UNNEST
                        ->OR
                        ->NEST
                            ->lessThan('date_from', $this->endDate)
                            ->AND
                            ->greaterThan('date_to', $this->endDate)
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
                
                $start = date('Y-m-d', strtotime($row['date_from']));
                $end = date('Y-m-d', strtotime($row['date_to']));
                
                
                $current = strtotime($this->startDate);
                $counter = 0;
                while($current <= strtotime($this->endDate))
                {
                    if($current >= strtotime($start) && $current <= strtotime($end))
                    {
                        $reservations[date('Y-m-d', $current)] = 'reserved';
                    }
                    else 
                    {
                        $reservations[date('Y-m-d', $current)] = 'free';
                    }
                    
                    $current = strtotime('+1 day', $current);
                    $counter++;
                }                                
            }
        }
        
        return $reservations;
    }
        
}
