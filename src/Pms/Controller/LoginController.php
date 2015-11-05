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
        $authService = $this->getAuthService();
        $adapter = $authService->getAdapter();        
        $adapter->setIdentity($this->request->getPost('email'));
        $adapter->setCredential($this->request->getPost('password'));               
        $result = $authService->authenticate();
        
        if($result->isValid()) {
            $authService->getStorage()->write($this->request->getPost('email'));
            return $this->redirect()->toRoute(NULL, ['controller' => 'login', 'action' => 'confirm']);
        }
        
        return new ViewModel([
            'mail' => $this->request->getPost('email'),
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
        $results = $userTable->fetchView($user->id);
        $viewModel = new ViewModel(['user' => $results[0]]);
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
}

