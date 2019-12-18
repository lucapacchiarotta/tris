<?php
namespace Application\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
//use Zend\Session\SessionManager;
use Application\Controller\IndexController;

class IndexControllerFactory implements FactoryInterface {
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        //$sessionManager = $container->get(SessionManager::class);
        // Instantiate the controller and inject dependencies
        return new IndexController($entityManager);
    }
}
