<?php

namespace Pms\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Debug\Debug;
use Zend\Session\Container;

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
        $service = $this->getServiceLocator()->get('AuthenticationService');
        $mail = $service->getStorage()->read();
        if(!isset($mail))
        {
            return $this->redirect()->toUrl('/');
        }
        
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
                    $attElement->setAttribute('COLS', 40);
                    $attElement->setAttribute('ROWS', 4);
                }
                elseif ($attribute->type == 'timestamp') {
                    $attElement = new \Zend\Form\Element\DateTime($attribute->code);
                }
                elseif ($attribute->type == 'select')
                {
                    $attElement = new \Zend\Form\Element\Select($attribute->code);                    
                    $attElement->setValueOptions($attribute->optionValues);
                }
                elseif ($attribute->type == 'double' || $attribute->type == 'integer')
                {
                    $attElement = new \Zend\Form\Element\Number($attribute->code);
                    if($attribute->type == 'integer')
                    {
                        $attElement->setAttributes([
                            'min' => 0,
                            'max' => 1000,
                            'step' => 1,
                        ]);
                    }
                    else 
                    {
                        $attElement->setAttributes([
                            'min' => -1000.0,
                            'max' => 1000.0,
                            'step' => 0.1,
                        ]);
                    }
                    
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
        $entityDefinitionModel = $this->getServiceLocator()->get('EntityDefinitionModel');
        if($id)
        {
            $id = (int)$id;            
            // handling attributes ...
            
            $entityDefinitionModel->setId($id);
            $attributes = $entityDefinitionModel->getAttributes();
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
                elseif ($attribute->type == 'select')
                {
                    $attElement = new \Zend\Form\Element\Select($attribute->code);
                    $attElement->setValueOptions($attribute->optionValues);
                }
                elseif ($attribute->type == 'double' || $attribute->type == 'integer')
                {
                    $attElement = new \Zend\Form\Element\Number($attribute->code);
                    if($attribute->type == 'integer')
                    {
                        $attElement->setAttributes([
                            'min' => 0,
                            'max' => 1000,
                            'step' => 1,
                        ]);
                    }
                    else 
                    {
                        $attElement->setAttributes([
                            'min' => -1000.0,
                            'max' => 1000.0,
                            'step' => 0.1,
                        ]);
                    }
                    
                }
                else {
                    $attElement = new \Zend\Form\Element\Text($attribute->code);
                }
                
                $attElement->setLabel($attribute->label);
                $attElement->setValue($attribute->getValue());
                $form->add($attElement);
            }
            
            $form->bind($entityDefinitionModel);
            $form->setData($post);

            if($form->isValid())
            {
                $entityDefinitionModel->save();
            }
                    
        }
        else 
        {            
            $session = new Container('models');
            $entityDefinitionModel->setData($session->entityDefinitionData);
            $attributes = $entityDefinitionModel->getAttributes();
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
                elseif ($attribute->type == 'select')
                {
                    $attElement = new \Zend\Form\Element\Select($attribute->code);
                    $attElement->setValueOptions($attribute->optionValues);
                }
                elseif ($attribute->type == 'double' || $attribute->type == 'integer')
                {
                    $attElement = new \Zend\Form\Element\Number($attribute->code);
                    if($attribute->type == 'integer')
                    {
                        $attElement->setAttributes([
                            'min' => 0,
                            'max' => 1000,
                            'step' => 1,
                        ]);
                    }
                    else 
                    {
                        $attElement->setAttributes([
                            'min' => -1000.0,
                            'max' => 1000.0,
                            'step' => 0.1,
                        ]);
                    }
                    
                }
                else {
                    $attElement = new \Zend\Form\Element\Text($attribute->code);
                }
                
                $attElement->setLabel($attribute->label);
                $attElement->setValue($attribute->getValue());
                $form->add($attElement);
            }
            $form->bind($entityDefinitionModel);
            $form->setData($post);
            if($form->isValid())
            {          
                $entityDefinitionModel->save();
            }                        
        }
        return $this->redirect()->toRoute('pms/entity-definition');
    }
        
    /**
     * Adds the new type.
     */
    public function newAction()
    {        
        //$this->redirect()->toRoute('pms/entity-definition', ['action' => 'edit']);
        $dbAdapter = $this->getServiceLocator()->get('Adapter');
        $table = new \Zend\Db\TableGateway\TableGateway('entity_type', $dbAdapter);
        $entity_types = $table->select();
        return new ViewModel([
            'entity_types' => $entity_types,
        ]);
    }
    
    /**
     * Processes choice of the entity type in order to set up new entity definition.
     * @return ViewModel
     */
    public function processNewAction()
    {
        $id = $this->params()->fromRoute('id');
        $dbAdapter = $this->getServiceLocator()->get('Adapter');
        $model = $this->getServiceLocator()->get('EntityDefinitionModel');
        $model->setEntityType($id);
        $session = new Container('models');
        $session->entityDefinitionData = $model->getData();
//        Debug::dump($model->getData());
        
        $form = $this->getServiceLocator()->get('EntityDefinitionForm');
        $form->bind($model);      
        $attributes = $model->attributes;
        if(isset($attributes))
        {
            foreach($attributes as $attribute)
            {                
                if($attribute->type == 'boolean')
                {
                    $attElement = new \Zend\Form\Element\Checkbox($attribute->code);
                }
                elseif ($attribute->type == 'text') {
                    $attElement = new \Zend\Form\Element\Textarea($attribute->code);
                    $attElement->setAttribute('COLS', 40);
                    $attElement->setAttribute('ROWS', 4);
                }
                elseif ($attribute->type == 'double' || $attribute->type == 'integer')
                {
                    $attElement = new \Zend\Form\Element\Number($attribute->code);
                    if($attribute->type == 'integer')
                    {
                        $attElement->setAttributes([
                            'min' => 0,
                            'max' => 1000,
                            'step' => 1,
                        ]);
                    }
                    else 
                    {
                        $attElement->setAttributes([
                            'min' => -1000.0,
                            'max' => 1000.0,
                            'step' => 0.1,
                        ]);
                    }
                    
                }
                elseif ($attribute->type == 'timestamp') {
                    $attElement = new \Zend\Form\Element\DateTime($attribute->code);
                }
                elseif ($attribute->type == 'select')
                {
                    $attElement = new \Zend\Form\Element\Select($attribute->code);
                    $optionValues = array();
                    if(isset($attribute->optionValues))
                    {
                        $optionValues = array();
                        foreach($attribute->optionValues as $optionValue)
                        {
                            $optionValues[$optionValue['value']] = $optionValue['text'];
                        }
                        $attElement->setValueOptions($optionValues);
                    }                    
                    
                }
                else {
                    $attElement = new \Zend\Form\Element\Text($attribute->code);
                }

                $attElement->setLabel($attribute->label);
                $attElement->setValue($attribute->getValue());
                $form->add($attElement);
            }  
            
            $viewModel =  new ViewModel([
                'form' => $form,
                'model' => $model,
                'attributes' => $attributes,
            ]);
            $viewModel->setTemplate('/pms/entity-definition/edit');
            return $viewModel;
        }
        
        $viewModel =  new ViewModel([
            'form' => $form,
            'model' => $model,
            'attributes' => array(),
        ]);
        $viewModel->setTemplate('/pms/entity-definition/edit');              
        return $viewModel;
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
    
    public function processAttributesAction()
    {
        $id = $this->params()->fromRoute('id');
        if(!isset($id))
            return $this->redirect()->toRoute('pms/entity-definition');
        $id = (int)$id;
        $post =  $this->request->getPost();
                        
        $entityDefinitionModel = $this->getServiceLocator()->get('EntityDefinitionModel');
        $entityDefinitionModel->setId($id);
        $attributes = $entityDefinitionModel->getAttributes();
                
        foreach($post as $key=>$value)
        {
            if(!$value || $key == 'submit')
                continue;
                        
            if(!array_key_exists($key, $attributes))
            {
                $entityDefinitionModel->addAttribute($key);
            }
        }        
                        
        foreach($attributes as $attribute)
        {
            if($post[$attribute->code] == FALSE)
            {
                $entityDefinitionModel->deleteAttribute($attribute->code);
            }
        }
        
        $entityDefinitionModel->save();
        
        return $this->redirect()->toRoute('pms/entity-definition', [
            'action' => 'edit',
            'id' => $id,
        ]);        
    }
    
    /**
     * This action will show the list of all attributes associated with the entity definitions
     * whose id is passed as argument.
     * @return ViewModel
     */
    public function previewAction()
    {
        $id = $this->params()->fromRoute('id');
        $id = (int)$id;
        $entityDefinitionModel = $this->getServiceLocator()->get('EntityDefinitionModel');
        $entityDefinitionModel->setId($id);
        $attributes = $entityDefinitionModel->getAttributes();

        return new ViewModel([
            'model' => $entityDefinitionModel,
            'id' => $id,
            'attributes' => $attributes,
        ]);
    }
    
    public function testAction()
    {        
        $type = $this->params()->fromQuery('type');
        if($type == 'new')
        {
            $edModel = $this->getServiceLocator()->get('EntityDefinitionModel');
            $edModel->setData([
                'code' => "TEST2",
                'name' => "Drugi Test objekate",
                'description' => "opis testa",            
            ]);
            $edModel->setEntityType(14);
            Debug::dump($edModel->getData());
            $edModel->save();    
        }
        
        if($type == 'preview')
        {
            $edModel = $this->getServiceLocator()->get('EntityDefinitionModel');
            $edModel->setId(9);
            Debug::dump($edModel->getAttributes());
        }
        
        die();
    }
}



