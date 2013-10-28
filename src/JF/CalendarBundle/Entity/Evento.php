<?php

namespace JF\CalendarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Evento
 *
 * @ORM\Table(name="jf_eventi")
 * @ORM\Entity(repositoryClass="JF\CalendarBundle\Entity\EventoRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Evento implements \JF\CalendarBundle\Interfaces\IEvento {

    use Traits\Evento;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

}