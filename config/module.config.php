<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Pms\Controller\Index' => 'Pms\Controller\IndexController',
            'Pms\Controller\Login' => 'Pms\Controller\LoginController',
            'Pms\Controller\Register' => 'Pms\Controller\RegisterController',
            'Pms\Controller\EntityType' => 'Pms\Controller\EntityTypeController',
            'Pms\Controller\UserManager' => 'Pms\Controller\UserManagerController',
            'Pms\Controller\AttributeManager' => 'Pms\Controller\AttributeManagerController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'pms' => array(
                'type'    => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route'    => '/pms',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'Pms\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    // This route is a sane default when developing a module;
                    // as you solidify the routes for your module, however,
                    // you may want to remove it and replace it with more
                    // specific routes.
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                    'entity-type' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/entity-type[/:action[/:id]]',
                            'constraints' => array(
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Pms\Controller\EntityType',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'user-manager' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/user-manager[/:action[/:id]]',
                            'constraints' => array(
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Pms\Controller\UserManager',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'attribute-manager' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/attribute-manager[/:action[/:id]]',
                            'constraints' => array(
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Pms\Controller\AttributeManager',
                                'action' => 'index',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Pms' => __DIR__ . '/../view',
        ),
    ),
);
