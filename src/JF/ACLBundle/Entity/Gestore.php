<?php

namespace JF\ACLBundle\Entity;

use Ephp\ACLBundle\Model\BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * Gestore
 *
 * @ORM\Table(name="acl_gestori")
 * @ORM\Entity(repositoryClass="JF\ACLBundle\Entity\GestoreRepository")
 */
class Gestore extends BaseUser {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="sigla", type="string", length=3)
     */
    protected $sigla;

    /**
     * @var string
     *
     * @ORM\Column(name="nome", type="string", length=255)
     */
    protected $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", length=16)
     */
    protected $telefono;

    /**
     * @var Cliente
     * 
     * @ORM\ManyToOne(targetEntity="Cliente")
     * @ORM\JoinColumn(name="cliente_id", referencedColumnName="id", nullable=true)
     */
    private $cliente;

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
     * @return Gestore
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
     * @return Gestore
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
     * Set telefono
     *
     * @param string $telefono
     * @return Gestore
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
     * Set cliente
     *
     * @param Cliente $cliente
     * @return Gestore
     */
    public function setCliente($cliente) {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * Get cliente
     *
     * @return Cliente 
     */
    public function getCliente() {
        return $this->cliente;
    }

    public function hasRole($role) {
        if (is_array($role)) {
            $roles = $role;
            foreach ($roles as $role) {
                if (in_array($role, array('R_EPH', 'R_SUPER')) || !$this->getCliente()) {
                    return parent::hasRole($roles);
                }
            }
            $_roles = array_intersect($this->getCliente()->getRoles(), $this->getRoles());
            foreach ($roles as $role) {
                foreach ($_roles as $_role) {
                    if (strtoupper($_role) == strtoupper($role)) {
                        return true;
                    }
                }
            }
            return false;
        }
        if (in_array($role, array('R_EPH', 'R_SUPER')) || !$this->getCliente()) {
            return parent::hasRole($role);
        }

        $roles = array_intersect($this->getCliente()->getRoles(), $this->getRoles());
        foreach ($roles as $_role) {
            if (strtoupper($_role) == strtoupper($role)) {
                return true;
            }
        }
        return false;
    }

    public function isCredentialsNonExpired() {
        if ($this->getCliente()) {
            if (!$this->hasRole('R_SUPER')) {
                if ($this->getCliente()->getBloccato()) {
                    return false;
                }
            }
        }
        return parent::isCredentialsNonExpired();
    }

    public function get($key, $default = null) {
        $out = null;
        if ($this->getCliente()) {
            $out = $this->getCliente()->get($key);
        }
        if(!$out) {
            $d = $this->getDati();
            if(isset($d[$key])) {
                $out = $d[$key];
            }
        }
        return $out ?: $default;
    }

    public function set($key, $value) {
        $d = $this->getDati();
        $d[$key] = $value;
        $this->setDati($d);
        return $this;
    }

}