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
        $table = $this->getServiceLocator()->get('ClientTable');
        $clients = $table->fetchAll()->toArray();
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
        $id = $this->params()->fromRoute('id');
        $form = $this->getServiceLocator()->get('ClientForm');
        if(isset($id))
        {
            // edit
            $clientTable = $this->getServiceLocator()->get('ClientTable');
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
}
