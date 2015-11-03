<?php

namespace Pms\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class EntityTypeController extends AbstractActionController
{
    public function indexAction()
    {
        $table = $this->getServiceLocator()->get('EntityTypeTable');
        $types = $table->fetchAll()->toArray();
        return $viewModel = new ViewModel([
            'types' => $types,
        ]);
    }
        
}

