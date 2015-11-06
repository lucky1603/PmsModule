<?php
/**
 * Entity type controller.
 * @description Controller which handles the user management.
 * @name RegisterController.php
 * @author Sinisa Ristic <sinisa.ristic@gmail.com>
 * @date 11/2/2015.
 */

namespace Pms\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Entity type controller class.
 */
class EntityTypeController extends AbstractActionController
{
    /**
     * Main action. Lists the current entity types.
     * @return type
     */
    public function indexAction()
    {
        $table = $this->getServiceLocator()->get('EntityTypeTable');
        $types = $table->fetchAll()->toArray();
        return $viewModel = new ViewModel([
            'types' => $types,
        ]);
    }
    
    /**
     * Edits the selected entity type.
     * @return ViewModel
     */
    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        $form = $this->getServiceLocator()->get('EntityTypeForm');
        $table = $this->getServiceLocator()->get('EntityTypeTable');
        if($id)
        {
            $id = (int)$id;
            $entityType = $table->getEntityType($id);
            $form->setData($entityType->getArrayCopy());
            $viewModel = new ViewModel([
                'form' => $form,
                'id' => $id,
            ]);       
            return $viewModel;
        }
        
        return new ViewModel(['form' => $form]);
    }
    
    /**
     * Processes the form input.
     * @return type
     */
    public function processAction()
    {
        $post = $this->request->getPost();
        $id = $this->params()->fromRoute('id');
        $form = $this->getServiceLocator()->get('EntityTypeForm');
        $table = $this->getServiceLocator()->get('EntityTypeTable');
        if($id)
        {
            $id = (int)$id;
            $entityType = $table->getEntityType($id);
            $form->bind($entityType);
            $form->setData($post);
            if($form->isValid())
            {
                $table->saveEntityType($entityType);
            }
        }
        else 
        {            
            $form->setData($post);
            if($form->isValid())
            {                
                $entityType = new \Pms\Model\EntityType();
                $entityType->exchangeArray($form->getData());
                $table->saveEntityType($entityType);
            }                        
        }
        
        return $this->redirect()->toRoute('pms/entity-type');
    }
        
    /**
     * Adds the new type.
     */
    public function newAction()
    {
        $this->redirect()->toRoute('pms/entity-type', ['action' => 'edit']);
    }
    
    /**
     * Deletes entity type.
     */
    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id');
        $table = $this->getServiceLocator()->get('EntityTypeTable');
        $entityType = $table->getEntityType($id);
        $table->deleteEntityType($id);
        return $this->redirect()->toRoute('pms/entity-type');
    }
}

