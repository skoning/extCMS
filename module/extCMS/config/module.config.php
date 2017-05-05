<?php
namespace extCMS;

use extCMS\Entity\Security\User;
use Zend\Config\Config;

$config = array(
  'router' => array(
    'routes' => array(
      'page' => array(
        'type' => 'Regex',
        'options' => array(
          'regex' => '/(?<alias>(.*?))',
          'defaults' => array(
            'controller' => 'extCMS\Controller\List',
            'action' => 'index'
          ),
          'spec' => '/%alias%'
        ),
      ),
      'manager' => array(
        'type' => 'literal',
        'may_terminate' => false,
        'options' => array(
          'route' => '/manager',
          'defaults' => array(
            'controller' => 'manager',
            'action' => 'index'
          ),
        ),
        'child_routes' => array(
          'login' => array(
            'type' => 'literal',
            'may_terminate' => true,
            'options' => array(
              'route' => '/login',
              'defaults' => array(
                'controller' => 'manager',
                'action' => 'login'
              )
            )
          ),
          'page-add' => array(
            'type' => 'literal',
            'may_terminate' => true,
            'options' => array(
              'route' => '/add',
              'defaults' => array(
                'controller' => 'page',
                'action' => 'add',
              )
            )
          ),
          'page-edit' => array(
            'type' => 'segment',
            'may_terminate' => true,
            'options' => array(
              'route' => '/edit/:id',
              'constrants' => array(
                'id' => '[0-9]+'
              ),
              'defaults' => array(
                'controller' => 'page',
                'action' => 'edit'
              )
            )
          ),
          'manager-config' => array(
            'type' => 'literal',
            'may_terminate' => true,
            'options' => array(
              'route' => '/config',
              'defaults' => array(
                'controller' => 'manager',
                'action' => 'set'
              )
            )
          ),
          'manager-extension' => array(
            'type' => 'literal',
            'may_terminate' => true,
            'options' => array(
              'route' => '/extension',
              'defaults' => array(
                'controller' => 'page',
                'action' => 'extension'
              )
            )
          ),
            
        )
      )
    )
  ),
  'controllers' => array(
    'factories' => array(
      'extCMS\Controller\List' => 'extCMS\Controller\ListController',
      
      'manager' => 'extCMS\Manager\ManagerController',
      'page' => 'extCMS\Manager\PageController'
    )
  ),
  'view_manager' => array(
    'template_path_stack' => array(
      __DIR__ . '/../view',
    )
  ),
  'doctrine' => array(
    'authentication' => array(
      'orm_default' => array(
        'object_manager' => 'Doctrine\ORM\EntityManager',
        'identity_class' => 'extCMS\Entity\Security\User',
        'identity_property' => 'username',
        'credential_property' => 'password',
        'credential_callable' => function( User $user, $passwordGiven ) {
          return $user->validatePassword($passwordGiven);
        }
      ),
    ),
    'authenticationadapter' => array(
      'orm_default' => 'extCMS\Manager\AuthenticationAdapterFactory',
    ),
    'driver' => array(
       __NAMESPACE__ . '_driver' => array(
        'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
        'cache' => 'array',
        'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity')
      ),
      'orm_default' => array(
        'drivers' => array(
          __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
        )
      )
    )
  ),
  'doctrine_factories' => array(
    'authenticationadapter' => 'extCMS\Factories\AuthenticationAdapterFactory',
  ),
  'service_manager' => array(
    'factories' => array(
      'extCMSManager' => 'extCMS\Factories\extCMSManager',
      'extCMS' => 'extCMS\Factories\extCMS',
      'GA' => 'extCMS\Factories\GA',
      'extCMSConfig' => function($sl) {
        $em = $sl->get('doctrine.entitymanager.orm_default');
        $config = array();
        
        $entries = $em->getRepository('extCMS\Entity\Config')->findAll();
        
        foreach ($entries as $entry) {
          $config[$entry->getKey()] = $entry->getValue();
        }
        
        return new Config($config, true);
      }
    )
  ),
  'translator' => array(
    'locale' => 'nl_NL',
    'translation_file_patterns' => array(
      array(
        'type'     => 'gettext',
        'base_dir' => __DIR__ . '/../language',
        'pattern'  => '%s.mo',
      ),
    ),
  ),
  'zfctwig' => array(
    'extensions' => array()
  )
);
/*
// Get twig extensions
$files = glob('data/twigExtensions/*.php');
foreach ($files as $file) {
  $file = basename($file, '.php');
  $config['view_helpers']['invokables'][$file] = 'extCMS\twigExtensions\\' . $file;
}
*/
return $config;
