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
     * @var string
     *
     * @ORM\Column(name="questione", type="string", length=255)
     */
    private $questione;

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
        $this->questione = $questione;

        return $this;
    }

    /**
     * Get questione
     *
     * @return string 
     */
    public function getQuestione() {
        return $this->questione;
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

}
