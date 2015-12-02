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
use Zend\Session\Container;

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
        $entityTypeModel = $this->getServiceLocator()->get('EntityTypeModel');
        $session = new Container('models');        
        
        if(isset($session->entityTypeData))
        {
            $entityTypeData = $session->entityTypeData;

            if(isset($id))
            {
                $id = (int)$id;
                $entity_id = isset($entityTypeData['id']) ? $entityTypeData['id'] : null;
                
                if($id != $entity_id)
                {
//                    \Zend\Debug\Debug::dump('razlicito ... id = '.$id.', entity_id = '.$entity_id);
                    $entityTypeModel->setId($id);
                    $session->entityTypeData = $entityTypeModel->getData();
                }
                else 
                {
                    $entityTypeModel->setData($entityTypeData);
                }
                                
                $form->bind($entityTypeModel);
                return new ViewModel([
                    'form' => $form,
                    'id' => $id,
                    'model' => $entityTypeModel,
                ]);
            }
            else 
            {
                if(empty($entityTypeData['id']))
                {
                    $entityTypeModel->setData($entityTypeData);
                }
                else {
                    $session->entityTypeData = $entityTypeModel->getData();
                }
                 
                 $form->bind($entityTypeModel);
                 return new ViewModel([
                     'form' => $form,
                     'model' => $entityTypeModel,
                 ]);
            }
        }
        
        if(isset($id))
        {
            $id = (int)$id;
            $entityTypeModel->setId($id);
            $session->entityTypeData = $entityTypeModel->getData();            
            $form->bind($entityTypeModel);
            $viewModel = new ViewModel([
                'form' => $form,
                'id' => $id, 
                'model' => $entityTypeModel,
            ]);
            return $viewModel;
        }
        
        $session->entityTypeData = $entityTypeModel->getData();
        return new ViewModel([
            'form' => $form,
            'model' => $entityTypeModel,
        ]);
        
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
        $model = $this->getServiceLocator()->get("EntityTypeModel");
        $session = new Container('models');
        $entityTypeData = $session->entityTypeData;
        $model->setData($entityTypeData);
        
        $form->bind($model);
        $form->setData($post);
        if($form->isValid())
        {             
            $model->save();
            unset($session->entityTypeData);
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
    
    /**
     * Add new or edit existing attribute.
     * @return ViewModel
     */
    public function editAttributeAction()
    {
        $id = $this->params()->fromRoute('id');
        $form = $this->getServiceLocator()->get("AttributeForm");
        $session = new Container('models');
        $entityTypeData = $session->entityTypeData;
        $model = $this->getServiceLocator()->get('EntityTypeModel');
        $model->setData($entityTypeData);
        
        $aModel = $this->getServiceLocator()->get("AttributeModel");
        if(isset($id))
        {
            // edit
            $aModel = $model->attributes[$id];
            $form->bind($aModel);
            return new ViewModel([
                'form' => $form,
                'id' => $id,
                'model' => $aModel,
            ]);
        }
        else 
        {
            // add new
            return new ViewModel([
                'form' => $form,
            ]);
        }
    }
    
    /**
     * Processes action add/edit attribute.
     */
    public function processEditAttributeAction()
    {
        $id = $this->params()->fromRoute('id');
        $post = $this->request->getPost();
        $form = $this->getServiceLocator()->get('AttributeForm');
        $session = new Container('models');
        $entityTypeData = $session->entityTypeData;
        $model = $this->getServiceLocator()->get("EntityTypeModel");
        $model->setData($entityTypeData);
                        
        if(isset($id))
        {
            $aModel = $model->attributes[$id];
            $form->bind($aModel);
            $count = $post['counter'];
            for($i = 1; $i <= $count; $i++)
            {
                $val = $form->add([
                    'name' => 'val'.$i,
                    'attributes' => [
                        'type' => 'text', 
                        'id' => 'val'.$i,
                    ]
                ]);
                
                $val = $form->add([
                    'name' => 'text'.$i,
                    'attributes' => [
                        'type' => 'text', 
                        'id' => 'text'.$i,
                    ]
                ]);               
            }

            $form->setData($post);
            if($form->isValid())
            {
                $model->attributes[$id] = $aModel;                
                $session->entityTypeData = $model->getData();
            }
            
            
        }
        else 
        {
            \Zend\Debug\Debug::dump("new...");
            $aModel = $this->getServiceLocator()->get("AttributeModel");
            $form->bind($aModel);
            $count = $post['counter'];
            for($i = 1; $i <= $count; $i++)
            {
                $val = $form->add([
                    'name' => 'val'.$i,
                    'attributes' => [
                        'type' => 'text', 
                        'id' => 'val'.$i,
                    ]
                ]);
                
                $val = $form->add([
                    'name' => 'text'.$i,
                    'attributes' => [
                        'type' => 'text', 
                        'id' => 'text'.$i,
                    ]
                ]);
               
            }

            $form->setData($post);
            if($form->isValid())
            {
                $model->addAttribute($aModel);
                $session->entityTypeData = $model->getData();
            }
            
            
        }
        if(isset($model->id))
        {
            return $this->redirect()->toRoute('pms/entity-type', [
                'action' => 'edit',
                'id' => $model->id,
            ]);
        }

        return $this->redirect()->toRoute('pms/entity-type', [
            'action' => 'edit',
        ]);    
       
    }
    
    public function deleteAttributeAction()
    {
        $id = $this->params()->fromRoute('id');
        $session = new Container('models');
        $modelData = $session->entityTypeData;
        $model = $this->getServiceLocator()->get('EntityTypeModel');
        $model->setData($modelData);
        
//        \Zend\Debug\Debug::dump($model->getData());
//        die();
        unset($model->attributes[$id]);
        $session->entityTypeData = $model->getData();
        if(isset($model->id))
        {
            return $this->redirect()->toRoute('pms/entity-type', [
                'action' => 'edit', 
                'id' => $model->id,
            ]);
        }
        
        return $this->redirect()->toRoute('pms/entity-type', [
                'action' => 'edit', 
        ]);
    }
}

