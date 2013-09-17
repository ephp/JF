<?php

namespace JF\ACLBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ephp\ACLBundle\Model\BaseAccessLog;

/**
 * BaseAccessLog
 *
 * @ORM\Table(name="acl_access_log")
 * @ORM\Entity(repositoryClass="JF\ACLBundle\Entity\AccessLogGestoreRepository")
 */
class AccessLogGestore extends BaseAccessLog {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Gestore")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private $user;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param string $user
     * @return Gestore
     */
    public function setUser($user) {
        if(! $user instanceof Gestore) {
            throw new \Exception('Classe Gestore non conforme a quella attesa');
        }
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return Gestore 
     */
    public function getUser() {
        return $this->user;
    }

}
