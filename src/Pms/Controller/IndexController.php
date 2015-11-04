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
//        $conn = pg_connect('dbname=hotel host=192.168.0.14 user=hotel password=BiloKoji12');
        
//        $result = pg_prepare($conn, "myquery1", 'select * from user');
//        $result = pg_exec($conn, 'select * from role');
//        $result = pg_query($conn, "select * from user");
//        var_dump(pg_fetch_all($result));
//        var_dump(pg_fetch_all($result));
//        die();
        
//        $adapter = $this->getServiceLocator()->get('Adapter');
//        $qi = function($name) use ($adapter) { return $adapter->platform->quoteIdentifier($name); };
//        $fp = function($name) use ($adapter) { return $adapter->driver->formatParameterName($name); };
//        $statement = $adapter->query('SELECT * FROM '.$qi('user'));
//        $results = $statement->execute();
//        var_dump($results->current());
//        die();
        
//        $userTable = $this->getServiceLocator()->get("UserTable");
//        $users = $userTable->fetchAll();
//        return new ViewModel([
//            'users' => $users,
//        ]);
     
//        \Zend\Debug\Debug::dump("And not the others...");
//   
//        $table = $this->getServiceLocator()->get("UserTable");
//        $results1 = $table->fetchAll();
//        \Zend\Debug\Debug::dump("Ima ih ... " . $results1->count());
//        \Zend\Debug\Debug::dump($results1->toArray());     
        
//        $userTable = $this->getServiceLocator()->get('UserTable');
//        $rows = $userTable->fetchView();
//        \Zend\Debug\Debug::dump($rows);
        
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
}

