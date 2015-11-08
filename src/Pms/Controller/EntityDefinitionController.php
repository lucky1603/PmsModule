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
            $entityDefModel = $this->getServiceLocator()->get('EntityDefinitionModel');
            $entityDefModel->setId($id);
            $form->setData($entityDefModel->getData());
            $attributes = $entityDefModel->getAttributes();
            foreach($attributes as $attribute)
            {                
                if($attribute->type == 'boolean')
                {
                    $attElement = new \Zend\Form\Element\Checkbox($attribute->code);
                }
                elseif ($attribute->type == 'text') {
                    $attElement = new \Zend\Form\Element\Textarea($attribute->code);
                }
                elseif ($attribute->type == 'timestamp') {
                    $attElement = new \Zend\Form\Element\DateTime($attribute->code);
                }
                else {
                    $attElement = new \Zend\Form\Element\Text($attribute->code);
                }
                
                $attElement->setLabel($attribute->label);
                $attElement->setValue($attribute->getValue());
                $form->add($attElement);
            }
            
            $viewModel = new ViewModel([
                'form' => $form,
                'id' => $id,
                'attributes' => $attributes,
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



