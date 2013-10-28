<?php

namespace JF\ACLBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Studio
 *
 * @ORM\Table(name="acl_clienti")
 * @ORM\Entity(repositoryClass="JF\ACLBundle\Entity\ClienteRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Cliente {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nome", type="string", length=128)
     * @Assert\NotBlank()
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="indirizzo", type="string", length=128)
     * @Assert\NotBlank()
     */
    private $indirizzo;

    /**
     * @var string
     *
     * @ORM\Column(name="citta", type="string", length=64)
     * @Assert\NotBlank()
     */
    private $citta;

    /**
     * @var string
     *
     * @ORM\Column(name="cap", type="string", length=5)
     * @Assert\NotBlank()
     */
    private $cap;

    /**
     * @var string
     *
     * @ORM\Column(name="partita_iva", type="string", length=16)
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/\([0-9]{11}|[A-Za-z0-9]{16}/",
     *     match=false,
     *     message="Partita IVA non valida"
     * )     
     */
    private $partitaIva;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", length=16, nullable=true)
     * @Assert\Regex(
     *     pattern="/^(\+[0-9]{2,4} )?[0-9. \-\/]{4,12}$/",
     *     message="Numero telefono non valido"
     * )     
     */
    private $telefono;

    /**
     * @var string
     *
     * @ORM\Column(name="cellulare", type="string", length=16, nullable=true)
     * @Assert\Regex(
     *     pattern="/^(\+[0-9]{2,4} )?[0-9. \-\/]{4,12}$/",
     *     message="Numero cellulare non valido"
     * )     
     */
    private $cellulare;

    /**
     * @var string
     *
     * @ORM\Column(name="fax", type="string", length=16, nullable=true)
     * @Assert\Regex(
     *     pattern="/^(\+[0-9]{2,4} )?[0-9. \-\/]{4,12}$/",
     *     message="Numero fax non valido"
     * )     
     */
    private $fax;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=64)
     * @Assert\NotBlank()
     * @Assert\Email(
     *     message = "L'email '{{ value }}' non è valida.",
     *     checkMX = true
     * )
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="sito", type="string", length=64, nullable=true)
     * @Assert\Url()
     */
    private $sito;

    /**
     * @var User
     *
     * @ORM\OneToOne(targetEntity="Gestore")
     * @ORM\JoinColumn(name="referente_id", referencedColumnName="id", nullable=true)
     */
    private $referente;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Gestore")
     * @ORM\JoinColumn(name="presentatore_id", referencedColumnName="id", nullable=true)
     */
    private $presentatore;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Gestore", mappedBy="cliente", cascade="all")
     */
    private $utenze;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Licenza", mappedBy="cliente", cascade="all")
     */
    private $licenze;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="scadenza_licenza", type="datetime")
     */
    private $scadenzaLicenza;

    /**
     * @var boolean
     *
     * @ORM\Column(name="bloccato", type="boolean")
     */
    private $bloccato;

    /**
     * @var string
     *
     * @ORM\Column(name="dati", type="array", nullable=null)
     */
    protected $dati;

    /**
     * Constructor
     */
    public function __construct() {
        $this->utenze = new \Doctrine\Common\Collections\ArrayCollection();
        $this->licenze = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set nome
     *
     * @param string $nome
     * @return Studio
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
     * Set indirizzo
     *
     * @param string $indirizzo
     * @return Studio
     */
    public function setIndirizzo($indirizzo) {
        $this->indirizzo = $indirizzo;

        return $this;
    }

    /**
     * Get indirizzo
     *
     * @return string 
     */
    public function getIndirizzo() {
        return $this->indirizzo;
    }

    /**
     * Set citta
     *
     * @param string $citta
     * @return Studio
     */
    public function setCitta($citta) {
        $this->citta = $citta;

        return $this;
    }

    /**
     * Get citta
     *
     * @return string 
     */
    public function getCitta() {
        return $this->citta;
    }

    /**
     * Set cap
     *
     * @param string $cap
     * @return Studio
     */
    public function setCap($cap) {
        $this->cap = $cap;

        return $this;
    }

    /**
     * Get cap
     *
     * @return string 
     */
    public function getCap() {
        return $this->cap;
    }

    /**
     * Set partitaIva
     *
     * @param string $partitaIva
     * @return Studio
     */
    public function setPartitaIva($partitaIva) {
        $this->partitaIva = $partitaIva;

        return $this;
    }

    /**
     * Get partitaIva
     *
     * @return string 
     */
    public function getPartitaIva() {
        return $this->partitaIva;
    }

    /**
     * Set telefono
     *
     * @param string $telefono
     * @return Studio
     */
    public function setTelefono($telefono) {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono
     *
     * @return string 
     */
    public function getTelefono() {
        return $this->telefono;
    }

    /**
     * Set cellulare
     *
     * @param string $cellulare
     * @return Studio
     */
    public function setCellulare($cellulare) {
        $this->cellulare = $cellulare;

        return $this;
    }

    /**
     * Get cellulare
     *
     * @return string 
     */
    public function getCellulare() {
        return $this->cellulare;
    }

    /**
     * Set fax
     *
     * @param string $fax
     * @return Studio
     */
    public function setFax($fax) {
        $this->fax = $fax;

        return $this;
    }

    /**
     * Get fax
     *
     * @return string 
     */
    public function getFax() {
        return $this->fax;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Studio
     */
    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set sito
     *
     * @param string $sito
     * @return Studio
     */
    public function setSito($sito) {
        $this->sito = $sito;

        return $this;
    }

    /**
     * Get sito
     *
     * @return string 
     */
    public function getSito() {
        return $this->sito;
    }

    /**
     * Set referente
     *
     * @param Geastore $referente
     * @return Studio
     */
    public function setReferente($referente) {
        $this->referente = $referente;

        return $this;
    }

    /**
     * Get referente
     *
     * @return Gestore
     */
    public function getReferente() {
        return $this->referente;
    }

    /**
     * Set presentatore
     *
     * @param Geastore $presentatore
     * @return Studio
     */
    public function setPresentatore($presentatore) {
        $this->presentatore = $presentatore;

        return $this;
    }

    /**
     * Get presentatore
     *
     * @return Gestore
     */
    public function getPresentatore() {
        return $this->presentatore;
    }

    /**
     * Set scadenzaLicenza
     *
     * @param \DateTime $scadenzaLicenza
     * @return Studio
     */
    public function setScadenzaLicenza($scadenzaLicenza) {
        $this->scadenzaLicenza = $scadenzaLicenza;

        return $this;
    }

    /**
     * Get scadenzaLicenza
     *
     * @return \DateTime 
     */
    public function getScadenzaLicenza() {
        return $this->scadenzaLicenza;
    }

    /**
     * Set bloccato
     *
     * @param boolean $bloccato
     * @return Studio
     */
    public function setBloccato($bloccato) {
        $this->bloccato = $bloccato;

        return $this;
    }

    /**
     * Get bloccato
     *
     * @return boolean 
     */
    public function getBloccato() {
        return $this->bloccato;
    }

    /**
     * Add utenze
     *
     * @param \JF\ACLBundle\Entity\Gestore $utenze
     * @return Cliente
     */
    public function addUtenze(\JF\ACLBundle\Entity\Gestore $utenze) {
        $this->utenze[] = $utenze;

        return $this;
    }

    /**
     * Remove utenze
     *
     * @param \JF\ACLBundle\Entity\Gestore $utenze
     */
    public function removeUtenze(\JF\ACLBundle\Entity\Gestore $utenze) {
        $this->utenze->removeElement($utenze);
    }

    /**
     * Get utenze
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUtenze() {
        return $this->utenze;
    }

    /**
     * Add licenze
     *
     * @param Licenza $licenze
     * @return Cliente
     */
    public function addLicenze(\JF\ACLBundle\Entity\Gestore $licenze) {
        $this->licenze[] = $licenze;

        return $this;
    }

    /**
     * Add licenze
     *
     * @param Licenza $licenze
     * @return boolean
     */
    public function getLicenza($licenza_id) {
        foreach ($this->getLicenze() as $licenza) {
            /* @var $licenza \JF\ACLBundle\Entity\Licenza */
            if ($licenza->getLicenza()->getId() == $licenza_id) {
                return $licenza;
            }
        }

        return false;
    }

    /**
     * Add licenze
     *
     * @param Licenza $licenze
     * @return boolean
     */
    public function getLicenzaGruppo($gruppo) {
        foreach ($this->getLicenze() as $licenza) {
            /* @var $licenza \JF\ACLBundle\Entity\Licenza */
            if ($licenza->getGruppo() == $gruppo) {
                return $licenza;
            }
        }

        return false;
    }

    /**
     * Remove licenze
     *
     * @param \JF\ACLBundle\Entity\Licenza $licenze
     */
    public function removeLicenze(\JF\ACLBundle\Entity\Gestore $licenze) {
        $this->licenze->removeElement($licenze);
    }

    /**
     * Get licenze
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLicenze() {
        return $this->licenze;
    }

    /**
     * Set dati
     *
     * @param Data $dati
     * @return array
     */
    public function setDati($dati) {
        $this->dati = $dati;

        return $this;
    }

    /**
     * Get dati
     *
     * @return array 
     */
    public function getDati() {
        return $this->dati;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist() {
        $this->scadenzaLicenza = new \DateTime();
        $this->bloccato = false;
    }

    public function __toString() {
        return $this->getNome();
    }

    public function getTipoUtenze() {
        $out = array();
        foreach ($this->utenze as $utenza) {
            /* @var $utenza JF\ACLBundle\Entity\Gestore */
            foreach ($utenza->getRoles() as $role) {
                if ($role != 'ROLE_USER') {
                    if (!isset($out[$role])) {
                        $out[$role] = array('role' => str_replace(array('ROLE_', 'R_'), array('', ''), $role), 'n' => 0);
                    }
                    $out[$role]['n']++;
                }
            }
        }
        usort($out, function($a, $b) {
                    return $a['n'] > $b['n'];
                });
        return $out;
    }

    public function getRoles() {
        $roles = array();
        foreach ($this->getLicenze() as $licenza) {
            /* @var $licenza Licenza */
            if (!$licenza->getScadenza() || $licenza->getScadenza()->diff(new \DateTime())->format('R') != '-') {
                foreach ($licenza->getLicenza()->getRoles() as $role) {
                    $roles[$role] = 1;
                }
            }
        }
        return array_keys($roles);
    }

    public function getWidgets() {
        $widgets = array();
        foreach ($this->getLicenze() as $licenza) {
            /* @var $licenza Licenza */
            if (!$licenza->getScadenza() || $licenza->getScadenza()->diff(new \DateTime())->format('R') != '-') {
                foreach ($licenza->getLicenza()->getWidgets() as $widget) {
                    $widgets[$widget] = 1;
                }
            }
        }
        return array_keys($widgets);
    }

    /**
     * Add licenze
     *
     * @param Licenza $licenze
     * @return boolean
     */
    public function hasLicenza($gruppo, $sigla = false) {
        if (!$sigla) {
            foreach ($this->getLicenze() as $licenza) {
                /* @var $licenza \JF\ACLBundle\Entity\Licenza */
                if ($licenza->getLicenza()->getId() == $gruppo) {
                    return true;
                }
            }

            return false;
        } else {
            if(!is_array($sigla)) {
                $sigla = array($sigla);
            }
            $licenze = $this->getLicenzeAttive();
            if (isset($licenze[$gruppo])) {
                return in_array($licenze[$gruppo], $sigla);
            }
            return false;
        }
    }

    public function getLicenzeAttive($object = false, $sorted = true) {
        $licenze = array();
        foreach ($this->getLicenze() as $licenza) {
            /* @var $licenza Licenza */
            if ($licenza->isActive()) {
                $licenze[] = $object ? $licenza : $licenza->info();
            }
        }
        return $object || !$sorted ? $licenze : $this->ordinaLicenze($licenze);
    }

    private function ordinaLicenze($licenze) {
        $out = array();
        foreach ($licenze as $licenza) {
            $out[$licenza['gruppo']->getSiglaCompleta()] = $licenza['codice'];
        }
        return $out;
    }

    private $cache = null;
    private $specialVars = array('calendario_personale', 'form_cliente');

    public function get($key, $default = null) {
        if (!$this->cache) {
            $this->cache = array();
            foreach ($this->getLicenze() as $licenza) {
                $licenza = $licenza->getLicenza();
                /* @var $licenza \JF\ACLBundle\Entity\Licenza */
                $params = $licenza->getParams();
                foreach ($params as $k => $v) {
                    if (in_array($k, $this->specialVars)) {
                        $this->cache[$k][$licenza->getGruppo()->getSiglaCompleta()] = $v;
                    } else {
                        $this->cache[$licenza->getGruppo()->getSiglaCompleta() . '.' . $k] = $v;
                    }
                }
            }
        }
//        \Ephp\UtilityBundle\Utility\Debug::pr($this->cache);
        return isset($this->cache[$key]) ? $this->cache[$key] : $default;
    }

    public function getVars() {
        if (!$this->cache) {
            $this->cache = array();
            foreach ($this->getLicenze() as $licenza) {
                $licenza = $licenza->getLicenza();
                /* @var $licenza \JF\ACLBundle\Entity\Licenza */
                $params = $licenza->getParams();
                foreach ($params as $k => $v) {
                    if (in_array($k, $this->specialVars)) {
                        $this->cache[$k][$licenza->getGruppo()->getSiglaCompleta()] = $v;
                    } else {
                        $this->cache[$licenza->getGruppo()->getSiglaCompleta() . '.' . $k] = $v;
                    }
                }
            }
        }
        return $this->cache;
    }

    public function getTipiEventiPrivati($tipi) {
        $out = array();
        $licenze = $this->getLicenze();
        foreach($tipi as $sigla => $tipo) {
            if($tipo['permission'] === true) {
                $out[$sigla] = $tipo;
            } elseif(is_array($tipo['permission'])) {
                foreach ($licenze as $licenza) {
                    $licenza = $licenza->getLicenza();
                    /* @var $licenza \JF\ACLBundle\Entity\Licenza */
                    if(in_array($licenza->getGruppo()->getSiglaCompleta(), $tipo['permission'])) {
                        $out[$sigla] = $tipo;
                    }
                }
            }
        }
        return $out;
    }

}