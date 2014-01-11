<?php

namespace Claims\HBundle\Entity\Traits;

trait Pratica {

    public function tpl() {
        return array(
            781 => "781 - TPL lesioni senza negligenza medica",
            787 => "787 - TPL negligenza medica coinvolta",
            780 => "780 - Danni alla proprietà",
            786 => "786 - Responsabilità datore di lavoro",
        );
    }

    public function sa() {
        return array(
            0 => '0 - Altri non classificati',
            101 => '101 - Cure infermieristiche',
            201 => '201 - Dermatologia',
            202 => '202 - Patologia',
            203 => '203 - Pediatria',
            204 => '204 - Psichiatria',
            205 => '205 - Riabilitazione chimica o fisica',
            301 => '301 - Anestesia',
            302 => '302 - Disturbi cardiovascolari o polmonari',
            303 => '303 - Endocrinologia',
            304 => '304 - Gastroenterologia',
            305 => '305 - Cure medico-generico',
            306 => '306 - Ematologia',
            307 => '307 - Medicina nucleare',
            308 => '308 - Oncologia',
            309 => '309 - Oculistica',
            310 => '310 - Otorinolaringoiatria',
            311 => '311 - Terapia, radiazioni, trauma',
            401 => '401 - Chirurgia del colon e del retto',
            402 => '402 - Chirurgia generale (altri non classificati)',
            403 => '403 - Cure intensive e critiche',
            404 => '404 - Chirurgia laringoiatrica',
            405 => '405 - Chirurgia urologica',
            501 => '501 - Indicenti ed emergenza',
            502 => '502 - Ostetricia',
            503 => '503 - Chirurgia neurologica',
            504 => '504 - Ostetricia e ginecologia',
            505 => '505 - Chirurgia ortopedica',
            506 => '506 - Chirurgia pediatrica',
            507 => '507 - Chirurgia estetica',
            508 => '508 - Chirurgia traumatologica',
        );
    }

    public function severityOfInjury() {
        return array(
            "9" => "9 - Solo emotivo - Spavento, nessun danno fisico.",
            "8" => "8 - Temporanei: Lieve - Lacerazioni, contusioni, ferite minori, eruzioni cutanee. Nessun ritardo.",
            "7" => "7 - Temporanei: Minore - Infezioni, frattura omessa, caduta in ospedale. Guarigione ritardata.",
            "6" => "6 - Temporanei: Maggiore - Ustioni, materiale chirurgico dimenticato, effetto da tossicodipendenza, danni al cervello. Guarigio",
            "5" => "5 - Permanente: Minori - Perdita di dita, perdita o danneggiamento di organi. Non comprende gli infortuni invalidanti.",
            "4" => "4 - Permanente: Significativo - Sordità, perdita di arti, perdita della vista, perdita di un rene o di un polmone.",
            "3" => "3 - Permanente: Maggiore - Paraplegia, cecità, perdita di due arti, danni al cervello.",
            "2" => "2 - Permanente: Grave - Quadraplegia, gravi danni al cervello, inabilità totale.",
            "1" => "1 - Permanente: Morte - ",
            "B" => "B - Baby Case - Neonato con 50% e oltre di disabilità.",
            "B1" => "B1 - Baby Case - Decesso.",
            "B2" => "B2 - Baby Case - Neonato con distocia alle spalle e/o danni minori a seguito parto.",
        );
    }

    public function fasceMpl() {
        return array(
            "A" => "A - da 800.000 in poi",
            "B" => "B - da 400.000 a 800.000",
            "C" => "C - da 150.000 a 400.000",
            "D" => "D - da 40.000 a 150.000",
            "E" => "E - da 10.000 a 40.000",
            "F" => "F - da 0 a 10.000",
        );
    }

    public function allegati() {
        return array(
            1 => "Provvedimenti opportuni non presi",
            2 => "Ritardo nella prestazione",
            3 => "Prestazioni improprie od errori",
            4 => "Procedura inutile o controindicata",
            5 => "Comunicazione o vigilanza",
            6 => "Continuità delle cure o gestione delle cure",
            7 => "Comportamento o Legale",
            8 => "Altri non classificati",
        );
    }

}
