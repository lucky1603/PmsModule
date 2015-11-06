<?php

namespace Pms\Controller;

use Pms\Model\Attribute;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Entity type controller class.
 */
class AttributeManagerController extends AbstractActionController
{
    /**
     * Main action. Lists the current entity types.
     * @return type
     */
    public function indexAction()
    {
        $table = $this->getServiceLocator()->get('AttributeTable');
        $attributes = $table->fetchAll()->toArray();
        return $viewModel = new ViewModel([
            'attributes' => $attributes,
        ]);
    }
    
    /**
     * Edits the selected entity type.
     * @return ViewModel
     */
    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        $form = $this->getServiceLocator()->get('AttributeForm');
        $table = $this->getServiceLocator()->get('AttributeTable');
        if($id)
        {
            $id = (int)$id;
            $attribute = $table->getAttribute($id);
            $form->setData($attribute->getArrayCopy());
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
        $form = $this->getServiceLocator()->get('AttributeForm');
        $table = $this->getServiceLocator()->get('AttributeTable');
        if($id)
        {
            $id = (int)$id;
            $attribute = $table->getAttribute($id);
            $form->bind($attribute);
            $form->setData($post);
            if($form->isValid())
            {
                $table->saveAttribute($attribute);
            }
        }
        else 
        {            
            $form->setData($post);
            if($form->isValid())
            {                
                $attribute = new Attribute();
                $attribute->exchangeArray($form->getData());
                $table->saveAttribute($attribute);
            }                        
        }
        
        return $this->redirect()->toRoute('pms/attribute-manager');
    }
        
    /**
     * Adds the new type.
     */
    public function newAction()
    {
        $this->redirect()->toRoute('pms/attribute-manager', ['action' => 'edit']);
    }
    
    /**
     * Deletes entity type.
     */
    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id');
        $table = $this->getServiceLocator()->get('AttributeTable');
        $attribute = $table->getAttribute($id);
        $table->deleteAttribute($id);
        return $this->redirect()->toRoute('pms/attribute-manager');
    }
}



