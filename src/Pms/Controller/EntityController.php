<?php

namespace Pms\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class EntityController extends AbstractActionController 
{
    public function indexAction()
    {
        return new ViewModel();
    }
}
