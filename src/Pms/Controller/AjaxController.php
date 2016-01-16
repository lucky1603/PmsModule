<?php

namespace Pms\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;
use Zend\Db\Sql\Sql;
use Pms\Model\ReservationModel;
use Zend\Session\Container;

class AjaxController extends AbstractActionController
{
    protected $viewModel;

    public function onDispatch(MvcEvent $mvcEvent)
    {
        $this->viewModel = new ViewModel; // Don't use $mvcEvent->getViewModel()!
        $this->viewModel->setTemplate('/pms/ajax/response');
        $this->viewModel->setTerminal(true); // Layout won't be rendered

        return parent::onDispatch($mvcEvent);
    }

    /**
     * Test ajax action.
     * @return type
     */
    public function someAjaxAction()
    {
        $something = array();
        $something = [
            'name' => "sinisa",
            'last_name' => 'ristic',
            'else' => "35",
        ];
        
        $this->viewModel->setVariable('response', json_encode($something));
        return $this->viewModel;
    }
    
    public function getReservationListAction()
    {
        $post = $this->request->getPost();
        $typeId = $post['entity_type_id'];
        $startDate = date('Y-m-d', strtotime($post['date_from']));
        $startTime = date('H:i:s', strtotime($post['date_from']));      
        if(isset($post['multi-select']))
        {
            $attrs = $post['multi-select']; 

        }
        else 
        {
            $attrs = array();
        }
                        
        $table = $this->getServiceLocator()->get('EntityTable');    
        if(isset($sort))
        {
            $results = $table->fetchView($typeId, $sort);
        }
        else 
        {
            $results = $table->fetchView($typeId);
        }
        
        $adapter = $this->getServiceLocator()->get('Adapter');
        
        $lines = array();
        $index = array();
        $attList = array();
        foreach($results as $row)
        {                        
            $line = array();
            $id = $row['id'];
            $model = new \Pms\Model\EntityReservationModel($adapter);
            $model->setId($id);
            $time_resolution = $model->getTimeResolution();
            switch ($time_resolution) {
                case 1:
                    $startPeriod = $startDate;
                    $endPeriod = date('Y-m-d', strtotime('+6 days', strtotime($startDate)));      
                    break;
                default:
                    if(isset($startTime))
                    {
                        $startPeriod = $startDate.' '.$startTime;
                    }
                    else 
                    {
                        $startPeriod = date('Y-m-d H:i:s', strtotime($startDate));
                    }                    
                    $endPeriod = date('Y-m-d H:i:s', strtotime('+23 hours', strtotime($startDate)));      
                    break;
            }
            if(isset($startPeriod) && isset($endPeriod))
            {
                $model->setPeriod($startPeriod, $endPeriod);
            }
            $line['guid'] = $model->guid;
            $line['code'] = $row['code'];
            $line['status'] = $model->status;
            $attributes = $model->getAllAttributes();
            $mAttList = $model->getAttributesList();
            foreach($attributes as $attribute)
            {
                if(!isset($attList[$attribute->code]))
                {
                    $attList[$attribute->code] = $attribute->label;
                }
                
                if(!in_array($attribute->code, $attrs))
                {
                    continue;
                }
                
                if($attribute->type == "boolean")
                {
                    $line[$attribute->code] = $attribute->value == 1 ? "Yes" : "No";
                }
                else if($attribute->type == "select")
                {
                    $line[$attribute->code] = $attribute->optionValues[$attribute->value];
                }
                else 
                {
                    $line[$attribute->code] = $attribute->value;
                }                
            }
            
            $reservations = $model->getReservations();                     
            $current = strtotime($startDate);
            foreach($reservations as $key=>$value)
            {
                $line[$key] = $value;                                
            }

            if(isset($sort))
            {
                $key = $line[$sort];
                $index[$line['guid']] = $key;
            }
            else 
            {
                $key = $line['guid'];
                $index[$line['guid']] = $key;
            }
                        
            $lines[$line['guid']] = $line;
        }                       
        $viewModel = new ViewModel([
            'data' => $lines,
        ]);                
        return $viewModel;
    }
    
    /**
     * Do jaja!!
     * @return type
     */
    public function getAvailableRoomsAction()
    {
//        $from = (int) strtotime($this->params()->fromQuery('from'));
//        $to = strtotime($this->params()->fromQuery('to'));   
        
        $from = $this->params()->fromQuery('from');
        $to = $this->params()->fromQuery('to');           
        $type = $this->params()->fromQuery('type');

        $current = date('d.m.Y', time());
        
        // Za izabranu definiciju sobe, daj mi sve slobodne sobe:
        //  1.  Daj mi sve sobe tipa A, koje nisu u tabeli rezervacija ili 
        //      ciji datumi rezervacije ne upadaju u period zeljene rezervacije:
        //      - i start i end su manji od starta rezervacije
        //      - i start i end su veci od starta rezervacije
        //      
        //                          ILI
        //                          
        //  1.  Daj mi sve sobe tipa A, koje su u tabeli rezervacija i 
        //      ciji se datum preklapa sa datumom zeljene rezervacije i to:
        //      - Krajnji datum veci od pocetnog datuma rezervacije ili startni
        //        datum manji od krajnjeg datuma rezervacije.
        //  2.  Daj mi sve sobe tipa A, koje ne pripadaju skupu soba iz gornje tacke.
        //      Ako ih ima, to su slobodne sobe.
        
        $dbAdapter = $this->getServiceLocator()->get('Adapter');
        $sql = new Sql($dbAdapter);
        $select = $sql->select();
        $select->from(['re' => 'reservation_entity'])
            ->join(['e' => 'entity'], 're.entity_id = e.id', ['guid', 'definition_id'])
            ->join(['ed' => 'entity_definition'], 'e.definition_id = ed.id', ['code', 'name']);
        
        $select->where->NEST
                ->between('re.date_from', $from, $to)
                ->OR
                ->between('re.date_to', $from, $to)
                ->OR
                ->NEST
                ->lessThan('re.date_from', $from)
                ->greaterThan('re.date_to', $to)
                ->UNNEST
                ->UNNEST;
        
        if(isset($type))
        {
            $select->where->equalTo('code', $type);
        }
        
        $statement = $sql->prepareStatementForSqlObject($select);
        
        $results = $statement->execute();
        $rows = array();
        foreach($results as $row)
        {
            if(!in_array($row['guid'], $rows))
            {
                $rows[] = $row['guid'];
            }            
        }
        
        $select = $sql->select();
        $select->from(['e' => 'entity'])
                ->columns(['id', 'guid'])
                ->join(['ed' => 'entity_definition'], 'e.definition_id = ed.id', ['code']);
        if(isset($type))
        {
            $select->where(['code' => $type]);
        }
        $statement = $sql->prepareStatementForSqlObject($select);

        $results = $statement->execute();
        $keys = [];
        foreach($results as $row)
        {
            $keys[$row['id']] = $row['guid'];
        }
        
        foreach($keys as $key=>$value)
        {
            if(in_array($value, $rows))
            {
                unset($keys[$key]);
            }
        }
                                
        //$result = [$from, $to, $current];
        $this->viewModel->setVariable('response', json_encode($keys));
        return $this->viewModel;
    }
    
    /**
     * Get the client id of the last written client.
     * @return type
     */
    public function getLastClientAction()
    {
        $client_table = $this->getServiceLocator()->get('ClientTable');
        return $this->viewModel->setVariable('response', json_encode($client_table->getLastId()));
    }
    
    /**
     * Write new client to the database and return its id.
     * @return type
     */
    public function writeNewClientAction()
    {
        $post = $this->request->getPost();
        $form = $this->getServiceLocator()->get("ClientForm");
        $table = $this->getServiceLocator()->get("ClientTable");
                       
//        if(count($post) == 0)
//        {
//            $post['first_name'] = 'kikiriki';
//        }
                
        $form->setData($post);
//        \Zend\Debug\Debug::dump($form);
        if($form->isValid())
        {
//            \Zend\Debug\Debug::dump('formvalid');
            $client = new \Pms\Model\Client();    
            $client->exchangeArray($post);
            $table->saveClient($client);
        }
        else {
//            \Zend\Debug\Debug::dump('form not valid');
        }
//        die();
        
        $data = array();
        $id = $table->getLastId();
        $data['client'] = $table->getClient($id);
        $data['lastId'] = $id;
        return $this->viewModel->setVariable('response', json_encode($data));
    }       
    
    /**
     * Updates the session data of the reservation model.
     * @return type
     */
    public function updateReservationModelAction()
    {
        $client_id = $this->params()->fromQuery('client_id');
        $status_id = $this->params()->fromQuery('status_id');
        $session = new \Zend\Session\Container('models');
        if(isset($session->reservationModel))
        {
            $session->reservationModel['client_id'] = $client_id;
            $session->reservationModel['status_id'] = $status_id;    
            $response = "Succeded";
        }
        else {
            $response = "Failed";
        }
        
        return $this->viewModel->setVariable($response, json_encode($response));
    }
    
    /**
     * Gets reservation details for the content of tooltip.
     * @return type
     */
    public function reservationDetailsAction()
    {
        $id = $this->params()->fromQuery('id');
        $dbAdapter = $this->getServiceLocator()->get("Adapter");
        $model = new ReservationModel($dbAdapter);
        $model->setId($id);
        
        $out = [
            'Reservation Id' => $id,
            'Reserved By' => $model->clientName,
            'Created At' => $model->created_at,
            'Status' => $model->status,
        ];
        
        return $this->viewModel->setVariable('response', json_encode($out));
    }
    
    /**
     * Gets the list of upcomming guests
     * @return ViewModelgit push
     */
    public function getAvailabilityAction()
    {
        if($this->request->isPost())
        {
            $post = $this->request->getPost();
            $type = $post['entity_type_id'];
            $startDate = $post['date_from'];
        }
        else 
        {
            $startDate = $this->params()->fromQuery('startDate');
            $endDate = $this->params()->fromQuery('endDate');
            $type = $this->params()->fromQuery('type');
        }
                
        $entityTypeTable = $this->getServiceLocator()->get("EntityTypeTable");
        $entityType = $entityTypeTable->getEntityType($type);
        $time_resolution = $entityType->time_resolution;
        
        $dbAdapter = $this->getServiceLocator()->get('Adapter');
        $sql = new Sql($dbAdapter);
        $select = $sql->select();
        $select->from(['re' => 'reservation_entity'])
               ->columns(['Arrival' => 'date_from', 'Leaving' => 'date_to'])
               ->join(['e' => "entity"], 're.entity_id=e.id', ['Number' => 'guid', 'Room Status' => 'status'])
               ->join(['ed' => 'entity_definition'], 'e.definition_id=ed.id', ['Object Class' => 'code', 'Object Name' => 'name'])
               ->join(['et' => 'entity_type'], 'ed.entity_type_id=et.id', ['Object Type' => 'name'])
               ->join(['r' => 'reservations'], 're.reservation_id=r.id', ['Reservation' => 'reservation_id', 'Status Id' => 'status_id'])
               ->join(['c' => 'clients'], 're.guest_id=c.id', ['Guest_FirstName' => 'first_name', 'Guest_LastName' => 'last_name'])
               ->join(['d' => 'clients'], 'r.client_id=d.id', ['Client_FirstName' => 'first_name', 'Client_LastName' => 'last_name']);
        
        if(empty($startDate))
        {
            if($time_resolution == 1 /* days */)
            {
                $format = 'Y-m-d';
            }
            else 
            {
                $format = 'Y-m-d H:i:s';
            }
            
            $startDate = date($format, time());
        }
        
        if(empty($endDate))
        {
           if($time_resolution == 1 /* Days */)
           {
               $increment = '+ 6 days';
               $format = 'Y-m-d';
           }
           else 
           {
               $increment = '+ 23 hours';
               $format = 'Y-m-d H:i:s';
           }

           $endDate = date($format, strtotime($increment, strtotime($startDate)));
        }
        
        $select->where->between('re.date_from', $startDate, $endDate);
        $select->where->equalTo('et.id', $type);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();
        return new ViewModel([
            'incomming' => $results
        ]);
    }
    
    /**
     * Determines, where will the application go back.
     * @return type
     */
    public function whereToAction()
    {
        $session = new Container('models');
        if($session->direction == 'edit')
        {
            unset($session->direction);
            unset($session->reservationModel);
            $instructions = array();
            $instructions['path'] = '/pms/reservation';
            $instructions['bookmark'] = '';
            $instructions['method'] = 'GET';
            return $this->viewModel->setVariable('response', json_encode($instructions));
        }
        else {
            $bookmark = $session->bookmark;
            unset($session->direction);
            unset($session->bookmark);
            unset($session->reservationModel);
            $instructions = array();
            $instructions['path'] = '/pms/entity/fullList';
            $instructions['bookmark'] = $bookmark;
            $instructions['method'] = 'POST';
            return $this->viewModel->setVariable('response', json_encode($instructions));
        }
    }
    
    public function testDaysAction()
    {
        $start = '2015-12-15';
        $end = '2015-12-25';
        while(strtotime($start) <= strtotime($end)) 
        {
            \Zend\Debug\Debug::dump($start);
            $start = date('Y-m-d h:i:s', strtotime('+ 1 day + 2 hours', strtotime($start)));
        }
        
        \Zend\Debug\Debug::dump(date('s', 0));
        die();
    }
    
    public function rememberAction()
    {
        $mark = $this->params()->fromQuery('mark');
        $session = new Container('models');
        $session->direction = $mark;
        if($this->request->isPost())
        {
            $post = $this->request->getPost();
            $session->bookmark = $post;
        }
        die($mark);
    }
}
