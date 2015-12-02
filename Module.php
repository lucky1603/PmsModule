<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Pms;

use Pms\Model\Attribute;
use Pms\Model\Entity;
use Pms\Model\EntityDefinition;
use Pms\Model\EntityType;
use Pms\Model\User;
use Pms\Model\Client;
use Pms\Model\AttributeTable;
use Pms\Model\BusinessModel;
use Pms\Model\BusinessForm;
use Pms\Model\EntityTable;
use Pms\Model\EntityDefinitionTable;
use Pms\Model\EntityTypeTable;
use Pms\Model\EntityTypeModel;
use Pms\Model\UserTable;
use Pms\Model\ClientTable;
use Pms\Model\AttributeValueModel;
use Pms\Model\AttributeModel;
use Pms\Model\EntityDefinitionModel;
use Pms\Model\EntityModel;
use Pms\Model\ReservationModel;
use Pms\Form\LoginForm;
use Pms\Form\RegisterForm;
use Pms\Form\EntityForm;
use Pms\Form\LoginFilter;
use Pms\Form\RegisterFilter;
use Pms\Form\AttributeForm;
use Pms\Form\EntityDefinitionForm;
use Pms\Form\ClientForm;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;

class Module implements AutoloaderProviderInterface
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
		    // if we're in a namespace deeper than one level we need to fix the \ in the path
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/' , __NAMESPACE__),
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e)
    {
        // You may not need to do this if you're doing it elsewhere in your
        // application
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }
    
    public function getServiceConfig()
    {
         return [
            'abstract_factories' => [],
            'aliases' => [],
            'factories' => [
                // Tables
                "UserTable" => function($sm) {
                    $tableGateway = $sm->get('UserTableGateway');
                    $table = new UserTable($tableGateway);
                    return $table;
                },
                'EntityTypeTable' => function($sm) {
                    $tableGateway = $sm->get('EntityTypeTableGateway');
                    $table = new EntityTypeTable($tableGateway);
                    return $table;
                },
                'AttributeTable' => function($sm) {
                    $tableGateway = $sm->get("AttributeTableGateway");
                    $table = new AttributeTable($tableGateway);
                    return $table;
                },
                'EntityDefinitionTable' => function($sm) {
                    $tableGateway = $sm->get("EntityDefinitionTableGateway");
                    $table = new EntityDefinitionTable($tableGateway);
                    return $table; 
                },
                'EntityTable' => function($sm) {
                    $tableGateway = $sm->get('EntityTableGateway');
                    $table = new EntityTable($tableGateway);
                    return $table;
                },
                'ClientTable' => function($sm) {
                    $tableGateway = $sm->get("ClientTableGateway");
                    $table = new ClientTable($tableGateway);
                    return $table;
                },
                //Gateways
                'UserTableGateway' => function($sm) {
                    $dbAdapter = $sm->get('\Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new User());
                    return new TableGateway('user', $dbAdapter, null, $resultSetPrototype);
                },
                'EntityTypeTableGateway' => function($sm) {
                    $dbAdapter = $sm->get('\Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new EntityType());
                    return new TableGateway('entity_type', $dbAdapter, null, $resultSetPrototype);
                }, 
                'AttributeTableGateway' => function($sm) {
                    $dbAdapter = $sm->get('\Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Attribute());
                    return new TableGateway('attribute', $dbAdapter, null, $resultSetPrototype);
                },
                'EntityDefinitionTableGateway' => function($sm) {
                    $dbAdapter = $sm->get('\Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new EntityDefinition());
                    return new TableGateway('entity_definition', $dbAdapter, null, $resultSetPrototype);
                },  
                'EntityTableGateway' => function($sm) {
                    $dbAdapter = $sm->get("\Zend\Db\Adapter\Adapter");
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Entity());
                    return new TableGateway('entity', $dbAdapter, null, $resultSetPrototype);
                },
                'ClientTableGateway' => function($sm) {
                    $dbAdapter = $sm->get('\Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Client());
                    return new TableGateway('clients', $dbAdapter, null, $resultSetPrototype);
                },
                // Adapters
                'Adapter' => function($sm) {
                    $dbAdapter = $sm->get('\Zend\Db\Adapter\Adapter');
                    return $dbAdapter;
                },
                //Forms
                'LoginForm' => function($sm) {
                    $loginForm = new LoginForm();
                    $loginForm->setInputFilter($sm->get('LoginFilter'));
                    return $loginForm;
                },
                'RegisterForm' => function($sm) {
                    $dbAdapter = $sm->get("Adapter");
                    $registerForm = new RegisterForm('', ['adapter' => $dbAdapter]);
                    $registerForm->setInputFilter($sm->get('RegisterFilter'));
                    return $registerForm;
                }, 
                'EntityTypeForm' => function($sm) {
                    $form = new \Pms\Form\EntityTypeForm();
                    return $form;
                },
                'AttributeForm' => function($sm) {
                    $form = new AttributeForm();
                    return $form;
                },
                'BusinessForm' => function($sm) {
                    $form = new BusinessForm();
                    return $form;
                },
                'EntityDefinitionForm' => function($sm) {
                    $dbAdapter = $sm->get("Adapter");
                    $form = new EntityDefinitionForm('', ['adapter' => $dbAdapter]);
                    return $form;
                },
                'EntityForm' => function($sm) {
                    $dbAdapter = $sm->get("Adapter");
                    $form = new EntityForm('', ['adapter' => $dbAdapter]);
                    return $form;
                },
                'ClientForm' => function($sm) {
                    $dbAdapter = $sm->get("Adapter");
                    $form = new ClientForm('', ['adapter' => $dbAdapter]);
                    return $form;
                },
                'ReservationForm' => function($sm) {
                    $dbAdapter = $sm->get("Adapter");
                    $form = new \Pms\Form\ReservationForm('', ['adapter' => $dbAdapter]);
                    return $form;
                },
                'ReservationEntityForm' => function($sm) {
                    $dbAdapter = $sm->get('Adapter');
                    $form = new \Pms\Form\ReservationEntityForm('ReservationEntity', ['adapter' => $dbAdapter]);
                    return $form;
                },
                // Filters
                'LoginFilter' => function($sm) {
                    $loginFilter = new LoginFilter();
                    return $loginFilter;
                },
                'RegisterFilter' => function($sm) {
                    $registerFilter = new RegisterFilter();
                    return $registerFilter;
                },
                // Complex models
                'AttributeValueModel' => function($sm) {
                    $dbAdapter = $sm->get('Adapter');
                    $attModel = new AttributeValueModel($dbAdapter);
                    return $attModel;
                },
                'AttributeModel' => function($sm) {
                    $dbAdapter = $sm->get('Adapter');
                    $attModel = new AttributeModel($dbAdapter);
                    return $attModel;
                },                        
                'EntityDefinitionModel' => function($sm) {
                    $dbAdapter = $sm->get('Adapter');
                    $edModel = new EntityDefinitionModel($dbAdapter);
                    return $edModel;
                },
                'EntityModel' => function($sm) {
                    $dbAdapter = $sm->get('Adapter');
                    $eModel = new EntityModel($dbAdapter);
                    return $eModel;
                },        
                'EntityTypeModel' => function($sm) {
                    $dbAdapter = $sm->get('Adapter');
                    $eModel = new EntityTypeModel($dbAdapter);
                    return $eModel;
                },                                
                'ReservationModel' => function($sm) {
                    $dbAdapter = $sm->get('Adapter');
                    $eModel = new ReservationModel($dbAdapter);
                    return $eModel;
                },
                'BusinessModel' => function($sm) {
                    $dbAdapter = $sm->get('Adapter');
                    $model = new BusinessModel($dbAdapter);
                    return $model;
                },
                // Authentication Services
                'AuthenticationService' => function($sm) {
                    $dbAdapter = $sm->get('\Zend\Db\Adapter\Adapter');
                    $dbTableAuthAdapter = new DbTableAuthAdapter($dbAdapter, 'user', 'email', 'password', 'md5(?)');                    
                    $authservice = new AuthenticationService();
                    $authservice->setAdapter($dbTableAuthAdapter);
                    return $authservice;
                },
            ],
            'invokables' => [],
            'services' => [],
            'shared' => [],  
        ];
    }
}
