<?php
namespace Api\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * Games
 *
 * @ORM\Table(name="games")
 * @ORM\Entity
 */
class Games {
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", nullable=false)
     * @ORM\Id
     */
    private $id;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="size", type="integer", nullable=false)
     */
    private $size;
    
    /**
     * @var string
     *
     * @ORM\Column(name="players", type="text", nullable=false)
     */
    private $players;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="current_player", type="integer", nullable=false)
     */
    private $currentPlayer;
    
    /**
     * @var string
     *
     * @ORM\Column(name="fields", type="text", nullable=false)
     */
    private $fields;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="winner", type="integer", nullable=true)
     */
    private $winner;
    
    /**
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return number
     */
    public function getSize() {
        return $this->size;
    }

    /**
     * @return string
     */
    public function getPlayers() {
        return $this->players;
    }

    /**
     * @return number
     */
    public function getCurrentPlayer() {
        return $this->currentPlayer;
    }

    /**
     * @return string
     */
    public function getFields() {
        return $this->fields;
    }

    /**
     * @return number
     */
    public function getWinner() {
        return $this->winner;
    }

    /**
     * @param string $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @param number $size
     */
    public function setSize($size) {
        $this->size = $size;
    }

    /**
     * @param string $players
     */
    public function setPlayers($players) {
        $this->players = $players;
    }

    /**
     * @param number $currentPlayer
     */
    public function setCurrentPlayer($currentPlayer) {
        $this->currentPlayer = $currentPlayer;
    }

    /**
     * @param string $fields
     */
    public function setFields($fields) {
        $this->fields = $fields;
    }

    /**
     * @param number $winner
     */
    public function setWinner($winner) {
        $this->winner = $winner;
    }
    
}