<?php

namespace Application\Controller;

use Zend\Mvc\MvcEvent;
use Doctrine\ORM\Query;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Entity\SystemStatus;
use Zend\Http\Request;
use Zend\Http\Client;
use Exception;
use Domo\Core\DomoRequest;
use Domo\Sensors\Temperature;
use Domo\Sensors\Voltage;
use Api\Entity\Games;

class IndexController extends AbstractActionController {
    
    protected $entityManager;
    
    public function __construct($entityManager) {
        $this->entityManager = $entityManager;
    }
    
    
    public function indexAction() {
        $vm = new ViewModel();
        
        return $vm;
    }
    
    public function gameAction() {
        $vm = new ViewModel();
        
        $idGame = $this->params()->fromRoute('idgame');
        
        $game = $this->entityManager
            ->getRepository(Games::class)
            ->findOneBy(['id' => $idGame]);
        
        if (!$game) {
            //echo "Game not found!";
        }
        
        $vm->idGame = $idGame;
        $vm->size = $game->getSize();
        $vm->players = $game->getPlayers();
        $vm->current_player = $game->getCurrentPlayer();
        $vm->fields = $game->getFields();
        $vm->winner = $game->getWinner();
        
        return $vm;
    }
}
