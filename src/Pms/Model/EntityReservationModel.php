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
     * Remembers the list of attributes to be shown in availability form.
     * @var type 
     */
    protected $attributesList;
    
    /**
     * Determines the display resolution of the cells (day = 1, week = 2, month = 3);
     * @var displayResolution 
     */
    protected $displayResolution;
        
    /**
     * Constructor
     * @param \Zend\Db\Adapter\Adapter $dbAdapter Database adapter.
     * @param type $id Entity id.
     */
    public function __construct(\Zend\Db\Adapter\Adapter $dbAdapter, $id = NULL) {
        parent::__construct($dbAdapter, $id);    
        // Test
        $this->attributesList = ['clima', 'floor'];
        // End test
    }
    
    /**
     * Sets the reservation period and gets the reservations for that period.
     * @param type $startDate
     * @param type $endDate
     */
    public function setPeriod($startDate, $endDate, $displayResolution = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        
        if($displayResolution == null)
        {
            $this->displayResolution = $this->time_resolution;                        
        }
        else 
        {
            $this->displayResolution = $displayResolution;
        }
        
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
    
    /**
     * Returns the list of attributes to be shown in form.
     * @return type
     */
    public function getAttributesList()
    {
        return $this->attributesList;
    }
    
    /**
     * Sets the list of attributes to be shown in the form.
     * @param type $attributesList
     */
    public function setAttributesList($attributesList)
    {
        $this->attributesList = $attributesList;
    }
    
    protected function initReservations()
    {
        $reservations = array();
        if(isset($this->id) && isset($this->startDate) && isset($this->endDate))
        {                    
            $startDate = $this->startDate;
            if($this->displayResolution == 1)
            {
                $startDate = date('Y-m-d H:i:s', strtotime($startDate));
            }
            $endDate = $this->endDate;
            if($this->displayResolution == 1)
            {
                $endDate = date('Y-m-d H:i:s', strtotime($endDate));
            }
                        
            // Generate time classes.
            $date = $startDate;
            while(strtotime($date) <= strtotime($endDate))
            {
                if($this->displayResolution == 1)
                {
                    $key = date('H:i', strtotime($date));
                }
                else if($this->displayResolution == 2)
                {
                    $key = date('Y-m-d', strtotime($date));
                    $date = date('Y-m-d H:i', strtotime('+ 12 hours', strtotime($key)));
                }
                else
                {
                    $key = date('d', strtotime($date));
                    $date = date('Y-m-d', strtotime($date));
                    $date = date('Y-m-d H:i', strtotime('+ 12 hours', strtotime($date)));
                }
                
                $reservations[$key] = [
                    'statustext' => 'Free',
                    'statusvalue' => 'free',
                    'time' => date('Y-m-d H:i', strtotime($date)),
                    'id' => null,
                    'time_resolution' => $this->time_resolution,
                ];
                
                if($this->displayResolution == 1)
                {
                    $date = date('Y-m-d H:i', strtotime('+ 1 hour', strtotime($date)));
                }
                else if ($this->displayResolution == 2)
                {
                    $date = date('Y-m-d', strtotime('+ 1 day', strtotime($date)));
                }      
                else 
                {
                    $date = date('Y-m-d', strtotime('+ 1 day', strtotime($date)));                    
                }                              
            }      
            
//            \Zend\Debug\Debug::dump($reservations);
                                   
            // Now make the reservation query.
            $sql = new Sql($this->dbAdapter);
            $select = $sql->select();     
            $select->from(['re' =>'reservation_entity'])
                   ->join(['r' => 'reservations'], 're.reservation_id = r.id', ['status_id'])
                   ->join(['rs' => 'reservation_status'], 'r.status_id=rs.id', ['statustext', 'statusvalue'])
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
                        ->OR
                        ->NEST
                            ->lessThan('date_from', $startDate)
                            ->AND
                            ->greaterThan('date_to', $endDate)
                        ->UNNEST
                    ->UNNEST
                    ->AND
                    ->equalTo('entity_id', $this->id);
            
            $statement = $this->sql->prepareStatementForSqlObject($select);
            $results = $statement->execute();
            
            // Now parse the reservation results to time classes.
            foreach($results as $row)
            {        
                if ($this->displayResolution == 1)
                {
                    $increment_format = "+ 1 hour";
                    $date_format = "Y-m-d H:i"; 
                    $key_format = 'H:i';
                }                
                else if($this->displayResolution == 2)
                {
                    $increment_format = "+ 1 day";
                    $date_format = "Y-m-d";        
                    $key_format = 'Y-m-d';
                }
                else 
                {
                    $increment_format = "+ 1 day";
                    $date_format = "Y-m-d";            
                    $key_format = 'd';
                }
                                
                $start = date($date_format, strtotime($row['date_from']));
                $end = date($date_format, strtotime($row['date_to']));
                $increment = $increment_format;                    
                $reservation_id = $row['reservation_id'];                       
                $current = strtotime($startDate);                 
                
                while($current <= strtotime($endDate))
                {       
                    if($current >= strtotime($start) && $current < strtotime($end))
                    {                    
                        $reservations[date($key_format, $current)]['statustext'] = $row['statustext'];
                        $reservations[date($key_format, $current)]['statusvalue'] = $row['statusvalue'];
                        $reservations[date($key_format, $current)]['id'] = $reservation_id;
                        $reservations[date($key_format, $current)]['time_resolution'] = $this->time_resolution;                                                                                 
                    }
                    
                    $current = strtotime($increment, $current);
                }                                       
            }       
        }
        
//        \Zend\Debug\Debug::dump($reservations);
//        die();
//        \Zend\Debug\Debug::dump($reservations); 
        return $reservations;
    }
        
}
