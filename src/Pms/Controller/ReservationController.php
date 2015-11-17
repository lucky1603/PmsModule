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
}
