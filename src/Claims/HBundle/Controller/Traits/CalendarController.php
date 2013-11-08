<?php

namespace Claims\HBundle\Controller\Traits;

trait CalendarController {

    protected $ANALISI_SINISTRI_COPERTURA   = "ASC";
    protected $VERIFICA_INCARICHI_MEDICI    = "VIM";
    protected $RICERCA_POLIZZE_MEDICI       = "RPM";
    protected $RELAZIONE_RISERVA            = "RER";
    protected $RICHIESTA_SA                 = "RSA";
    protected $TRATTATIVE_AGGIORNAMENTI     = "TAX";
    protected $JWEB                         = "JWB";
    protected $EMAIL_JWEB                   = "JWE"; //"EJW";
    protected $ALL_JWEB                     = "JWA";
    protected $CANCELLERIA_TELEMATICA       = "CNT";
    protected $RAVINALE                     = "RVP";
    protected $EMAIL_RAVINALE               = "RVE"; //"MRV";
    protected $ATTIVITA_MANUALE             = "OTH";
    protected $EMAIL_MANUALE                = "EML";
    protected $RISCHEDULAZIONE              = "RIS";
    protected $VERIFICA_PERIODICA           = "VER";
    protected $CAMBIO_STATO_OPERATIVO       = "CHS";
    protected $CAMBIO_GESTORE               = "CHG";
    protected $PRIORITA                     = "PRI";
    protected $RECUPERI                     = "REC";
    protected $RECUPERI_MANUALE             = "RCM";
    protected $BORDERAUX                    = "BDX";
    protected $RISERVA                      = "RES";
    protected $DEFINITO                     = "DEF";
    protected $RIPASSATONP                  = "RNP";

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
            /* @var $_tipo \Ephp\CalendarBundle\Entity\TipoRepository */
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
                case $this->ALL_JWEB:
                    $tipo = $_tipo->createTipo($this->ALL_JWEB, 'Allegato da J-Web Claims', 'aa2222', $cal, true, false, true);
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
                    $tipo = $_tipo->createTipo($this->ATTIVITA_MANUALE, 'Attività manuali', 'ffaa31', $cal);
                    break;
                case $this->EMAIL_MANUALE:
                    $tipo = $_tipo->createTipo($this->EMAIL_MANUALE, 'Email', 'aaaa22', $cal, true, false, true);
                    break;
                case $this->RISCHEDULAZIONE:
                    $tipo = $_tipo->createTipo($this->RISCHEDULAZIONE, 'Rischedulazione', 'aaaaaa', $cal, true, false, false);
                    break;
                case $this->PRIORITA:
                    $tipo = $_tipo->createTipo($this->PRIORITA, 'Priorita', 'aaaaaa', $cal, false, false, false);
                    break;
                case $this->VERIFICA_PERIODICA:
                    $tipo = $_tipo->createTipo($this->VERIFICA_PERIODICA, 'Verifica periodica', '44aa44', $cal);
                    break;
                case $this->CAMBIO_STATO_OPERATIVO:
                    $tipo = $_tipo->createTipo($this->CAMBIO_STATO_OPERATIVO, 'Cambio Stato Operativo', '4444aa', $cal, true, false, false);
                    break;
                case $this->CAMBIO_GESTORE:
                    $tipo = $_tipo->createTipo($this->CAMBIO_GESTORE, 'Cambio Gestore', 'aaaaaa', $cal, false, false, false);
                    break;
                case $this->RECUPERI:
                    $tipo = $_tipo->createTipo($this->RECUPERI, 'Recuperi', '228822', $cal, false, false, true);
                    break;
                case $this->RECUPERI_MANUALE:
                    $tipo = $_tipo->createTipo($this->RECUPERI_MANUALE, 'Recuperi manuali', 'dd8831', $cal);
                    break;
                case $this->BORDERAUX:
                    $tipo = $_tipo->createTipo($this->BORDERAUX, 'Aggiornamenti da importazione Borderaux', '00b4ff', $cal, false, false, true);
                    break;
                case $this->RISERVA:
                    $tipo = $_tipo->createTipo($this->RISERVA, 'Messa in riserva', '00e000', $cal, false, false, false);
                    break;
                case $this->DEFINITO:
                    $tipo = $_tipo->createTipo($this->DEFINITO, 'Definito', '0000e0', $cal, false, false, false);
                    break;
                case $this->RIPASSATONP:
                    $tipo = $_tipo->createTipo($this->RIPASSATONP, 'Ripassato a N.P.', 'ff0000', $cal, false, false, false);
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
    protected function newEvento($sigla, \Claims\HBundle\Entity\Pratica $pratica, $titolo = null, $note = "") {
        $oggi = new \DateTime();
        $cal = $this->getCalendar();
        $tipo = $this->getTipoEvento($sigla);
        $evento = new \Claims\HBundle\Entity\Evento();
        $evento->setCalendario($cal)
                ->setCliente($this->getUser() ? $this->getUser()->getCliente() : $pratica->getCliente())
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

    protected function titoloRecuperi($name) {
        switch ($name) {
            case 'recupero_responsabile':
                return '1. Individuazione responsabile';
            case 'recupero_sollecito_asl':
                return '2. Email ASL (Sollecito 1910)';
            case 'recupero_copia_polizza':
                return '3. Recupero copia sinistro/copia polizza';
            case 'recupero_email_liquidatore':
                return '4. Scrivere/telefonare al liquidatore per chiedere compartecipazione';
            case 'recupero_quietanze':
                return '5. Chiedere percentuale espressamente e quietanze separate';
            case 'recupero_azione_di_recupero':
                return '6. Azione di recupero';
        }
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
