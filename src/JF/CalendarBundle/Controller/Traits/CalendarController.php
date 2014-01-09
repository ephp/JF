<?php

namespace JF\CalendarBundle\Controller\Traits;

trait CalendarController {

    protected $APPUNTAMENTO = "APP";
    protected $PROMEMORIA = "PRM";
    protected $SCADENZA = "SCD";
    protected $RIUNIONE_CLIENTI = "RIE";
    protected $RIUNIONE_INTERNA = "RII";
    protected $JF_SYSTEM = "JFS";

    /**
     * @return \Ephp\Bundle\CalendarBundle\Entity\Calendario
     */
    protected function getCalendar() {

        $cal = $this->findOneBy('EphpCalendarBundle:Calendario', array('sigla' => 'JFP'));
        if (!$cal) {
            $_cal = $this->getRepository('EphpCalendarBundle:Calendario');
            /* @var $_cal \Ephp\Bundle\CalendarBundle\Entity\CalendarioRepository */
            $cal = $_cal->createCalendario('JFP', 'JF-Calendar');
        }
        return $cal;
    }

    /**
     * @param string $tipo
     * @return \Ephp\Bundle\CalendarBundle\Entity\Tipo
     */
    protected function getTipoEvento($sigla) {
        $cal = $this->getCalendar();
        $tipo = $this->findOneBy('EphpCalendarBundle:Tipo', array('calendario' => $cal->getId(), 'sigla' => $sigla));
        if (!$tipo) {
            $_tipo = $this->getRepository('EphpCalendarBundle:Tipo');
            /* @var $_tipo \Ephp\CalendarBundle\Entity\TipoRepository */
            $tipi = $this->getUser()->getCliente()->getTipiEventiPrivati($this->container->getParameter('jf.tipi_evento', array()), false);
            if(isset($tipi[$sigla])) {
                $tipo = $_tipo->createTipo($sigla, $tipi[$sigla]['name'], $tipi[$sigla]['colore'], $cal, $tipi[$sigla]['cancellabile'], $tipi[$sigla]['modificabile'], $tipi[$sigla]['pubblico']);
            } else {
                throw new \Exception("Tipo evento {$sigla} non disponibile per questa utenza");
            }
        }
        return $tipo;
    }

    /**
     * @param string $sigla
     * @param \Claims\HBundle\Entity\Pratica $pratica
     * @param string $titolo
     * @param string $note
     * @return \Claims\HBundle\Entity\Evento
     */
    protected function newEvento($sigla, $titolo = null, $note = "", \DateTime $oggi = null, $giornoIntero = true, $importante = false) {
        if(!$oggi) {
            $oggi = new \DateTime();
        }
        $cal = $this->getCalendar();
        $tipo = $this->getTipoEvento($sigla);
        $evento = new \JF\CalendarBundle\Entity\Evento();
        $evento->setCalendario($cal)
                ->setCliente($this->getUser()->getCliente())
                ->setGestore($this->getUser())
                ->setTipo($tipo)
                ->setDataOra($oggi)
                ->setGiornoIntero($giornoIntero)
                ->setImportante($importante)
                ->setNote($note)
                ->setTitolo($titolo ? $titolo : $tipo->getNome());
        return $evento;
    }

}
