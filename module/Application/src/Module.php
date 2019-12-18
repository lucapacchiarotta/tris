<?php

namespace Application;

use Zend\Mvc\MvcEvent;
use Zend\Session\SessionManager;
use Zend\ModuleManager\ModuleManager;

class Module {
    const VERSION = '2.0';

    public function getConfig() {
        return include __DIR__ . '/../config/module.config.php';
    }
}
