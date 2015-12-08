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
use Pms\Model\EntityModel;
use Zend\Session\Container;
use Zend\Form\Exception\InvalidElementException;

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
        $form->setData($entityModel->getData());
//        Debug::dump($entityModel->getData());
//        die();
        
        if(isset($entityModel->attributes))
        {
            foreach($entityModel->attributes as $attribute)
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
                else {
                    $attElement = new \Zend\Form\Element\Text($attribute->code);
                }

                $attElement->setLabel($attribute->label);
                $attElement->setValue($attribute->getValue());
                $form->add($attElement);
            }
            
        }
        
        if(isset($entityModel->attributes))
        {
            $viewModel = new ViewModel([
                'form' => $form,
                'id' => $id,
                'attributes' => $entityModel->attributes,
            ]);
        }
        else 
        {
            $viewModel = new ViewModel([
                'form' => $form,
                'id' => $id,
            ]);    
        }

        return $viewModel;                        
    }
    
    /**
     * Processes changes to entity object. 
     */
    public function processAction()
    {
        $post = $this->request->getPost();
        $id = $this->params()->fromRoute('id');
        $entityModel = $this->getServiceLocator()->get('EntityModel');        
        $form = $this->getServiceLocator()->get('EntityForm');               
        if(isset($id))
        {
            $entityModel->setId($id);
            $attributes = $entityModel->attributes;
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
                    elseif ($attribute->type == 'timestamp') {
                        $attElement = new \Zend\Form\Element\DateTime($attribute->code);
                    }
                    elseif ($attribute->type == 'select')
                    {
                        $attElement = new \Zend\Form\Element\Select($attribute->code);
                        $attElement->setValueOptions($attribute->optionValues);                         
                    }
                    else {
                        $attElement = new \Zend\Form\Element\Text($attribute->code);
                    }

                    $attElement->setLabel($attribute->label);
                    $attElement->setValue($attribute->getValue());
                    $form->add($attElement);
                }  
                
                $form->bind($entityModel);
                $form->setData($post);
                if($form->isValid())
                {
                    $entityModel->save();
                }
            }
            else 
            {

            }
            
        }
        else 
        {
            $session = new Container('models');
//            Debug::dump($session->entityData);
//            die();
            $entityModel->setData($session->entityData);
            $attributes = $entityModel->attributes;
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
                    elseif ($attribute->type == 'timestamp') {
                        $attElement = new \Zend\Form\Element\DateTime($attribute->code);
                    }
                    elseif ($attribute->type == 'select')
                    {
                        $attElement = new \Zend\Form\Element\Select($attribute->code);
                        $attElement->setValueOptions($attribute->optionValues);                                                
                    }
                    else {
                        $attElement = new \Zend\Form\Element\Text($attribute->code);
                    }

                    $attElement->setLabel($attribute->label);
                    $attElement->setValue($attribute->getValue());
                    $form->add($attElement);
                }                
            }
            
            $form->bind($entityModel);
            $form->setData($post);
            if($form->isValid())
            {
                $entityModel->save();
            }
        }
        
        //return $this->redirect()->toRoute()
        

//        $table = $this->getServiceLocator()->get('EntityTable');
//        if(!isset($id))
//        {
//            $entity = new Entity();
//            $entity->exchangeArray($post);
//            $entityTable->saveEntity($entity);
//        }
//        else
//        {
//            $form = $this->getServiceLocator()->get("EntityForm");
//            $entity = $entityTable->getEntity($id);
//            Debug::dump($id);
//            $form->bind($entity);
//            $form->setData($post);
//            if($form->isValid())
//            {
//                $entityTable->saveEntity($entity);
//            }
//            else 
//            {
//                throw new \Exception("Invalid form data!");
//            }
//        }
//        
        return $this->redirect()->toRoute('pms/entity', [
            'action' => 'index'
        ]);        
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
    
    public function newAction()
    {
        $dbAdapter = $this->getServiceLocator()->get('Adapter');
        $table = new \Zend\Db\TableGateway\TableGateway('entity_definition', $dbAdapter);
        $sql = new Sql($dbAdapter);
        $select = $sql->select();
        $select->from(['ed' => "entity_definition"])
                ->join(['et' =>'entity_type'], 'ed.entity_type_id=et.id', ['type' => 'name']);
        $statement = $sql->prepareStatementForSqlObject($select);
        $entity_definitions = $statement->execute();

        return new ViewModel([
            'entity_definitions' => $entity_definitions,
        ]);
    }
    
    public function processNewAction()
    {
        $id = $this->params()->fromRoute('id');
        $dbAdapter = $this->getServiceLocator()->get('Adapter');
        $model = $this->getServiceLocator()->get('EntityModel');
        $model->setEntityDefinitionId($id);
        $session = new Container('models');
        $session->entityData = $model->getData();    
                
        $form = $this->getServiceLocator()->get('EntityForm');
        $form->setData($model->getData());
        //$form->bind($model);      
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
            $viewModel->setTemplate('/pms/entity/edit');
            return $viewModel;
        }
        
        $viewModel =  new ViewModel([
            'form' => $form,
            'model' => $model,
            'attributes' => array(),
        ]);
        $viewModel->setTemplate('/pms/entity/edit');              
        return $viewModel;

    }
}
