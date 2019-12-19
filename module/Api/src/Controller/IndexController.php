<?php

namespace Api\Controller;

use Zend\View\Model\JsonModel;
use Api\Entity\Wishlist;
use Api\Entity\Games;

class IndexController extends AbstractApiController {
    
    // Entity Manager
    protected $em;
    
    public function __construct($em) {
        $this->em = $em;
    }
    
    /**
     * Creazione di una nuova lista dei desideri
     * @return \Zend\View\Model\JsonModel
     */
    public function creategameAction() {
        $this->_objResult['status'] = self::RISP_KO;
        
        $request = $this->getRequest();
        $content = $request->getContent();
        
        if ($content) {
            $content = json_decode($content);
        }
        
        if ($content) {
            $size = $content->size;         // Grandezza della griglia di gioco (3X3, 5X5, 10X10)
            $players = $content->players;   // Numero di giocatori
            $symbols = $content->symbols;   // Simboli dei giocatori
            
            // Validazione dati in input
            $check = true;
            if (empty($size)) {
                $this->_objResult['message'] = 'Invalid size value';
                $check = false;
            }
            if (empty($players)) {
                $this->_objResult['message'] = 'Invalid number of players value';
                $check = false;
            }
            if (empty($symbols)) {
                $this->_objResult['message'] = 'Invalid symbols of players';
                $check = false;
            }
            if ($players != count($symbols)) {
                $this->_objResult['message'] = 'Number of symbols and number of players is not the same. ' . $players . ' ' . count($symbols);
                $check = false;
            }
            
            if (!$check) {
                $this->_objResult['status'] = self::RISP_KO;
            }
            
            if ($check) {
                $fields = [];
                for ($i = 0; $i < $size; $i++) {
                    for ($j = 0; $j < $size; $j++) {
                        $fields[$i][$j] = ' ';                        
                    }
                }
                $arrPlayers = [];
                for ($i = 0; $i < $players; $i++) {
                    $arrPlayers[$i] = $symbols[$i];
                }
                
                $game = new Games();
                $game->setId(md5(time()));
                $game->setSize($size);
                $game->setPlayers(json_encode($arrPlayers));
                $game->setFields(json_encode($fields));
                $game->setCurrentPlayer(0);
                $game->setWinner(null);
                
                try {
                    $this->em->persist($game);
                    $this->em->flush();                    
                } catch (\Exception $e) {
                    $this->_objResult['error'] = $e->getMessage();
                }
                
                $this->_objResult['status'] = self::RISP_OK;
                $this->_objResult['message'] = 'Game created!';
                $this->_objResult['game_id'] = $game->getId();
            }
        }
        
        return new JsonModel($this->_objResult);
    }
    
    public function doselectionAction() {
        $request = $this->getRequest();
        $content = $request->getContent();
        
        if ($content) {
            $content = json_decode($content);
        }
        
        if ($content) {
            $idGame = $content->idGame;
            $player = $content->player;
            $coord = $content->coord;
        }
        
        $check = true;
        
        if (empty($idGame) || is_null($player) || empty($coord)) {
            $this->_objResult['status'] = self::RISP_KO;
            $this->_objResult['message'] = 'Missing mandatory data';
            $check = false;
        }
        
        $this->_objResult['status'] = self::RISP_OK;
        
        if ($check) {
            $item = $this->em
                ->getRepository(Games::class)
                ->findOneBy(['id' => $idGame]);
            
            if (!$item) {
                $this->_objResult['status'] = self::RISP_KO;
                $this->_objResult['message'] = 'Game not found';
                return new JsonModel($this->_objResult);
            }
            
            $fields = json_decode($item->getFields());
            list($x, $y) = explode('|', $coord);
            
            $size = $item->getSize();
            
            if ($x > $size - 1 || $y > $size - 1 ) {
                $this->_objResult['status'] = self::RISP_KO;
                $this->_objResult['message'] = 'Invalid coordinates';
                return new JsonModel($this->_objResult);
            }
            
            $elem = trim($fields[$x][$y]);
            if (!empty($elem)) {
                $this->_objResult['status'] = self::RISP_KO;
                $this->_objResult['message'] = 'Wrong selection! Field already selected.';
                return new JsonModel($this->_objResult);
            }
            
            $players = json_decode($item->getPlayers());
            $fields[$x][$y] = $players[$player]; // Player's symbol
            
            $this->_objResult['players'] = $players;
            $this->_objResult['symbol'] = $players[$player];
            $this->_objResult['fields'] = $fields;
            $this->_objResult['message'] = "Selection ($x, $y) made!";
            
            // Check if is the winner
            $result = $this->_checkGridWinner($fields);
            $won = $result['won'];
            $gameFinished = $result['gameFinished'];
            
            $this->_objResult['won'] = $won;
            $this->_objResult['gameFinished'] = $gameFinished;
            $this->_objResult['checks'] = $result['checks'];
            
            $item->setFields(json_encode($fields));
            
            if ($won) {
                $item->setWinner($player);
            } else {
                if ($gameFinished) {
                    $item->setWinner(-1);
                } else {
                    if ($player < count($players) - 1) {
                        $player++;
                    } else {
                        $player = 0;
                    }
                    $item->setCurrentplayer($player);
                }
            }
            
            // Save data to DB
            $this->em->flush();
        }
        
        return new JsonModel($this->_objResult);
    }
    
    public function gamelistAction() {
        $item = $this->em
            ->getRepository(Games::class)
            ->findAll();
        
        foreach ($item as $k) {
            echo "ID: {$k->getId()} <br />Size: {$k->getSize()} <br />Players: {$k->getPlayers()} Current player: {$k->getCurrentplayer()} <br />" . 
                 "Fileds: {$k->getFields()} <br /> Winner: {$k->getWinner()} <br /><br />";
        }
        
        /*$this->_objResult['status'] = self::RISP_OK;
        $this->_objResult['message'] = count($item);
        $this->_objResult['data'] = print_r($item, true);*/
        
        return new JsonModel([
            $this->_objResult
        ]);
    }
    
    private function _checkGridWinner($grid) {
        $size = count($grid);
        
        $won = false;
        $gameFinished = true;
        
        $resHorizontal = [];
        $resVertical = [];
        $resDiagonal = [];
        
        for ($i = 0; $i < $size; $i++) {
            $resHorizontal[$i] = true;
            $resVertical[$i] = true;
            $resDiagonal[$i] = true;
        }
        
        $checks = [];
        
        for ($i = 0; $i < $size; $i++) {
            
            for ($j = $i; $j < $size; $j++) {
                
                $currElem = trim($grid[$i][$j]);
                
                $checks[] = "Elemento corrente ($i, $j) : $currElem";
                
                if (empty($currElem)) {
                    $gameFinished = false;
                    $resHorizontal[$i] = false;
                    $resVertical[$i] = false;
                    $resDiagonal[$i] = false;
                }
                /*
                if ($i < $size - 1) {
                    $resHorizontal[$i] = $resHorizontal[$i] && !empty($currElem) && ($grid[$i][$j] == $grid[$i + 1][$j]);
                    $checks[] = "Orizz: {$grid[$i][$j]} == {$grid[$i + 1][$j]}";
                }
                
                if ($j < $size - 1) {
                    $resVertical[$i] = $resVertical[$i] && !empty($currElem) && ($grid[$i][$j] == $grid[$i][$j + 1]);                    
                    $checks[] = "Vert: {$grid[$i][$j]} == {$grid[$i][$j + 1]}";                    
                }
                
                
                if ($i < $size - 1 && $i == $j) {
                    $resDiagonal[$i] = $resHorizontal[$i] && !empty($currElem) && ($grid[$i][$j] == $grid[$i + 1][$j + 1]);
                    $checks[] = "Diag: {$grid[$i][$j]} == {$grid[$i + 1][$j + 1]}";
                }
                */
                if ($resHorizontal[$i] === true) {
                    if ($i < $size - 1) {
                        if ($currElem !== $grid[$i + 1][$j]) {
                            $resHorizontal[$i] = false;
                        }
                    }
                }
                
                if ($resVertical[$i] === true) {
                    if ($j < $size - 1) {
                        if ($currElem !== $grid[$i][$j + 1]) {
                            $resVertical[$i] = false;
                        }
                    }
                }
                
                if ($resDiagonal[$i] === true) {
                    if ($i == $j) {
                        if ($i < $size - 1) {
                            if ($currElem !== $grid[$i + 1][$j + 1]) {
                                $resDiagonal[$i] = false;
                            }
                        }
                    }
                }
                
            }
            
        }
        /*
        var_dump($resHorizontal);
        var_dump($resVertical);
        var_dump($resDiagonal);
        */
        
        for ($i = 0; $i < $size; $i++) {
            if ($resHorizontal[$i] === true || $resVertical[$i] === true /*|| $resDiagonal[$i] === true*/) {
                $won = true;
                break;
            }
        }
        
       return [
           'won' => $won, 
           'gameFinished' => $gameFinished,
           'checks' => $checks
       ];
    }
    
    
}
