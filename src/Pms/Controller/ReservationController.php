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
}
