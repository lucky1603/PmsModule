<?php

namespace Pms\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;
use Zend\Dd\Adapter\Adapter;
use Zend\Db\Sql\Sql;

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
    
    /**
     * Do jaja!!
     * @return type
     */
    public function getAvailableRoomsAction()
    {
        $from = (int) strtotime($this->params()->fromQuery('from'));
        $to = strtotime($this->params()->fromQuery('to'));   
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
                ->between('re.date_start', $from, $to)
                ->OR
                ->between('re.date_end', $from, $to)
                ->OR
                ->NEST
                ->lessThan('re.date_start', $from)
                ->greaterThan('re.date_end', $to)
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
}
