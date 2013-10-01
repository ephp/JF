<?php

namespace Claims\HBundle\Controller\Traits;

trait CalendarController {

    protected $ANALISI_SINISTRI_COPERTURA = "ASC";
    protected $VERIFICA_INCARICHI_MEDICI = "VIM";
    protected $RICERCA_POLIZZE_MEDICI = "RPM";
    protected $RELAZIONE_RISERVA = "RER";
    protected $RICHIESTA_SA = "RSA";
    protected $TRATTATIVE_AGGIORNAMENTI = "TAX";
    protected $JWEB = "JWB";
    protected $EMAIL_JWEB = "EJW";
    protected $CANCELLERIA_TELEMATICA = "CNT";
    protected $RAVINALE = "RVP";
    protected $EMAIL_RAVINALE = "MRV";
    protected $ATTIVITA_MANUALE = "OTH";
    protected $EMAIL_MANUALE = "EML";
    protected $RISCHEDULAZIONE = "RIS";
    protected $VERIFICA_PERIODICA = "VER";
    protected $CAMBIO_STATO_OPERATIVO = "CHS";
    protected $CAMBIO_GESTORE = "CHG";
    protected $PRIORITA = "PRI";

    /**
     * @return \Ephp\Bundle\CalendarBundle\Entity\Calendario
     */
    protected function getCalendar() {

        $cal = $this->findOneBy('EphpCalendarBundle:Calendario', array('sigla' => 'JFCH'));
        if (!$cal) {
            $_cal = $this->getRepository('EphpCalendarBundle:Calendario');
            /* @var $_cal \Ephp\Bundle\CalendarBundle\Entity\CalendarioRepository */
            $cal = $_cal->createCalendario('JFCH', 'JF-Claims Hospital');
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
            /* @var $_tipo \Ephp\Bundle\CalendarBundle\Entity\TipoRepository */
            switch ($sigla) {
                case $this->ANALISI_SINISTRI_COPERTURA:
                    $tipo = $_tipo->createTipo($this->ANALISI_SINISTRI_COPERTURA, 'Analisi Sinistri e Copertura', '44aa44', $cal, false);
                    break;
                case $this->VERIFICA_INCARICHI_MEDICI:
                    $tipo = $_tipo->createTipo($this->VERIFICA_INCARICHI_MEDICI, 'Verifica Incarichi e Medici', '44aa44', $cal, false);
                    break;
                case $this->RICERCA_POLIZZE_MEDICI:
                    $tipo = $_tipo->createTipo($this->RICERCA_POLIZZE_MEDICI, 'Ricerca Polizze e Medici', '44aa44', $cal, false);
                    break;
                case $this->RELAZIONE_RISERVA:
                    $tipo = $_tipo->createTipo($this->RELAZIONE_RISERVA, 'Relazione e Riserva', '44aa44', $cal, false);
                    break;
                case $this->RICHIESTA_SA:
                    $tipo = $_tipo->createTipo($this->RICHIESTA_SA, 'Richiesta di SA', '44aa44', $cal, false);
                    break;
                case $this->TRATTATIVE_AGGIORNAMENTI:
                    $tipo = $_tipo->createTipo($this->TRATTATIVE_AGGIORNAMENTI, 'Trattative e Aggiornamenti', '44aa44', $cal, false);
                    break;
                case $this->JWEB:
                    $tipo = $_tipo->createTipo($this->JWEB, 'J-Web Claims', 'aa4444', $cal);
                    break;
                case $this->EMAIL_JWEB:
                    $tipo = $_tipo->createTipo($this->EMAIL_JWEB, 'Email da J-Web Claims', 'aa2222', $cal, true, false, true);
                    break;
                case $this->CANCELLERIA_TELEMATICA:
                    $tipo = $_tipo->createTipo($this->CANCELLERIA_TELEMATICA, 'Cancelleria Telematiche', '44aaaa', $cal);
                    break;
                case $this->RAVINALE:
                    $tipo = $_tipo->createTipo($this->RAVINALE, 'Ravinale Piemonte', 'aa44aa', $cal);
                    break;
                case $this->EMAIL_RAVINALE:
                    $tipo = $_tipo->createTipo($this->EMAIL_RAVINALE, 'Email da Ravinale Piemonte', 'aa22aa', $cal, true, false, true);
                    break;
                case $this->ATTIVITA_MANUALE:
                    $tipo = $_tipo->createTipo($this->ATTIVITA_MANUALE, 'Attività manuali', 'aaaa44', $cal);
                    break;
                case $this->EMAIL_MANUALE:
                    $tipo = $_tipo->createTipo($this->EMAIL_MANUALE, 'Email', 'aaaa22', $cal, true, false, true);
                    break;
                case $this->RISCHEDULAZIONE:
                    $tipo = $_tipo->createTipo($this->RISCHEDULAZIONE, 'Rischedulazione', '444444', $cal, true, false, false);
                    break;
                case $this->PRIORITA:
                    $_tipo->createTipo($this->PRIORITA, 'Priorita', '444444', $cal, false, false, false);
                    break;
                case $this->VERIFICA_PERIODICA:
                    $tipo = $_tipo->createTipo($this->VERIFICA_PERIODICA, 'Verifica periodica', '44aa44', $cal);
                    break;
                case $this->CAMBIO_STATO_OPERATIVO:
                    $tipo = $_tipo->createTipo($this->CAMBIO_STATO_OPERATIVO, 'Cambio Stato Operativo', '4444aa', $cal, true, false, false);
                    break;
                case $this->CAMBIO_GESTORE:
                    $tipo = $_tipo->createTipo($this->CAMBIO_GESTORE, 'Cambio Gestore', '444444', $cal, false, false, false);
                    break;
            }
        }
        return $tipo;
    }

    /**
     * @param string $sigla
     * @param \Ephp\Bundle\SinistriBundle\Entity\Scheda $pratica
     * @param string $titolo
     * @param string $note
     * @return \Ephp\Bundle\SinistriBundle\Traits\Controller\Evento
     */
    protected function newEvento($sigla, \Claims\HBundle\Entity\Pratica $pratica, $titolo = null, $note = "") {
        $oggi = new \DateTime();
        $cal = $this->getCalendar();
        $tipo = $this->getTipoEvento($sigla);
        $evento = new \Claims\HBundle\Entity\Evento();
        $evento->setCalendario($cal)
                ->setCliente($this->getUser()->getCliente())
                ->setGestore($pratica->getGestore())
                ->setTipo($tipo)
                ->setDataOra($oggi)
                ->setDeltaG(0)
                ->setGiornoIntero(true)
                ->setImportante(false)
                ->setNote($note)
                ->setPratica($pratica)
                ->setTitolo($titolo ? $titolo : $tipo->getNome());
        return $evento;
    }

    protected function generatore() {
        return array(
            array('tipo' => $this->ANALISI_SINISTRI_COPERTURA, 'giorni' => 0),
            array('tipo' => $this->VERIFICA_INCARICHI_MEDICI, 'giorni' => 10),
            array('tipo' => $this->RICERCA_POLIZZE_MEDICI, 'giorni' => 25),
            array('tipo' => $this->RELAZIONE_RISERVA, 'giorni' => 14),
            array('tipo' => $this->RICHIESTA_SA, 'giorni' => 14),
            array('tipo' => $this->TRATTATIVE_AGGIORNAMENTI, 'giorni' => 30),
            array('tipo' => $this->TRATTATIVE_AGGIORNAMENTI, 'giorni' => 14, 'from' => 1),
            array('tipo' => $this->TRATTATIVE_AGGIORNAMENTI, 'giorni' => 14, 'from' => 2)
        );
    }

}