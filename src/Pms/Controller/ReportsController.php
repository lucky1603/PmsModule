<?php
/**
 * Reports controller. Controller for setup of various reports.
 * @author John Doe <john.doe@example.com>
 * @date 25.12.2015.
 */
namespace Pms\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Class ReportsController.php
 */
class ReportsController extends AbstractActionController
{
    /**
     * Index action.
     * @return \Pms\Controller\ViewModel
     */
    public function indexAction() {
        return new ViewModel();
    }
    
    /**
     * Complete usage of all entities.
     */
    public function completeUsageAction()
    {
        $adapter = $this->getServiceLocator()->get('Adapter');
        $model = new \Pms\Model\EntityReportModel($adapter);
        $usageData = $model->getCompleteEntityUsageData(/*22*/);
        
        return new ViewModel([
            'usageData' => $usageData,
        ]);        
    }
    
    public function entityUsageAction()
    {
        $id = $this->params()->fromQuery('id');
        
        $adapter = $this->getServiceLocator()->get('Adapter');
        $model = new \Pms\Model\EntityReportModel($adapter);
        $usageData = $model->getSingleEntityUsageData($id);
        
        return new ViewModel([
            'usageData' => $usageData,
        ]);
    }
}

