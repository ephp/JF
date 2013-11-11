<?php

namespace Claims\CoreBundle\Entity\Traits;

trait Documento {

    /**
     * @var \JF\ACLBundle\Entity\Cliente
     * 
     * @ORM\ManyToOne(targetEntity="JF\ACLBundle\Entity\Cliente")
     * @ORM\JoinColumn(name="cliente_id", referencedColumnName="id")
     */
    private $cliente;

    /**
     * @var \JF\ACLBundle\Entity\Gestore
     * 
     * @ORM\ManyToOne(targetEntity="JF\ACLBundle\Entity\Gestore")
     * @ORM\JoinColumn(name="gestore_id", referencedColumnName="id", nullable=true)
     */
    private $gestore;

    /**
     * @var string
     *
     * @ORM\Column(name="titolo", type="string", length=255)
     */
    private $titolo;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=255)
     */
    private $filename;

    /**
     * @var string
     *
     * @ORM\Column(name="mimetype", type="text")
     */
    private $mimetype;

    /**
     * @var string
     *
     * @ORM\Column(name="size", type="integer")
     */
    private $size;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="importante", type="boolean", nullable=true)
     */
    private $importante;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * Set cliente
     *
     * @param integer $cliente
     * @return Pratica
     */
    public function setCliente($cliente) {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * Get cliente
     *
     * @return integer 
     */
    public function getCliente() {
        return $this->cliente;
    }

    /**
     * Set gestore
     *
     * @param integer $gestore
     * @return Pratica
     */
    public function setGestore($gestore) {
        $this->gestore = $gestore;

        return $this;
    }

    /**
     * Get gestore
     *
     * @return \JF\ACLBundle\Entity\Gestore 
     */
    public function getGestore() {
        return $this->gestore;
    }

    public function getTitolo() {
        return $this->titolo;
    }

    public function getFilename() {
        return $this->filename;
    }

    public function getMimetype() {
        return $this->mimetype;
    }

    public function getSize() {
        return $this->size;
    }

    public function getImportante() {
        return $this->importante;
    }

    public function getUrl() {
        return $this->url;
    }

    public function setTitolo($titolo) {
        $this->titolo = $titolo;
        return $this;
    }

    public function setFilename($filename) {
        $this->filename = $filename;
        return $this;
    }

    public function setMimetype($mimetype) {
        $this->mimetype = $mimetype;
        return $this;
    }

    public function setSize($size) {
        $this->size = $size;
        return $this;
    }

    public function setImportante(\DateTime $importante) {
        $this->importante = $importante;
        return $this;
    }

    public function setUrl($url) {
        $this->url = $url;
        return $this;
    }

    public function getSizeKb() {
        $size = $this->size;
        if($size < 1024) {
            return $size.' b';
        }
        $size = $size / 1024;
        if($size < 1024) {
            return number_format($size).' kb';
        }
        $size = $size / 1024;
        if($size < 1024) {
            return number_format($size, 1).' Mb';
        }
        $size = $size / 1024;
        return number_format($size, 2).' Tb';
    }


}
