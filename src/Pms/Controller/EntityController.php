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
use Pms\Model\Entity;


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
                'id' => $id,
            ]);
            
            return $viewModel;
        }
        
        return $this->redirect()->toRoute('pms/entity');        
    }
    
    /**
     * Processes changes to entity object. 
     */
    public function processAction()
    {
        $post = $this->request->getPost();
        $id = $this->params()->fromRoute('id');
        $entityTable = $this->getServiceLocator()->get('EntityTable');
        if(!isset($id))
        {
            $entity = new Entity();
            $entity->exchangeArray($post);
            $entityTable->saveEntity($entity);
        }
        else
        {
            $form = $this->getServiceLocator()->get("EntityForm");
            $entity = $entityTable->getEntity($id);
            Debug::dump($id);
            $form->bind($entity);
            $form->setData($post);
            if($form->isValid())
            {
                $entityTable->saveEntity($entity);
            }
            else 
            {
                throw new \Exception("Invalid form data!");
            }
        }
        
        return $this->redirect()->toRoute('pms/entity');        
    }
    
    /**
     * Adds new entity.
     * @return ViewModel
     */
    public function addAction() 
    {
        $form = $this->getServiceLocator()->get('EntityForm');
        
        $viewModel = new ViewModel([
            'form' => $form,
        ]);
        
        $viewModel->setTemplate('/pms/entity/edit.phtml');
        return $viewModel;
    }
    
    /**
     * Deletes entity.
     */
    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id');
        if(!isset($id))
        {
            $this->redirect()->toRoute('pms/entity');
        }
        
        $entityTable = $this->getServiceLocator()->get("EntityTable");
        $entityTable->deleteEntity($id);
        
        $this->redirect()->toRoute('pms/entity');        
    }
}
