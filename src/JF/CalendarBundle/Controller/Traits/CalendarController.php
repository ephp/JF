<?php

namespace JF\CalendarBundle\Controller\Traits;

trait CalendarController {

    protected $APPUNTAMENTO = "APP";
    protected $PROMEMORIA = "PRM";
    protected $SCADENZA = "SCD";
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
            $_tipo = $this->getEm()->getRepository('EphpCalendarBundle:Tipo');
            /* @var $_tipo \Ephp\CalendarBundle\Entity\TipoRepository */
            switch ($sigla) {
                case $this->APPUNTAMENTO:
                    $tipo = $_tipo->createTipo($this->APPUNTAMENTO, 'Appuntamento', '0000aa', $cal, true, true, true);
                    break;
                case $this->PROMEMORIA:
                    $tipo = $_tipo->createTipo($this->PROMEMORIA, 'Promemoria', '00aa00', $cal, true, true, true);
                    break;
                case $this->SCADENZA:
                    $tipo = $_tipo->createTipo($this->SCADENZA, 'Scadenza', 'aa0000', $cal, true, true, true);
                    break;
                case $this->JF_SYSTEM:
                    $tipo = $_tipo->createTipo($this->JF_SYSTEM, 'JF-System', 'ff0000', $cal, false, false, false);
                    break;
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
