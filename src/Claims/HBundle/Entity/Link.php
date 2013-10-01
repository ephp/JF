<?php

namespace Claims\HBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Link
 *
 * @ORM\Table(name="claims_h_links")
 * @ORM\Entity(repositoryClass="Claims\HBundle\Entity\LinkRepository")
 */
class Link
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Pratica")
     * @ORM\JoinColumn(name="pratica_id", referencedColumnName="id")
     */
    private $pratica;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="text")
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="sito", type="string", length=255)
     */
    private $sito;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set scheda
     *
     * @param Pratica $scheda
     * @return Link
     */
    public function setPratica($scheda)
    {
        $this->pratica = $scheda;

        return $this;
    }

    /**
     * Get scheda
     *
     * @return Pratica
     */
    public function getPratica()
    {
        return $this->pratica;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Link
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set sito
     *
     * @param string $sito
     * @return Link
     */
    public function setSito($sito)
    {
        $this->sito = $sito;

        return $this;
    }

    /**
     * Get sito
     *
     * @return string
     */
    public function getSito()
    {
        return $this->sito;
    }
}