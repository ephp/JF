<?php

namespace JF\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Licenza
 *
 * @ORM\Table(name="jf_licenze")
 * @ORM\Entity(repositoryClass="JF\CoreBundle\Entity\LicenzaRepository")
 */
class Licenza {

    /**
     * Novità
     * 
     * @var integer
     */
    public static $S_NEW = 2;

    /**
     * Aggiornamento
     * 
     * @var integer
     */
    public static $S_UPD = 5;

    /**
     * Eliminazione
     * 
     * @var integer
     */
    public static $S_DEL = 6;

    /**
     * Sconto
     * 
     * @var integer
     */
    public static $S_SAL = 3;

    /**
     * Ribasso del prezzo
     * 
     * @var integer
     */
    public static $S_DOW = 4;

    /**
     * Offerta laancio
     * 
     * @var integer
     */
    public static $S_LIM = 1;

    /**
     * Attivabile solo da gestione
     * 
     * @var integer
     */
    public static $S_HID = -1;

    /**
     * Nessuna variazione
     * 
     * @var integer
     */
    public static $S_NOP = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Gruppo")
     * @ORM\JoinColumn(name="gruppo_id", referencedColumnName="id", nullable=true)
     */
    private $gruppo;

    /**
     * @var string
     *
     * @ORM\Column(name="sigla", type="string", length=16)
     */
    private $sigla;

    /**
     * @var string
     *
     * @ORM\Column(name="nome", type="string", length=64)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="descrizione", type="text", nullable=true)
     */
    private $descrizione;

    /**
     * @var array
     *
     * @ORM\Column(name="roles", type="array")
     */
    private $roles;

    /**
     * @var array
     *
     * @ORM\Column(name="widgets", type="array", nullable=true)
     */
    private $widgets;

    /**
     * @var array
     *
     * @ORM\Column(name="params", type="array", nullable=true)
     */
    private $params;

    /**
     * @var integer
     *
     * @ORM\Column(name="prezzo", type="decimal", precision=6, scale=2)
     */
    private $prezzo;

    /**
     * @var integer
     *
     * @ORM\Column(name="durata", type="integer", nullable=true)
     */
    private $durata;

    /**
     * @var boolean
     *
     * @ORM\Column(name="autoinstall", type="boolean")
     */
    private $autoinstall;

    /**
     * @var boolean
     *
     * @ORM\Column(name="market", type="boolean")
     */
    private $market;

    /**
     * @var decimal
     *
     * @ORM\Column(name="sconto", type="decimal", precision=6, scale=2)
     */
    private $sconto;

    /**
     * @var integer
     *
     * @ORM\Column(name="stato", type="integer")
     */
    private $stato;

    /**
     * @var integer
     *
     * @ORM\Column(name="ordine", type="integer")
     */
    private $ordine;

    function __construct() {
        $this->roles = array();
        $this->widgets = array();
        $this->params = array();
        $this->market = true;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set sigla
     *
     * @param string $sigla
     * @return Licenza
     */
    public function setSigla($sigla) {
        $this->sigla = $sigla;

        return $this;
    }

    /**
     * Get sigla
     *
     * @return string 
     */
    public function getSigla() {
        return $this->sigla;
    }

    /**
     * Set nome
     *
     * @param string $nome
     * @return Licenza
     */
    public function setNome($nome) {
        $this->nome = $nome;

        return $this;
    }

    /**
     * Get nome
     *
     * @return string 
     */
    public function getNome() {
        return $this->nome;
    }

    /**
     * Set descrizione
     *
     * @param string $descrizione
     * @return Licenza
     */
    public function setDescrizione($descrizione) {
        $this->descrizione = $descrizione;

        return $this;
    }

    /**
     * Get descrizione
     *
     * @return string 
     */
    public function getDescrizione() {
        return $this->descrizione;
    }

    /**
     * Set roles
     *
     * @param array $roles
     * @return Licenza
     */
    public function setRoles($roles) {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Verifica i roles
     *
     * @param array $roles
     * @return Licenza
     */
    public function hasRole($role) {
        foreach ($this->getRoles() as $_role) {
            if (strtolower($_role) == strtolower($role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get roles
     *
     * @return array 
     */
    public function getRoles() {
        return $this->roles;
    }

    /**
     * Set widgets
     *
     * @param array $widgets
     * @return Licenza
     */
    public function setWidgets($widgets) {
        $this->widgets = $widgets;

        return $this;
    }

    /**
     * Verifica i roles
     *
     * @param array $widget
     * @return Licenza
     */
    public function hasWidget($widget) {
        foreach ($this->getWidgets() as $_widget) {
            if (strtolower($_widget) == strtolower($widget)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get widgets
     *
     * @return array 
     */
    public function getWidgets() {
        return $this->widgets;
    }

    /**
     * Set durata
     *
     * @param integer $durata
     * @return Licenza
     */
    public function setDurata($durata) {
        $this->durata = $durata;

        return $this;
    }

    /**
     * Get durata
     *
     * @return integer 
     */
    public function getDurata() {
        return $this->durata;
    }

    /**
     * Set route
     *
     * @param string $route
     * @return Licenza
     */
    public function setRoute($route) {
        $this->route = $route;

        return $this;
    }

    /**
     * Get route
     *
     * @return string 
     */
    public function getRoute() {
        return $this->route;
    }

    /**
     * Set params
     *
     * @param array $params
     * @return Licenza
     */
    public function setParams($params) {
        $this->params = $params;

        return $this;
    }

    /**
     * Get params
     *
     * @return array 
     */
    public function getParams() {
        return $this->params;
    }

    /**
     * Get params
     *
     * @return array 
     */
    public function getParam($name, $default = null) {
        return isset($this->params[$name]) ? $this->params[$name] : $default;
    }

    /**
     * Set prezzo
     *
     * @param float $prezzo
     * @return Licenza
     */
    public function setPrezzo($prezzo) {
        $this->prezzo = $prezzo;

        return $this;
    }

    /**
     * Get prezzo
     *
     * @return float 
     */
    public function getPrezzo() {
        return $this->prezzo;
    }

    /**
     * Set market
     *
     * @param boolean $market
     * @return Licenza
     */
    public function setMarket($market) {
        $this->market = $market;

        return $this;
    }

    /**
     * Get market
     *
     * @return boolean 
     */
    public function getMarket() {
        return $this->market;
    }

    /**
     * Set gruppo
     *
     * @param Gruppo $gruppo
     * @return Licenza
     */
    public function setGruppo(\JF\CoreBundle\Entity\Gruppo $gruppo) {
        $this->gruppo = $gruppo;

        return $this;
    }

    /**
     * Get gruppo
     *
     * @return Gruppo 
     */
    public function getGruppo() {
        return $this->gruppo;
    }

    /**
     * Set sconto
     *
     * @param boolean $sconto
     * @return Licenza
     */
    public function setSconto($sconto) {
        $this->sconto = $sconto;

        return $this;
    }

    /**
     * Get sconto
     *
     * @return boolean 
     */
    public function getSconto() {
        return $this->sconto;
    }

    /**
     * Set stato
     *
     * @param integer $stato
     * @return Licenza
     */
    public function setStato($stato) {
        $this->stato = $stato;

        return $this;
    }

    /**
     * Get stato
     *
     * @return integer 
     */
    public function getStato() {
        return $this->stato;
    }

    public function getCodiceEsteso() {
        $sigla = array(
            $this->getGruppo()->getPackage()->getSigla(),
            $this->getGruppo()->getSigla(),
            $this->getSigla(),
        );
        return implode('-', $sigla);
    }

    public function getStatoTestuale() {
        switch ($this->stato) {
            case self::$S_DEL:
                return 'Fuori catalogo';
            case self::$S_DOW:
                return 'Prezzo ribassato';
            case self::$S_HID:
                return 'Visibile solo da Gestione';
            case self::$S_LIM:
                return 'Offerta lancio';
            case self::$S_NEW:
                return 'Nuovo';
            case self::$S_NOP:
                return 'Nessuna modifica';
            case self::$S_SAL:
                return 'In offerta';
            case self::$S_UPD:
                return 'Aggiornato';
            default:
                return '-';
        }
    }

    public function getStatoClasse() {
        switch ($this->stato) {
            case self::$S_DEL:
                return 'no-display';
            case self::$S_DOW:
                return 'label-warning';
            case self::$S_HID:
                return 'no-display';
            case self::$S_LIM:
                return 'label-success';
            case self::$S_NEW:
                return 'label-green';
            case self::$S_NOP:
                return 'no-display';
            case self::$S_SAL:
                return 'label-important';
            case self::$S_UPD:
                return 'label-info';
            default:
                return '-';
        }
    }

    /**
     * Set ordine
     *
     * @param integer $ordine
     * @return Licenza
     */
    public function setOrdine($ordine) {
        $this->ordine = $ordine;

        return $this;
    }

    /**
     * Get ordine
     *
     * @return integer 
     */
    public function getOrdine() {
        return $this->ordine;
    }

    /**
     * Set autoinstall
     *
     * @param boolean $autoinstall
     * @return Licenza
     */
    public function setAutoinstall($autoinstall) {
        $this->autoinstall = $autoinstall;

        return $this;
    }

    /**
     * Get autoinstall
     *
     * @return boolean 
     */
    public function getAutoinstall() {
        return $this->autoinstall;
    }

}