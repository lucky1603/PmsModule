<?php
/**
 * Login controller.
 * @description Controller which handles the login events.
 * @name LoginController.php
 * @author Sinisa Ristic <sinisa.ristic@gmail.com>
 * @date 11/2/2015.
 */

namespace Pms\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Debug\Debug;

/**
 * LoginController class.
 */
class LoginController extends AbstractActionController 
{
    /**
     * Auth service reference.
     * @var type 
     */
    protected $authService;
    /**
     * Main action.
     * @return ViewModel
     */
    public function indexAction()
    {
        $loginForm = $this->getServiceLocator()->get("LoginForm");
        return new ViewModel(['form' => $loginForm]);
    }
    
    /**
     * Process action. Called on form submit.
     * @return ViewModel
     */
    public function processAction()
    {     
        $user_data = $this->request->getPost('email');
        if(!$this->isMail($user_data))
        {
            $userTable = $this->getServiceLocator()->get('UserTable');
            $user = $userTable->getUserByName($user_data);
            if($user)
            {
                $mail = $user->email;
            }            
        }
        else 
        {
            $mail = $user_data;
        }
         
        if(isset($mail)) 
        {
            $authService = $this->getAuthService();
            $adapter = $authService->getAdapter();        
    //        $adapter->setIdentity($this->request->getPost('email'));
            $adapter->setIdentity($mail);
            $adapter->setCredential($this->request->getPost('password'));               
            $result = $authService->authenticate();

            if($result->isValid()) {
                //$authService->getStorage()->write($this->request->getPost('email'));
                $authService->getStorage()->write($mail);
                return $this->redirect()->toRoute(NULL);
            }

            return new ViewModel([
                'mail' => $this->request->getPost('email'),
            ]);
        }
        
        return new ViewModel([
            'mail' => $user_data,
        ]);
    }
    
    /**
     * Confirm action. Called upon the successfull validation of the user data.
     * @return ViewModel
     */
    public function confirmAction()
    {
        $user_email = $this->getAuthService()->getStorage()->read();
        $userTable = $this->getServiceLocator()->get('UserTable');
        $user = $userTable->getUserByEMail($user_email);

        if($user->role_id == 1)
        {
            $this->redirect()->toRoute(NULL, [
                'controller' => 'admin',
                'action' => 'index',
            ]);
        }
        return $viewModel;
    }
    
    /**
     * Gets authentication service.
     * @return Authentication service.
     */
    public function getAuthService()
    {
        if(! $this->authService) {
            $this->authService = $this->getServiceLocator()->get('AuthenticationService');
        }
        return $this->authService;
    }
    
    private function isMail($user)
    {
        if(strpos($user, '@') != false)                
        {
            return true;
        }
        
        return false;
    }
}

