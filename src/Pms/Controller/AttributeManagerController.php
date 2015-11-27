<?php
/**
 * @name AttributeManagerController - Controller for attribute management.
 * @author Dragutin Jovanovic<gutindra@gmail.com>
 * @date 12.11.2015.
 */
namespace Pms\Controller;

use Pms\Model\Attribute;
use Pms\Model\AttributeModel;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Form\Form;

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
            
    public function edefAction()
    {
        $id = $this->params()->fromRoute('id');
        if(!isset($id))
        {
            return $this->redirect()->toRoute('pms/entity-definition');
        }
        $id = (int)$id;
        $attributeTable = $this->getServiceLocator()->get('AttributeTable');
        $attributes = $attributeTable->fetchAll()->toArray();        
        $entityDefModel = $this->getServiceLocator()->get('EntityDefinitionModel');
        $entityDefModel->setId($id);
        $selectedAttributes = $entityDefModel->getAttributes();
        $form = new \Zend\Form\Form("EdefForm");
        $form->setAttributes([
            'method' => 'post',
            'enctype' => 'multipart/form-data',
        ]);
                
        foreach($attributes as $attribute)
        {
            $checkBox = new \Zend\Form\Element\Checkbox($attribute['code']);
            $checkBox->setLabel($attribute['label'].' ('.$attribute['type'].')');            
            $checkBox->setLabelOption('placement', 'append');
            if(array_key_exists($attribute['code'], $selectedAttributes))
            {
                $checkBox->setValue(TRUE);
            }
            $form->add($checkBox);
        }
        
        $submit = new \Zend\Form\Element\Submit('submit');
        $submit->setLabel('submit');
        $submit->setValue('Save Changes');
        $form->add($submit);
        
        $viewModel = new ViewModel([
            'form' => $form,
            'id' => $id,
        ]);
        return $viewModel;
    }
    
    public function testAction()
    {
        $dbAdapter = $this->getServiceLocator()->get('Adapter');
        $type = $this->params()->fromQuery('type');
        if($type == 'add')
        {
            //        // List existing...
            
            $attributeModel = new AttributeValueModel($dbAdapter);
            $attributeValueModel->setId(28);

            $adapter = $this->getServiceLocator()->get('Adapter');
            $model = new \Pms\Model\EntityTypeModel($adapter);   
            $model->setId(6);
            //$model->setId(1);
    //        \Zend\Debug\Debug::dump($model->getData());
//            $model->setData([
//                'name' => 'Jedrilica',
//                'description' => 'Jedrilica za iznajmljivanje',
//            ]);
            $model->addAttribute($attributeModel);

//            $attModel2 = new AttributeModel($adapter);
//            $attModel2->setData([
//                'code' => 'mytest',
//                'label' => "My own test",
//                'type' => 'double',
//                'sort_order' => 1,
//                'unique' => false,
//                'nullable' => true,
//            ]);
//            $model->addAttribute($attModel2);
            \Zend\Debug\Debug::dump($model->getData());      

            $model->save();        
            die();
        }
        else if ($type == 'delete')
        {
            $model = new \Pms\Model\EntityTypeModel($dbAdapter);       
            $model->setId(14);
            \Zend\Debug\Debug::dump($model->getData());
            $model->removeAttribute(28);
            \Zend\Debug\Debug::dump($model->getData());
            $model->save();
            die();
        }
        
        die();
    }
}



