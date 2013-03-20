<?php
return array(
		'controllers' => array(
				'invokables' => array(
						'Acl\Controller\Acl' => 'Acl\Controller\AclController',
						),
				),
		'controller_plugins' => array(
				'invokables' => array(
						'AclPlugin' => 'Acl\Controller\Plugin\AclPlugin',
						),
				),
		'router' => array(
				'routes' => array(
						'acl' => array(
								'type' => 'segment',
								'options' => array(
										'route' => '/acl[/:action]',
										'constraints' => array(
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
										),
										'defaults' => array(
												'controller' => 'Acl\Controller\Acl',
												'action' => 'error',
										),
								),
						),
				),
		),
		'view_manager' => array(
				'template_path_stack' => array(
						'acl' => __DIR__ . '/../view',
				),
		),
);