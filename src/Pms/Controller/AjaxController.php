<?php

namespace Pms\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;

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
        $from = strtotime($this->params()->fromQuery('from'));
        $from = date('d.m.Y', $from);                
        $to = strtotime($this->params()->fromQuery('to'));   
        $to = date('d.m.Y', $to);
        $current = date('d.m.Y', time());
        
        $result = [$from, $to, $current];
        $this->viewModel->setVariable('response', json_encode($result));
        return $this->viewModel;
    }
}
