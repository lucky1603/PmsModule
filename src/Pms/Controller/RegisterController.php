<?php
/**
 * Register controller.
 * @description Controller which handles the user registration.
 * @name RegisterController.php
 * @author Sinisa Ristic <sinisa.ristic@gmail.com>
 * @date 11/2/2015.
 */

namespace Pms\Controller;

use Pms\Model\User;
use Pms\Model\UserTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Register controller class.
 */
class RegisterController extends AbstractActionController
{
    /**
     * Main action.
     * @return ViewModel
     */
    public function indexAction() {
        $form = $this->getServiceLocator()->get("RegisterForm");
        $viewModel = new ViewModel(['form' => $form]);
        return $viewModel;
    }
    
    /**
     * Process action. Receivec the form input and processes it.
     * @return ViewModel
     */
    public function processAction()
    {
        if(!$this->request->isPost()) {
            return $this->redirect()->toRoute(NULL, [
                'controller' => 'register', 
                'action' => 'index'
            ]);
        }
        
        $post = $this->request->getPost();
        $form = $this->getServiceLocator()->get('RegisterForm');
        $filter= $this->getServiceLocator()->get("RegisterFilter");
        $form->setInputFilter($filter);
        $form->setData($post);
        if(!$form->isValid())
        {
            $viewModel = new ViewModel([
                'error' => true,
                'form' => $form,
            ]);
            $viewModel->setTemplate('pms/register/index');
            return $viewModel;
        }
        
        $this->createUser($form->getData());
        
        $model = new ViewModel([
           'userData' => $form->getData(),  
        ]);
        
        $model->setTemplate('pms/register/confirm');
        return $model;
    }
   
    /**
     * Writes the user to the data table.
     * @param type $data User data.
     * @return boolean True if succeeds.
     */
    public function createUser($data)
    {
        $user = new User();
        $user->exchangeArray($data);
        $userTable = $this->getServiceLocator()->get("UserTable");
        $userTable->saveUser($user);
        return true;
    }
}