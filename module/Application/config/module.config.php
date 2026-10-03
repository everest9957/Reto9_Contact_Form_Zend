<?php

declare(strict_types=1);

namespace Application;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'application' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/application[/:action]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'contact' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/contact',
                    'defaults' => [
                        'controller' => Controller\ContactController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'contact-process' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/contact/process',
                    'defaults' => [
                        'controller' => Controller\ContactController::class,
                        'action'     => 'process',
                    ],
                ],
            ],
        ],
    ],

    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,

            Controller\ContactController::class => function ($container) {
                $form  = $container->get(\Application\Form\ContactForm::class);
                $table = $container->get(\Application\Model\ContactTable::class);

                return new Controller\ContactController($form, $table);
            },
        ],
    ],

    'service_manager' => [
        'factories' => [
            Model\Contact::class    => InvokableFactory::class,
            Form\ContactForm::class => InvokableFactory::class,

            Model\ContactTable::class => function ($container) {
                /** @var AdapterInterface $dbAdapter */
                $dbAdapter = $container->get(AdapterInterface::class);

                $resultSetPrototype = new ResultSet();
                $resultSetPrototype->setArrayObjectPrototype(new Model\Contact());

                $tableGateway = new TableGateway(
                    'contacts',
                    $dbAdapter,
                    null,
                    $resultSetPrototype
                );

                return new Model\ContactTable($tableGateway);
            },
        ],
    ],

    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'application/contact/index' => __DIR__ . '/../view/application/contact/index.phtml',
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];