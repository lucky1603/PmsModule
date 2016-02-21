<?php

namespace Pms\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Debug\Debug;

class BusinessController extends AbstractActionController
{
    public function indexAction() {
        $model = $this->getServiceLocator()->get('BusinessModel');
        
        $authentication = $this->getServiceLocator()->get('AuthenticationService');
        $mail = $authentication->getStorage()->read();
        $users = $this->getServiceLocator()->get('UserTable');
        $user = $users->getUserByEmail($mail);

        
        
        $model->setUserId($user->id);

        
        return new ViewModel([
            'model' => $model,
        ]);
    }
    
    public function editAction()
    {
        $model = $this->getServiceLocator()->get('BusinessModel');
        
        $authentication = $this->getServiceLocator()->get('AuthenticationService');
        $mail = $authentication->getStorage()->read();
        $users = $this->getServiceLocator()->get('UserTable');
        $user = $users->getUserByEmail($mail);
        $model->setUserId($user->id);
        
        $form = $this->getServiceLocator()->get('BusinessForm');
        $form->get('user_id')->setValue($user->id);
        $form->bind($model);
//        Debug::dump($model->getData());    
//        Debug::dump($form);    
//        die();
        return new ViewModel([
            'form' => $form,
        ]);
    }
    
    public function processAction()
    {        
        $post = $this->request->getPost();

        
        $model = $this->getServiceLocator()->get('BusinessModel');
        
        $authentication = $this->getServiceLocator()->get('AuthenticationService');
        $mail = $authentication->getStorage()->read();
        $users = $this->getServiceLocator()->get('UserTable');
        $user = $users->getUserByEmail($mail);
        $model->setUserId($user->id);

        
        $form = $this->getServiceLocator()->get('BusinessForm');
        $form->bind($model);
        $form->setData($post);
                
        if($form->isValid())
        {
            $model->save();
        }

        
        return $this->redirect()->toRoute('pms/business');
    }
}
