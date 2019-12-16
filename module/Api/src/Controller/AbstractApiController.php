<?php

namespace Api\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\Mvc\MvcEvent;

abstract class AbstractApiController extends AbstractRestfulController {

    CONST RISP_OK = 'OK';
    CONST RISP_KO = 'KO';

    protected $_objResult;

    public function __construct() {
        $this->_objResult = array();
        $this->_objResult['status'] = '';
        $this->_objResult['message'] = '';
        $this->_objResult['extra'] = '';
    }

    /*
    public function onDispatch(MvcEvent $e) {
        parent::onDispatch($e);
        
        $req = $e->getRequest();
        //var_dump($req, 'REQ');
        
    }
    */

    
}
