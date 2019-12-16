<?php
namespace ApiTest\Controller;

use Api\Controller\IndexController;
use Zend\Stdlib\ArrayUtils;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class IndexControllerTest extends AbstractHttpControllerTestCase {
    
    protected $traceError = true;
    
    public function setUp(): void {
        $configOverrides = [];
        $this->setApplicationConfig(ArrayUtils::merge(
            include __DIR__ . '/../../../../config/application.config.php',
            $configOverrides
        ));

        parent::setUp();
    }
    
    public function testIndexActionCanBeAccessed() {
        $this->dispatch('/api/v1/getlist', 'POST');
        $this->assertResponseStatusCode(200);
    }

    public function testInvalidRouteDoesNotCrash() {
        $this->dispatch('/invalid/route', 'GET');
        $this->assertResponseStatusCode(404);
    }
}
