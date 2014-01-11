<?php

namespace Claims\HAuditBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PraticaQuestion
 *
 * @ORM\Table(name="claims_h_audit_pratica_question")
 * @ORM\Entity(repositoryClass="Claims\HAuditBundle\Entity\PraticaQuestionRepository")
 */
class PraticaQuestion {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Pratica
     * 
     * @ORM\ManyToOne(targetEntity="Pratica")
     * @ORM\JoinColumn(name="pratica_id", referencedColumnName="id")
     */
    private $pratica;

    /**
     * @var Question
     * 
     * @ORM\ManyToOne(targetEntity="Question")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     */
    private $question;

    /**
     * @var integer
     *
     * @ORM\Column(name="ordine", type="integer")
     */
    private $ordine;

    /**
     * @var text
     *
     * @ORM\Column(name="response", type="text")
     */
    private $response;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set audit
     *
     * @param integer $pratica
     * @return PraticaQuestion
     */
    public function setPratica($pratica) {
        $this->pratica = $pratica;

        return $this;
    }

    /**
     * Get audit
     *
     * @return integer 
     */
    public function getPratica() {
        return $this->pratica;
    }

    /**
     * Set question
     *
     * @param integer $question
     * @return PraticaQuestion
     */
    public function setQuestion($question) {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return integer 
     */
    public function getQuestion() {
        return $this->question;
    }

    /**
     * Set ordine
     *
     * @param integer $ordine
     * @return PraticaQuestion
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
     * Set response
     *
     * @param string $response
     * @return PraticaQuestion
     */
    public function setResponse($response) {
        $this->response = $response;

        return $this;
    }

    /**
     * Get response
     *
     * @return string 
     */
    public function getResponse() {
        return $this->response;
    }

}
