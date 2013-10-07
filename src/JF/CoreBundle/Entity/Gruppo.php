<?php

namespace JF\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Package
 *
 * @ORM\Table(name="jf_gruppi_licenze")
 * @ORM\Entity(repositoryClass="JF\CoreBundle\Entity\GruppoRepository")
 */
class Gruppo {

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
     * @ORM\ManyToOne(targetEntity="Package")
     * @ORM\JoinColumn(name="package_id", referencedColumnName="id")
     */
    private $package;

    /**
     * @var string
     *
     * @ORM\Column(name="sigla", type="string", length=16)
     */
    private $sigla;

    /**
     * @var string
     *
     * @ORM\Column(name="nome", type="string", length=32)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="descrizione", type="text")
     */
    private $descrizione;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set package
     *
     * @param \JF\CoreBundle\Entity\Package $package
     * @return Licenza
     */
    public function setPackage(\JF\CoreBundle\Entity\Package $package = null) {
        $this->package = $package;

        return $this;
    }

    /**
     * Get package
     *
     * @return \JF\CoreBundle\Entity\Package 
     */
    public function getPackage() {
        return $this->package;
    }

    /**
     * Set sigla
     *
     * @param string $sigla
     * @return Package
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
     * Get sigla
     *
     * @return string 
     */
    public function getSiglaCompleta() {
        $sigla = array(
            $this->getPackage()->getSigla(),
            $this->sigla,
        );
        return implode('-', $sigla);
    }

    /**
     * Set nome
     *
     * @param string $nome
     * @return Package
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
     * @return Package
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

}
