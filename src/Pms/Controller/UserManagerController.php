<?php

namespace Pms\Controller;

use Pms\Model\User;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class UserManagerController extends AbstractActionController
{
    public function indexAction() {
        $userTable = $this->getServiceLocator()->get('UserTable');
        $users = $userTable->fetchView();
        $viewModel = new ViewModel(array(
            'users' => $users,
        ));
        return $viewModel;
    }
    
    public function editAction()
    {
        $userTable = $this->getServiceLocator()->get('UserTable');
        $user = $userTable->getUser($this->params()->fromRoute('id'));
        $form = $this->getServiceLocator()->get('RegisterForm');
        $form->bind($user);
        $viewModel = new ViewModel([
           'form' => $form,
            'user_id' => $this->params()->fromRoute('id')
        ]);
        return $viewModel;
    }
    
    public function processAction()
    {
        $post = $this->request->getPost();
        $form = $this->getServiceLocator()->get("RegisterForm");
        $userTable = $this->getServiceLocator()->get("UserTable");
        $id = (int) $this->params()->fromRoute('id');
        \Zend\Debug\Debug::dump($id);
        if(isset($id) && $id != 0)
        {
            $user = $userTable->getUser($id);
            $userTable = $this->getServiceLocator()->get('UserTable');
            $form->bind($user);
            $form->setData($post);
            if($form->isValid())
            {         
                $userTable->saveUser($user);
            }
        }
        else 
        {
            $form->setData($post);          
            \Zend\Debug\Debug::dump($post);
   
            if($form->isValid())
            {             
                $user = new User();
                $user->exchangeArray($form->getData());
                \Zend\Debug\Debug::dump($user);
                $userTable->saveUser($user);
            }
        }
        
        return $this->redirect()->toRoute('pms/user-manager', [
                'action' => 'index'
        ]);
    }
    
    public function deleteAction()
    {
        $usersTable = $this->getServiceLocator()->get("UserTable");
        $id = (int) $this->params()->fromRoute('id');
        $user = $usersTable->getUser($id);
        if($user)
        {
            $usersTable->deleteUser($id);
            return new ViewModel([
               'user' => $user,
               'id' => $id,
            ]);
        }
        
        return new ViewModel([
            'error' => "There is no such user -> id = " . $id,
        ]);
    }
    
    public function newAction()
    {
        $registerForm = $this->getServiceLocator()->get('RegisterForm');
        $viewModel = new ViewModel([
            'form' => $registerForm,
        ]);
        $viewModel->setTemplate('pms/user-manager/edit');
        return $viewModel;
    }
}
