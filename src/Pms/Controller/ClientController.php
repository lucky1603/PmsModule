<?php
/**
 * @name ClientController - Controller class for manipulation of clients.
 * @author Dragutin Jovanovic <gutindra@gmail.com>
 * @date 16.11.2015.
 */
namespace Pms\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Pms\Model\Client;
use Zend\Session\Container;
use Zend\Debug\Debug;
use Zend\Db\Sql\Sql;

/**
 * ClientController class.
 */
class ClientController extends AbstractActionController
{
    /**
     * Default action.
     * @return ViewModel
     */
    public function indexAction()
    {
        $service = $this->getServiceLocator()->get('AuthenticationService');
        $mail = $service->getStorage()->read();
        $users = $this->getServiceLocator()->get('UserTable');
        $user = $users->getUserByEmail($mail);
        
        $table = $this->getServiceLocator()->get('ClientTable');
        $clients = $table->fetchAll($user->id)->toArray();
        return new ViewModel([
            'clients' => $clients,
        ]);
    }
    
    /**
     * Edit client data.
     * @return ViewModel Edit view model.
     */
    public function editAction()
    {
        $service = $this->getServiceLocator()->get('AuthenticationService');
        $mail = $service->getStorage()->read();
        $users = $this->getServiceLocator()->get('UserTable');
        $user = $users->getUserByEmail($mail);
        
        $id = $this->params()->fromRoute('id');
        $form = $this->getServiceLocator()->get('ClientForm');
        $form->get('user_id')->setValue($user->id);
        $clientTable = $this->getServiceLocator()->get('ClientTable');
        if(isset($id))
        {
            // edit            
            $client = $clientTable->getClient($id);
            $form->bind($client);
            $viewModel = new ViewModel([
                'form' => $form,
                'id' => $id,
            ]);
            
            return $viewModel;
        }
                
        return new ViewModel([
            'form' => $form,
        ]);        
    }
    
    /**
     * Process user action.
     */
    public function processAction()
    {
        $id = $this->params()->fromRoute('id');
        $table = $this->getServiceLocator()->get('ClientTable');
        $post = $this->request->getPost();
        $form = $this->getServiceLocator()->get('ClientForm');
        if(isset($id))
        {
            $client = $table->getClient($id);
            $form->bind($client);
            $form->setData($post);
            if($form->isValid())
            {
                $table->saveClient($client);
            }
            else {
                throw new \Exception('Invalid form entries found!');
            }
        }
        else
        {
            $client = new Client();
            $client->exchangeArray($post);
            $table->saveClient($client);            
        }
        
        $session = new Container('models');
        if(isset($session->reservationModel))
        {
            $lastId = $table->getLastId();
            $session->reservationModel['client_id'] = $lastId;
            return $this->redirect()->toRoute('pms/reservation', [
                'action' => 'edit',
            ]); 
        }
                
        $this->redirect()->toRoute('pms/client');
    }
    
    /**
     * Deletes the user with the given id.
     */
    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id');
        $table = $this->getServiceLocator()->get('ClientTable');
        if(isset($id))
        {
            $table->deleteClient($id);
        }
        
        $this->redirect()->toRoute('pms/client');
    }
    
    /**
     * Action that prepares the basic client data preview.
     * @return ViewModel
     */
    public function previewAction()
    {
        $id = $this->params()->fromRoute('id');
        $table = $this->getServiceLocator()->get('ClientTable');
        $client = $table->getClient($id);
        return new ViewModel([
            'client' => $client,
        ]);
    }
    
}
