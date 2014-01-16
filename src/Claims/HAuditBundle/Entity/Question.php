<?php

namespace Claims\HAuditBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Question
 *
 * @ORM\Table(name="claims_h_audit_question")
 * @ORM\Entity(repositoryClass="Claims\HAuditBundle\Entity\QuestionRepository")
 */
class Question {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \JF\ACLBundle\Entity\Cliente
     * 
     * @ORM\ManyToOne(targetEntity="JF\ACLBundle\Entity\Cliente")
     * @ORM\JoinColumn(name="cliente_id", referencedColumnName="id")
     */
    private $cliente;

    /**
     * @var string
     *
     * @ORM\Column(name="question", type="string", length=255)
     */
    private $question;

    /**
     * @var string
     *
     * @ORM\Column(name="example", type="string", length=255, nullable=true)
     */
    private $example;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var array
     *
     * @ORM\Column(name="options", type="array")
     */
    private $options;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set questione
     *
     * @param string $questione
     * @return Question
     */
    public function setQuestione($questione) {
        $this->question = $questione;

        return $this;
    }

    /**
     * Get questione
     *
     * @return string 
     */
    public function getQuestione() {
        return $this->question;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Question
     */
    public function setType($type) {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Set options
     *
     * @param array $options
     * @return Question
     */
    public function setOptions($options) {
        $this->options = $options;

        return $this;
    }

    /**
     * Get options
     *
     * @return array 
     */
    public function getOptions() {
        return $this->options;
    }


    /**
     * Set question
     *
     * @param string $question
     * @return Question
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    
        return $this;
    }

    /**
     * Get question
     *
     * @return string 
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set example
     *
     * @param string $example
     * @return Question
     */
    public function setExample($example)
    {
        $this->example = $example;
    
        return $this;
    }

    /**
     * Get example
     *
     * @return string 
     */
    public function getExample()
    {
        return $this->example;
    }

    /**
     * Set cliente
     *
     * @param \JF\ACLBundle\Entity\Cliente $cliente
     * @return Question
     */
    public function setCliente(\JF\ACLBundle\Entity\Cliente $cliente = null)
    {
        $this->cliente = $cliente;
    
        return $this;
    }

    /**
     * Get cliente
     *
     * @return \JF\ACLBundle\Entity\Cliente 
     */
    public function getCliente()
    {
        return $this->cliente;
    }
}