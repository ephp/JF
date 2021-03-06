<?php

namespace JF\AndreaniBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/andreani")
 */
class DefaultController extends Controller {

    private $servizi = array(
            'rivalutazioni_istat' => array(
                'label' => 'Rivalutazioni Istat',
                'funzioni' => array(
                    array('slug' => 'interessi_rivalutazione', 'label' => 'Calcolo Rivalutazione Monetaria e Interessi Legali'),
                    array('slug' => 'rivalutazione_mensile_assegni_importi_dovuti', 'label' => 'Rivalutazione Mensile Importi Dovuti e Non Pagati'),
                    array('slug' => 'calcolo_adeguamento_istat_canone_locazione', 'label' => 'Adeguamento Istat Canone di Locazione'),
                    array('slug' => 'calcolo_devalutazione_monetaria', 'label' => 'Calcolo Devalutazione Monetaria'),
                    array('slug' => 'rivalutazione-monetaria-storica', 'label' => 'Rivalutazione Monetaria Storica'),
                ),
            ),
            'tassi_interessi' => array(
                'label' => 'Tassi e Interessi',
                'funzioni' => array(
                    array('slug' => 'interessi_legali', 'label' => 'Calcolo Interessi Legali'),
                    array('slug' => 'interessi_moratori', 'label' => 'Calcolo Interessi di Mora'),
                    array('slug' => 'calcolo_interessi_mora_appalti_opere_pubbliche', 'label' => 'Calcolo Interessi Moratori Per Appalti Pubblici'),
                    array('slug' => 'interessi_tasso_fisso', 'label' => 'Calcolo Interessi a Tasso Fisso'),
                    array('slug' => 'calcolo-maggior-danno-obbligazioni-pecuniarie', 'label' => 'Calcolo Maggior Danno'),
                    array('slug' => 'calcolo-ammortamento-mutuo', 'label' => 'Calcolo Piano di Ammortamento'),
                    array('slug' => 'calcolo_tasso_usura', 'label' => 'Calcolo Tasso di Usura'),
                ),
            ),
            'atti_giudiziari' => array(
                'label' => 'Atti Giudiziari',
                'funzioni' => array(
                    array('slug' => 'calcolo_scadenze_termini_udienze', 'label' => 'Calcolo Scadenze e Termini Processuali'),
                    array('slug' => 'calcolo-termini-processuali-civili', 'label' => 'Calcolo Termini Processuali Civili'),
                    array('slug' => 'calcolo-termini-memorie-183-comparse-190', 'label' => 'Calcolo Termini Memorie 183, Comparse e Repliche 190'),
                    array('slug' => 'calcolo_contributo_unificato', 'label' => 'Calcolo Contributo Unificato'),
                    array('slug' => 'calcolo_diritti_copia_cancelleria', 'label' => 'Calcolo Diritti di Copia Nel Processo Civile'),
                    array('slug' => 'calcolo_diritti_copia_processo_tributario', 'label' => 'Calcolo Diritti di Copia Nel Processo Tributario'),
                    array('slug' => 'ricerca-codici-iscrizione-ruolo-cause', 'label' => 'Ricerca Codici Iscrizione a Ruolo'),
                ),
            ),
            'fatturazione' => array(
                'label' => 'Fatturazione',
                'funzioni' => array(
                    array('slug' => 'modelli-notula-decreto-ingiuntivo-precetto-esecuzioni', 'label' => 'Calcolo Nota Spese Con Modelli Predefiniti'),
                    array('slug' => 'calcolo_parcella_penale_avvocati_tariffario_forense', 'label' => 'Calcolo Notula Penale'),
                    array('slug' => 'calcolatrice_scorporo', 'label' => 'Scorporo Importi da Fatturare'),
                    array('slug' => 'calcolo_fattura_studio_legale', 'label' => 'Calcolo Fattura Per Studi Legali'),
                    array('slug' => 'calcolo-compenso-avvocati-parametri-ministeriali-civili', 'label' => 'Liquidazione Compenso Avvocati - Procedimenti Civili'),
                    array('slug' => 'calcolo-compenso-avvocati-parametri-ministeriali-penali', 'label' => 'Liquidazione Compenso Avvocati - Procedimenti Penali'),
                    array('slug' => 'calcolo-costi-e-tariffe-mediazione-civile', 'label' => 'Calcolo Tariffe Mediazione Civile'),
                    array('slug' => 'calcolo-compenso-onorario-ctu-liquidazione-tariffe', 'label' => 'Calcolo Onorari Ctu'),
                    array('slug' => 'calcolo-compenso-curatore-fallimentare', 'label' => 'Calcolo Compenso Curatore Fallimentare'),
                    array('slug' => 'calcolo_fattura_agente_enasarco', 'label' => 'Calcolo Fattura Agente Enasarco'),
                    array('slug' => 'calcolo-somma-ore-minuti-compensi-a-tempo', 'label' => 'Calcolo Tempo e Compenso Orario'),
                    array('slug' => 'calcolo_fattura_generica_scorporo', 'label' => 'Calcolo Fattura Generica Con Scorporo'),
                    array('slug' => 'calcolatrice-online', 'label' => 'Calcolatrice Web Online'),
                ),
            ),
            'risarcimento_danni' => array(
                'label' => 'Risarcimento Danni',
                'funzioni' => array(
                    array('slug' => 'calcolo_danno_biologico_r2', 'label' => 'Calcolo Danno Biologico'),
                    array('slug' => 'calcolo_danno_non_patrimoniale', 'label' => 'Calcolo Danno Non Patrimoniale - Tabelle Roma e Milano'),
                    array('slug' => 'calcolo_danno_non_patrimoniale_milano', 'label' => 'Calcolo Danno Non Patrimoniale - Tribunale di Milano'),
                    array('slug' => 'calcolo_danno_non_patrimoniale_roma', 'label' => 'Calcolo Danno Non Patrimoniale - Tribunale di Roma'),
                    array('slug' => 'calcolo-risarcimento-danno-perdita-parentale', 'label' => 'Calcolo Danno da Perdita Parentale'),
                    array('slug' => 'calcolo-equo-indennizzo-causa-servizio', 'label' => 'Calcolo Equo Indennizzo'),
                ),
            ),
            'proprieta_successione' => array(
                'label' => 'Proprietà e Successioni',
                'funzioni' => array(
                    array('slug' => 'calcolo_quote_ereditarie', 'label' => 'Calcolo Quote Ereditarie'),
                    array('slug' => 'calcolo_usufrutto_nuda_proprieta', 'label' => 'Calcolo Usufrutto e Nuda Proprietà'),
                    array('slug' => 'calcolo-pensione-reversibilita-inps', 'label' => 'Calcolo Pensione Reversibilità Inps'),
                    array('slug' => 'calcolo-imposta-registro-contratto-locazione', 'label' => 'Calcolo Imposta Registro Locazioni'),
                    array('slug' => 'calcolo-imposte-compravendita-immobiliare', 'label' => 'Calcolo Imposte Compravendita Immobiliare'),
                    array('slug' => 'calcolo-convenienza-cedolare-secca-affitti', 'label' => 'Calcolo Convenienza Cedolare Secca'),
                    array('slug' => 'calcolo-imu-nuova-ici-r3', 'label' => 'Calcolo Imu'),
                    array('slug' => 'calcolo-valore-catastale-immobili-asse-ereditario', 'label' => 'Calcolo Valore Catastale Immobili'),
                ),
            ),
            'investimenti_finanziari' => array(
                'label' => 'Investimenti Finanziari',
                'funzioni' => array(
                    array('slug' => 'calcolo-rendimento-bot', 'label' => 'Calcolo Rendimento Bot'),
                    array('slug' => 'calcolo-rendimento-pronti-contro-termine', 'label' => 'Calcolo Rendimento Pronti Contro Termine'),
                ),
            ),
            'dichiarazione_redditi' => array(
                'label' => 'Dichiarazione dei Redditi',
                'funzioni' => array(
                    array('slug' => 'calcolo-irpef', 'label' => 'Calcolo Irpef'),
                    array('slug' => 'calcolo-detrazione-figli-a-carico', 'label' => 'Calcolo Detrazione Figli a Carico'),
                    array('slug' => 'calcolo-detrazione-coniuge-a-carico', 'label' => 'Calcolo Detrazione Coniuge a Carico'),
                    array('slug' => 'calcolo-detrazione-altri-familiari-a-carico', 'label' => 'Calcolo Detrazione Altri Familiari a Carico'),
                    array('slug' => 'calcolo-detrazione-redditi-lavoro-dipendente', 'label' => 'Calcolo Detrazione Per Redditi da Lavoro Dipendente'),
                    array('slug' => 'calcolo-acconto-irpef', 'label' => 'Calcolo Acconto Irpef'),
                    array('slug' => 'calcolo-rateizzazione-imposte-irpef', 'label' => 'Calcolo Rateizzazione Imposte'),
                    array('slug' => 'calcolo-acconto-cedolare-secca', 'label' => 'Calcolo Acconto Cedolare Secca'),
                ),
            ),
            'penale' => array(
                'label' => 'Penale',
                'funzioni' => array(
                    array('slug' => 'calcolo-aumenti-riduzioni-pena', 'label' => 'Calcolo Aumenti / Riduzioni Pena'),
                    array('slug' => 'calcolo-conversione-pena-detentiva-pecuniaria', 'label' => 'Calcolo Conversione Pena'),
                    array('slug' => 'calcolo-prescrizione-reati', 'label' => 'Calcolo Prescrizione Reati'),
                ),
            ),
            'utilita_varie' => array(
                'label' => 'Utilità Varie',
                'funzioni' => array(
                    array('slug' => 'calcolo-prescrizione-diritti', 'label' => 'Calcolo Prescrizione Diritti'),
                    array('slug' => 'calcolo_differenza_date', 'label' => 'Calcolo Differenza Tra Date'),
                    array('slug' => 'calcolo-giorni-lavorativi-festivi', 'label' => 'Calcolo Giorni Lavorativi'),
                    array('slug' => 'scorporo-iva', 'label' => 'Scorporo Iva e Operazioni Correlate'),
                    array('slug' => 'calcoli-percentuali-frequenti', 'label' => 'Calcoli Percentuali Frequenti'),
                    array('slug' => 'decodifica_codice_fiscale', 'label' => 'Decodifica Codice Fiscale'),
                    array('slug' => 'calcolo-eta-anagrafica', 'label' => 'Calcolo Età Anagrafica'),
                    array('slug' => 'calcolatore-frazioni', 'label' => 'Calcolatore Per Frazioni'),
                    array('slug' => 'conversione-unita-di-misura', 'label' => 'Conversione Unità di Misura'),
                    array('slug' => 'cronometro-online', 'label' => 'Cronometro Online'),
                ),
            ),
    );

    /**
     * @Route("/", name="andreani", options={"unlock": true})
     * @Route("-utility/", name="studio_andreani", options={"unlock": true})
     * @Template()
     */
    public function indexAction() {
        return array('gruppi' => $this->servizi);
    }

    /**
     * @Route("/{slug}", name="andreani_funzione", options={"unlock": true, "expose": true})
     * @Template()
     */
    public function funzioneAction($slug) {
        foreach($this->servizi as $gruppo) {
            foreach($gruppo['funzioni'] as $funzione) {
                if($funzione['slug'] == $slug) {
                    return array('funzione' => $funzione);
                }
            }
        }
    }

}
