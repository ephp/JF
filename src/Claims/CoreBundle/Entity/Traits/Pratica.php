<?php

namespace Claims\CoreBundle\Entity\Traits;

trait Pratica
{
    /**
     * @var string
     *
     * @ORM\Column(name="codice", type="string", length=32)
     */
    private $codice;

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
     * @var \Claims\CoreBundle\Entity\StatoPratica
     * 
     * @ORM\ManyToOne(targetEntity="Claims\CoreBundle\Entity\StatoPratica")
     * @ORM\JoinColumn(name="stato_pratica_id", referencedColumnName="id", nullable=true)
     */
    private $statoPratica;

    /**
     * @var \Claims\CoreBundle\Entity\Priorita
     * 
     * @ORM\ManyToOne(targetEntity="Claims\CoreBundle\Entity\Priorita")
     * @ORM\JoinColumn(name="priorita_id", referencedColumnName="id", nullable=true)
     */
    private $priorita;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", nullable=true)
     */
    private $note;

    /**
     * @var string
     *
     * @ORM\Column(name="logs", type="array", nullable=true)
     */
    private $logs;
        
    /**
     * @Gedmo\Slug(fields={"codice"}, style="default", separator="-", updatable=true, unique=true)    
     * @ORM\Column(name="slug", type="string", length=64, unique=true)
     */
    protected $slug;
    
    /**
     * Set codice
     *
     * @param string $codice
     * @return Pratica
     */
    public function setCodice($codice)
    {
        $this->codice = $codice;
    
        return $this;
    }

    /**
     * Get codice
     *
     * @return string 
     */
    public function getCodice()
    {
        return $this->codice;
    }

    /**
     * Set cliente
     *
     * @param integer $cliente
     * @return Pratica
     */
    public function setCliente($cliente)
    {
        $this->cliente = $cliente;
    
        return $this;
    }

    /**
     * Get cliente
     *
     * @return integer 
     */
    public function getCliente()
    {
        return $this->cliente;
    }

    /**
     * Set gestore
     *
     * @param integer $gestore
     * @return Pratica
     */
    public function setGestore($gestore)
    {
        $this->gestore = $gestore;
    
        return $this;
    }

    /**
     * Get gestore
     *
     * @return \JF\ACLBundle\Entity\Gestore 
     */
    public function getGestore()
    {
        return $this->gestore;
    }

    /**
     * Set statoPratica
     *
     * @param integer $statoPratica
     * @return Pratica
     */
    public function setStatoPratica($statoPratica)
    {
        $this->statoPratica = $statoPratica;
    
        return $this;
    }

    /**
     * Get statoPratica
     *
     * @return \Claims\CoreBundle\Entity\StatoPratica 
     */
    public function getStatoPratica()
    {
        return $this->statoPratica;
    }

    /**
     * Set priorita
     *
     * @param \Claims\CoreBundle\Entity\Priorita $priorita
     * @return Pratica
     */
    public function setPriorita($priorita)
    {
        $this->priorita = $priorita;
    
        return $this;
    }

    /**
     * Get priorita
     *
     * @return \Claims\CoreBundle\Entity\Priorita 
     */
    public function getPriorita()
    {
        return $this->priorita;
    }

    /**
     * Set note
     *
     * @param string $note
     * @return Pratica
     */
    public function setNote($note)
    {
        $this->note = $note;
    
        return $this;
    }

    /**
     * Get note
     *
     * @return string 
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Pratica
     */
    public function setSlug($slug) {
        $this->slug = $slug;
    
        return $this;
    }
    
    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug() {
        return $this->slug;
    }
    
    /**
     * Set logs
     *
     * @param array $logs
     * @return Pratica
     */
    public function setLogs($logs)
    {
        $this->logs = $logs;
    
        return $this;
    }

    /**
     * Get logs
     *
     * @return array 
     */
    public function getLogs()
    {
        return $this->logs;
    }
    
    /**
     * Add log
     *
     * @param array $logs
     * @return Pratica
     */
    public function addLog($log) {
        if(!$this->logs) {
            $this->logs = array();
        }
        $oggi = new \DateTime();
        if(isset($this->logs[$oggi->format('Y-m-d')])) {
            $this->logs[$oggi->format('Y-m-d')]['info'] = array_merge($this->logs[$oggi->format('Y-m-d')]['info'], $log);            
        } else {
            $this->logs[$oggi->format('Y-m-d')] = array(
                'data' => $oggi->format('d-m-Y'),
                'info' => $log,
            );
        }
        krsort($this->logs);
        
        return $this;
    }
    
    public function getLastLog($html = true) {
        if(is_array($this->logs) && count($this->logs) > 0) {
            $tmp = $this->logs;
            $log = array_shift($tmp);
            return $html ? "<ul class=\"nav\"><li>".implode("</li><li>", $log['info'])."</li></ul>" : implode("; ", $log['info']);
        }
        return 'No log';
    }
}
