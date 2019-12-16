<?php

namespace Api;

class Module {
    const VERSION = '1.0dev';

    public function getConfig() {
        return include __DIR__ . '/../config/module.config.php';
    }
}
