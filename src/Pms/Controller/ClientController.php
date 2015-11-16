<?php
/**
 * @name ClientController - Controller class for manipulation of clients.
 * @author Dragutin Jovanovic <gutindra@gmail.com>
 * @date 16.11.2015.
 */
namespace Pms\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

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
    
    public function processAction()
    {
        
    }
}
