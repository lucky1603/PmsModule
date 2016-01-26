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
use Zend\Session\Container;
use Pms\Model\EntityReservationModel;

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
    
    public function testSortAction()
    {
        
    }
    
    /**
     * Returns the room list with attribute values.
     * @return ViewModel Corresponding view model.
     */
    public function fullListAction()
    {
        if($this->request->isPost())
        {
            $post = $this->request->getPost();
            $date = $post['date_from'];
            $entityTypeId = $post['entity_type_id'];
            $resolution = $post['resolution'];
            if(isset($post['multi-select']))
            {
                $multiSelect = $post['multi-select'];
            }
        }
        
        // Prepare the form entries.
        // Get entity type. Find ACUNIT if exists. If not, take the first from the list of entities.
        $table = $this->getServiceLocator()->get('EntityTypeTable');
        
        if(isset($entityTypeId))
        {
            $entityType = $table->getEntityType($entityTypeId);
        }
        else 
        {
            $entityType = $table->getEntityTypeByName('ACUNIT');
            $entityTypeId = $entityType->id;
        }
        
        if(empty($entityType))
        {
            $rows = $table->fetchAll();
            if(count($rows) > 0)
            {
                $entityTypeId = $rows->current()[0]['id'];
                $entityType = $table->getEntityType($entityTypeId);
            }
        }
        
        if(empty($entityType))
        {
            // TODO later.
        }
        
        if(empty($date))
        {
            // Date and time today
            $date = date('Y-m-d', time());
            $date = date('Y-m-d H:i', strtotime('+ 8 hours', strtotime($date)));
        }
        
        if(empty($resolution))
        {
            // Default time resolution.
            $resolution = $entityType->time_resolution;
        }
                
        // Attributes
        $dbAdapter = $this->getServiceLocator()->get('Adapter');
        $sql = new Sql($dbAdapter);
        $select = $sql->select();
        $select->from(['eta' => 'entity_type_attribute'])
               ->join(['a' => 'attribute'], 'eta.attribute_id=a.id', ['code', 'label'])
               ->where(['entity_type_id' => $entityType->id]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $rows = $statement->execute();
        $attributes = array();
        foreach($rows as $row)
        {
            $attributes[$row['code']] = $row['label'];
        }      
                
        $form = $this->getServiceLocator()->get('AvailabilityForm');
        $mselect = $form->get('multi-select');
        $mselect->setValueOptions($attributes);
                
        $formData = array();
        $formData['date_from'] = $date;
        $formData['entity_type_id'] = $entityTypeId;
        
        if(isset($multiSelect))
        {
            $formData['multi-select'] = $multiSelect;
        }
        else 
        {
            $formData['multi-select'] = array();
        }
        
        $formData['resolution'] = $resolution;
                                               
        $form->setData($formData);
        $viewModel = new ViewModel([
            'form' => $form,
            'typeName' => $entityType->name,
        ]);
        
        return $viewModel;
    }
    
    /**
     * REMARK: Old version of the function. I wanted to save the code though. 
     * That is why it is archived here.
     * Returns the room list with attribute values.
     * @return ViewModel
     */
    public function fullListActionArchive()
    {                 
        if($this->request->isPost())
        {
            $post = $this->request->getPost();
            $typeId = $post['entity_type_id'];
            $table = $this->getServiceLocator()->get('EntityTypeTable');
            $entity = $table->getEntityType($typeId);
            $typeName = $entity->name;           
            $startDate = date('Y-m-d', strtotime($post['date_from']));
            $startTime = date('H:i:s', strtotime($post['date_from']));      
            $resolution = $post['resolution'];
            if(isset($post['multi-select']))
            {
                $attrs = $post['multi-select']; 
               
            }
            else 
            {
                $attrs = array();
            }
            
            $session = new Container('models');
            $session->attrs = $attrs;
                
            $sort = $post['sort'];
            if(!isset($sort))
            {            
                $sort = 'guid';
            }                  
        }
        else 
        {
            $typeId = $this->params()->fromRoute('id');
            if(empty($typeId))
            {
                $typeId = $this->params()->fromQuery('id');
                $table = $this->getServiceLocator()->get('EntityTypeTable');
                if(empty($typeId))
                {
                    $entityType = $table->getEntityTypeByName('ACUNIT');
                    if(isset($entityType))
                    {
                        $typeId = $entityType->id;
                    }
                    else 
                    {
                        $typeId = $table->fetchAll()->current()->id;
                        $entityType = $table->getEntityType($typeId);
                    }             
                    
                    $resolution = $entityType->time_resolution; 
                }
                
                $entity = $table->getEntityType($typeId);
                $resolution = $entity->time_resolution;
                $typeName = $entity->name;         
                $sort = $this->params()->fromQuery('sort'); 
                if(!isset($sort))
                {
                    $sort = 'guid';
                }
                $startDate = $this->params()->fromQuery('startDate');
                if(!isset($startDate))
                {                    
                    $startDate = date('Y-m-d', time());
                }
                $startTime = $this->params()->fromQuery('startTime');
                if(!isset($startTime))
                {                
                    $startTime = date('H:i:s', time());
                }
                $session = new Container('models');
                if(isset($session->attrs))
                {
                    $attrs = $session->attrs;
                }          
                else 
                {
                    $attrs = [];
                }
            }
        }
                        
        $table = $this->getServiceLocator()->get('EntityTable');    
        if(isset($sort))
        {
            $results = $table->fetchView($typeId, $sort);
        }
        else 
        {
            $results = $table->fetchView($typeId);
        }
        
//        $endDate = date('Y-m-d', strtotime('+6 days', strtotime($startDate)));        
        $adapter = $this->getServiceLocator()->get('Adapter');
        
        $lines = array();
        $index = array();
        $attList = array();
        foreach($results as $row)
        {                        
            $line = array();
            $id = $row['id'];
            $model = new EntityReservationModel($adapter);
            $model->setId($id);
            $time_resolution = $model->getTimeResolution();
            switch ($time_resolution) {
                case 1:
                    $startPeriod = $startDate;
                    $endPeriod = date('Y-m-d', strtotime('+6 days', strtotime($startDate)));      
                    break;
                default:
                    if(isset($startTime))
                    {
                        $startPeriod = $startDate.' '.$startTime;
                    }
                    else 
                    {
                        $startPeriod = date('Y-m-d H:i:s', strtotime($startDate));
                    }                    
                    $endPeriod = date('Y-m-d H:i:s', strtotime('+23 hours', strtotime($startDate)));      
                    break;
            }
            if(isset($startPeriod) && isset($endPeriod))
            {
                $model->setPeriod($startPeriod, $endPeriod);
            }
            $line['guid'] = $model->guid;
            $line['code'] = $row['code'];
            $line['status'] = $model->status;
            $attributes = $model->getAllAttributes();
//            \Zend\Debug\Debug::dump($attributes);            
            $mAttList = $model->getAttributesList();
            \Zend\Debug\Debug::dump($mAttList);
            die();
            foreach($attributes as $attribute)
            {
                if(!isset($attList[$attribute->code]))
                {
                    $attList[$attribute->code] = $attribute->label;
                }
                
                if(!in_array($attribute->code, $attrs))
                {
                    continue;
                }
                
                if($attribute->type == "boolean")
                {
                    $line[$attribute->code] = $attribute->value == 1 ? "Yes" : "No";
                }
                else if($attribute->type == "select")
                {
                    $line[$attribute->code] = $attribute->optionValues[$attribute->value];
                }
                else 
                {
                    $line[$attribute->code] = $attribute->value;
                }                
            }
            
            $reservations = $model->getReservations();         
            
            $current = strtotime($startDate);
            foreach($reservations as $key=>$value)
            {
                $line[$key] = $value;                                
            }

            if(isset($sort))
            {
                $key = $line[$sort];
                $index[$line['guid']] = $key;
            }
            else 
            {
                $key = $line['guid'];
                $index[$line['guid']] = $key;
            }                        
                        
            $lines[$line['guid']] = $line;
        }     
//        \ksort($lines);        
        asort($index);
        $ilines = array();
        foreach($index as $key => $value)
        {
            $ilines[] = $lines[$key];
        }           
                
        $form = $this->getServiceLocator()->get('AvailabilityForm');
        $mcheckbox = $form->get('multi-checkbox');
        $mcheckbox->setValueOptions($attList);
        $mselect = $form->get('multi-select');
        $mselect->setValueOptions($attList);
        $sorter = $form->get('sort');
        $sortAttrs = array('guid' => 'guid', 'code' => 'code', 'status' => 'status');
        foreach($attrs as $attr)
        {
            $sortAttrs[$attr] = $attr;
        }
        $sorter->setValueOptions($sortAttrs);
        
        \Zend\Debug\Debug::dump($attrs);
        
        $formData = array();
        $formData['date_from'] = $startDate;
        $formData['entity_type_id'] = $typeId;
        $formData['multi-checkbox'] = $attrs;
        $formData['multi-select'] = $attrs;
        $formData['sort'] = $sort;
                
        $form->setData($formData);
        $viewModel = new ViewModel([
            'data' => $ilines,
            'form' => $form,
        ]);        
        if(isset($startPeriod) && isset($endPeriod))
        {
            $viewModel->setVariable('startDate', $startPeriod);
            $viewModel->setVariable('endDate', $endPeriod);
        }        
        if(isset($typeName))
        {
            $viewModel->setVariable('typeName', $typeName);
        }                
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
