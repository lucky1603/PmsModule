<?php
/**
 * Main application controller.
 * @name IndexController.php
 * @author Sinisa Ristic <sinisa.ristic@gmail.com>
 * @date 11/2/2015.
 */

namespace Pms\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use Zend\Debug\Debug;

/**
 * IndexController class.
 */
class IndexController extends AbstractActionController
{
    /**
     * Main controller action.
     * @return ViewModel
     */
    public function indexAction()
    {       
       $service = $this->getServiceLocator()->get('AuthenticationService');
       $mail = $service->getStorage()->read();
       if(isset($mail))
       {
           $table = $this->getServiceLocator()->get('UserTable');
           $user = $table->getUserByEmail($mail);
           if($user->role_id == 1)
           {
               $viewModel = new ViewModel([
                   'username' => $user->username,
               ]);
               $viewModel->setTemplate('/pms/login/admin');
               return $viewModel;
           }
           if($user->role_id == 2)
           {
               $viewModel = new ViewModel([
                   'username' => $user->username,
               ]);
               $viewModel->setTemplate('/pms/login/power-user');
               return $viewModel;
           }
           if($user->role_id == 3)
           {
               $viewModel = new ViewModel([
                   'username' => $user->username,
               ]);
               $viewModel->setTemplate('/pms/login/user');
               return $viewModel;
           }
       }
       
       return new ViewModel();
    }
    
    /**
     * Login action.
     */
    public function loginAction()
    {
        $viewModel = new ViewModel();
        $viewModel->setTemplate('pms/index/index');
        return $viewModel;
    }
    
    /**
     * Register new user action.
     */
    public function registerAction()
    {
        $viewModel = new ViewModel();
        $viewModel->setTemplate('pms/index/index');
        return $viewModel;
    }
    
    /**
     * Logout action.
     * @return type
     */
    public function logoutAction()
    {
        $authService = $this->getServiceLocator()->get('AuthenticationService');
        $authService->getStorage()->write(NULL);
        return $this->redirect()->toRoute(NULL);
    }
}

