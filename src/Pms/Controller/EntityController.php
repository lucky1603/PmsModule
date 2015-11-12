<?php
/**
 * @name EntityController.php
 * @description Controller which handles the management of 'entity' objects.
 * @author Sinisa Ristic <sinisa.ristic@gmail.com>
 * @date 11/2/2015.
 */

namespace Pms\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Sql;
use Zend\Debug\Debug;

/**
 * EntityController class.
 */
class EntityController extends AbstractActionController 
{
    /**
     * Default action.
     * @return ViewModel
     */
    public function indexAction()
    {
        $table = $this->getServiceLocator()->get('EntityTable');
        $results = $table->fetchView();
        $entities = [];
        do {
            $entities[] = $results->current();
        } while ($results->next());
        
        $viewModel = new ViewModel([
            'entities' => $entities, 
        ]);
        
        return $viewModel;
    }
    
    /**
     * Edit existing entity.
     * @return type
     */
    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        if(!isset($id))
        {
            return $this->redirect()->toRoute('pms/entity');
        }
        
        $id = (int)$id;
        $form = $this->getServiceLocator()->get('EntityForm');
        $entityModel = $this->getServiceLocator()->get('EntityModel');
        $entityModel->setId($id);
        $form->bind($entityModel);
        if($form->isValid())
        {
            $viewModel = new ViewModel([
                'form' => $form,
                'entityModel' => $entityModel,
            ]);
            
            return $viewModel;
        }
        
        return $this->redirect()->toRoute('pms/entity');        
    }
}
