<?php

use Zend\Mvc\Controller\AbstractActionController;

class ReportController extends AbstractActionController
{
    public function indexAction() {
        return new ViewModel();
    }
    
    public function entityUsageAction()
    {
        
    }
}

