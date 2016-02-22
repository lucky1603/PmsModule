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
        $service = $this->getServiceLocator()->get('AuthenticationService');
        $mail = $service->getStorage()->read();
        $users = $this->getServiceLocator()->get('UserTable');
        $user = $users->getUserByEmail($mail);
        
        $adapter = $this->getServiceLocator()->get('Adapter');
        $model = new \Pms\Model\EntityReportModel($adapter);
        // Prepare form.
        $form = $this->getServiceLocator()->get('ReportFilterForm');
        $entityTypeTable = $this->getServiceLocator()->get('EntityTypeTable');
        $types = $entityTypeTable->fetchForUser($user->id)->toArray();
        $valueOptions = array();
        foreach($types as $type)
        {
            $valueOptions[$type['id']] = $type['name'];
        }        
        $type = $form->get('type');
        $type->setValueOptions($valueOptions);
        
        if($this->request->isPost())
        {
            $post = $this->request->getPost();
            $entityType = $post['type'];
            if(isset($post['start']))
            {
                $start = $post['start'];
            }
            if(isset($post['end']))
            {
                $end = $post['end'];
            }
            
            $form->setData($post);
            if($form->isValid())
            {
                $usageData = $model->getCompleteEntityUsageData($entityType, $start, $end, $user->id);
            }
            else if(isset($entityType) && isset($start))
            {
                $usageData = $model->getCompleteEntityUsageData($entityType, $start, NULL, $user->id); 
            }                        
            else if (isset($entityType))
            {
                $usageData = $model->getCompleteEntityUsageData($entityType, NULL, NULL, $user->id); 
            }
        }
        else 
        {
            $usageData = $model->getCompleteEntityUsageData(/*22*/); 
        }
                        
        // Call view model.
        $viewModel = new ViewModel([
            'usageData' => $usageData,
            'form' => $form,
        ]);        
        
        if(isset($start))
        {
            $viewModel->setVariable('start', $start);
        }
        
        if(isset($end))
        {
            $viewModel->setVariable('end', $end);
        }
        
        return $viewModel;
    }
    
    public function entityUsageAction()
    {
        $id = $this->params()->fromQuery('id');
        $start = $this->params()->fromQuery('start');
        $end = $this->params()->fromQuery('end');
        
        $adapter = $this->getServiceLocator()->get('Adapter');
        $model = new \Pms\Model\EntityReportModel($adapter);
        $usageData = $model->getSingleEntityUsageData($id, $start, $end);
        
        return new ViewModel([
            'usageData' => $usageData,
        ]);
    }
}

