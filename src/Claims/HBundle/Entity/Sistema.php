<?php

namespace Claims\HBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sistema
 *
 * @ORM\Table(name="claims_h_sistemi")
 * @ORM\Entity(repositoryClass="Claims\HBundle\Entity\SistemaRepository")
 */
class Sistema {

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
     * @ORM\Column(name="nome", type="string", length=16)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="url_base", type="string", length=255, nullable=true)
     */
    private $urlBase;

    /**
     * @var array
     *
     * @ORM\Column(name="actions", type="array", nullable=true)
     */
    private $actions;

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
     * @return Sistema
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
     * Set urlBase
     *
     * @param string $urlBase
     * @return Sistema
     */
    public function setUrlBase($urlBase) {
        $this->urlBase = $urlBase;

        return $this;
    }

    /**
     * Get urlBase
     *
     * @return string 
     */
    public function getUrlBase() {
        return $this->urlBase;
    }

    /**
     * Set actions
     *
     * @param array $actions
     * @return Sistema
     */
    public function setActions($actions) {
        $this->actions = $actions;

        return $this;
    }

    /**
     * Get actions
     *
     * @return array 
     */
    public function getActions() {
        return $this->actions;
    }

    public function __toString() {
        return  'Sistema'. $this->nome;
    }

}
