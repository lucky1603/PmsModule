<?php
/**
 * @name ReservationController - Class which handles the reservations management.
 * @author Dragutin Jovanovic <gutindra@gmail.com>
 * @date 17.11.2015.
 */

namespace Pms\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Debug\Debug;
use Zend\Session\Container;
use Zend\Db\Sql\Sql;

/**
 * ReservationController
 */
class ReservationController extends AbstractActionController
{
    /**
     * Default action.
     */
    public function indexAction() {
        $model = $this->getServiceLocator()->get('ReservationModel');        
        $reservations = $model->fetchAll();        
        return new ViewModel([
            'reservations' => $reservations,
        ]);
    }
    
    /**
     * Edit existing reservation.
     * @return ViewModel Default view model.
     */
    public function editAction() {
        $form = $this->getServiceLocator()->get('ReservationForm');
        $id = $this->params()->fromRoute('id');     
        $model = $this->getServiceLocator()->get("ReservationModel");
        $sessionModels = new Container('models');
        
        if(isset($sessionModels->reservationModel))
        {
            $reservationModelData = $sessionModels->reservationModel;
            if(isset($id))
            {
                $id = (int)$id;
                $reservation_id = (int)$reservationModelData['reservation_id'];
                if($id != $reservation_id)
                {
                    Debug::dump('They are not equal');
                    $model->setId($id);
                    $sessionModels->reservationModel = $model->getData();
                }
                else 
                {
                    $model->setData($reservationModelData);
                }
                
                $form->bind($model);
                $viewModel = new ViewModel([
                    'form' => $form,
                    'id' => $id,
                    'model' => $model,
                ]);
                return $viewModel;
            }
            
        }
        
        if(isset($id))
        {            
            $model->setId($id);
            $form->bind($model);                     
            $sessionModels->reservationModel = $model->getData();
                        
            $viewModel = new ViewModel([
                'form' => $form,
                'id' => $id,
                'model' => $model,
            ]);
            return $viewModel;
        }
        
        return $viewModel = new ViewModel([
            'form' => $form,
        ]);
    }
    
    /**
     * Processes actions on reservation.
     * @return ViewModel Default view model.
     */
    public function processAction()
    {
        $form = $this->getServiceLocator()->get('ReservationForm');
        $post = $this->request->getPost();
//        Debug::dump($post);
        $model = $this->getServiceLocator()->get("ReservationModel");
        $sessionModels = new Container('models');
        if(isset($sessionModels->reservationModel))
        {
            $reservationModelData = $sessionModels->reservationModel;
            $model->setData($reservationModelData);
            $form->bind($model);
            $form->setData($post);
            if($form->isValid())
            {
//                Debug::dump($model->getData());
//                Debug::dump("guest is ".$model->client_id.' '.$model->first_name);
                $model = $form->getData();
                $model->save();
                unset($sessionModels->reservationModel);
            }
            else {
                Debug::dump('Form is not valid...');
                Debug::dump($form);
            }            
        }
        else 
        {
            // It will probably not enter here anymore, but let it be for now.
            $id = $this->params()->fromRoute('id');           
            if(isset($id))
            {            
                $model->setId($id);                                    
            }

            $form->bind($model);
            $form->setData($post);
            if($form->isValid())
            {
                $model = $form->getData();
                $model->save();
            } 
        }
        return $this->redirect()->toRoute('pms/reservation');
    }
    
    /**
     * Adding, editing, removing entity from reservation.
     * @return ViewModel Default view model.
     */
    public function entityAction()
    {
        $sessionModels = new Container('models');
        $reservationModelData = $sessionModels->reservationModel;

        $reservationModel = $this->getServiceLocator()->get('ReservationModel');
        $reservationModel->setData($reservationModelData);
        
        $form = $this->getServiceLocator()->get('ReservationEntityForm');
        $id = $this->params()->fromRoute('id');
        if(isset($id))
        {
            $reservedEntities = $reservationModel->getReservedEntities();
            $entity = $reservedEntities[$id];
            $form->bind($entity);

            if($form->isValid())
            {
                $viewModel = new ViewModel([
//                    'entity' => $entity,      
                    'form' => $form,
                    'id' => $id,
                ]);
                return $viewModel;
            }
            else {
               \Zend\Debug\Debug::dump("Wrong input format, check the form data...");
//               \Zend\Debug\Debug::dump($entity);
               \Zend\Debug\Debug::dump($form);
            }
            
            
        }
        return new ViewModel([
            'form' => $form,
        ]);
    }
    
    /**
     * Process reservation entity changes (add new, edit).
     * @return Redirect Redirects to 'edit' action.
     */
    public function processEntityAction()
    {        
        $post = $this->request->getPost();
        $id = $this->params()->fromRoute('id');
        $form = $this->getServiceLocator()->get("ReservationEntityForm");
        $sessionModels = new Container('models');
        $reservationModelData = $sessionModels->reservationModel;
        $reservationModel = $this->getServiceLocator()->get('ReservationModel');
        $reservationModel->setData($reservationModelData);
        if(isset($id))
        {
            $entity = $reservationModel->getReservedEntities()[$id];    
            $form->bind($entity);
            $form->setData($post);
            if($form->isValid())
            {
                $entity = $form->getData();
                $entity->setReservationId($reservationModel->reservation_id);
                $reservationModel->reservedEntities[$id] = $form->getData();
            }
            else 
            {
                Debug::dump('UPDATE:Invalid form entry...');
                Debug::dump($post);
            }
        }
        else 
        {
            $form->setData($post);
            if($form->isValid())
            {
                $adapter = $this->getServiceLocator()->get('Adapter');
                $entity = new \Pms\Model\ReservationEntityModel($adapter);
                $entity->setData($post);
                $entity->setReservationId($reservationModel->reservation_id);
                // create internal id....
                $entity->internal_id = $reservationModel->getNewInternalId();
                $reservationModel->addEntity($entity);                
            }
            else 
            {
                Debug::dump('UPDATE:Invalid form entry...');
                Debug::dump($post);
            }                              
        }                     
        
        $reservationModelData = $reservationModel->getData();      
//        Debug::dump($reservationModelData);
//        die();
        $sessionModels->reservationModel = $reservationModelData;      
        $rid = (int) $reservationModel->reservation_id;        

        return $this->redirect()->toRoute('pms/reservation', [
            'action' => 'edit',
            'id' => $rid,
        ]);
    }
    
    /**
     * Test action (one only use). Copies the dates from string/int -> timestamp fomatted fields.
     * TODO: To remove later.
     */
    public function testAction()
    {
       $dbAdapter = $this->getServiceLocator()->get('Adapter');
       $sql = new Sql($dbAdapter);
              
       // Get id's 
       $ids = array();
       $select = $sql->select();
       $select->from('reservation_entity')
              ->columns(['id', 'date_start', 'date_end']);
       $statement = $sql->prepareStatementForSqlObject($select);
       $results = $statement->execute();
       foreach($results as $row)
       {
           $date_from = date('m/d/Y', (int) $row['date_start']);
           $date_to = date('m/d/Y', $row['date_end']);
           Debug::dump($row['date_start']);
           Debug::dump($row['date_end']);
           Debug::dump($date_from);
           Debug::dump($date_to);
           
           
           $update = $sql->update();
           $update->table('reservation_entity')
                  ->set(['date_from' => $date_from,
                         'date_to' => $date_to])
                  ->where(['id' => $row['id']]);
           $statement = $sql->prepareStatementForSqlObject($update);
           $statement->execute();
       }
      
       die('conversion done!');
    }
}
