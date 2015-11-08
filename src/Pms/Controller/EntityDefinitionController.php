<?php

namespace Pms\Controller;

use Pms\Model\Attribute;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Entity type controller class.
 */
class EntityDefinitionController extends AbstractActionController
{
    /**
     * Main action. Lists the current entity types.
     * @return type
     */
    public function indexAction()
    {
        $table = $this->getServiceLocator()->get('EntityDefinitionTable');
        $entityDefinitions = $table->fetchView();
        return $viewModel = new ViewModel([
            'entityDefinitions' => $entityDefinitions,
        ]);
    }
    
    /**
     * Edits the selected entity type.
     * @return ViewModel
     */
    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        $form = $this->getServiceLocator()->get('EntityDefinitionForm');
        $table = $this->getServiceLocator()->get('EntityDefinitionTable');
        if($id)
        {
            $id = (int)$id;
            $entityDefinition = $table->getEntityDefinition($id);
            $form->setData($entityDefinition->getArrayCopy());
            $attrModel = $this->getServiceLocator()->get('AttributeModel');
            $attrModel->setRefId($id);
            $attributes = $attrModel->getCollection();
            \Zend\Debug\Debug::dump($attributes);
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
        $form = $this->getServiceLocator()->get('EntityDefinitionForm');
        $table = $this->getServiceLocator()->get('EntityDefinitionTable');
        if($id)
        {
            $id = (int)$id;
            $entityDefinition = $table->getEntityDefinition($id);
            $form->bind($entityDefinition);
            $form->setData($post);
            if($form->isValid())
            {
                $table->saveEntityDefinition($entityDefinition);
            }
        }
        else 
        {            
            $form->setData($post);
            if($form->isValid())
            {                
                $entityDefinition = new EntityDefinition();
                $entityDefinition->exchangeArray($form->getData());
                $table->saveEntityDefinition($entityDefinition);
            }                        
        }
        
        return $this->redirect()->toRoute('pms/entity-definition');
    }
        
    /**
     * Adds the new type.
     */
    public function newAction()
    {
        $this->redirect()->toRoute('pms/entity-definition', ['action' => 'edit']);
    }
    
    /**
     * Deletes entity type.
     */
    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id');
        $table = $this->getServiceLocator()->get('EntityDefinitionTable');
        $table->deleteAttribute($id);
        return $this->redirect()->toRoute('pms/entity-definition');
    }
}



