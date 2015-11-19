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
use Zend\View\Model\JsonModel;

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
        if(isset($id))
        {
            $model = $this->getServiceLocator()->get("ReservationModel");
            $model->setId($id);
//            Debug::dump($model);
            $form->bind($model);
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
        Debug::dump($post);
        $model = $this->getServiceLocator()->get("ReservationModel");
        
        $id = $this->params()->fromRoute('id');           
        if(isset($id))
        {            
            $model->setId($id);
                                    
        }
        
        $form->bind($model);
        $form->setData($post);
        if($form->isValid())
        {
            $model->save();
        }
        
        return $this->redirect()->toRoute('pms/reservation', []);
    }
    
    /**
     * Adding, editing, removing entity from reservation.
     * @return ViewModel Default view model.
     */
    public function entityAction()
    {
        $form = $this->getServiceLocator()->get('ReservationEntityForm');
        
        return new ViewModel([
            'form' => $form,
        ]);
    }
    
}
