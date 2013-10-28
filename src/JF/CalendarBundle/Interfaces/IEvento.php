<?php

namespace JF\CalendarBundle\Interfaces;

interface IEvento {

    public function setCalendario($calendario);

    public function getCalendario();

    public function setTipo($tipo);

    public function getTipo();

    public function setDataOra($dataOra);

    public function getDataOra();

    public function setTitolo($titolo);

    public function getTitolo();

    public function setNote($note);

    public function getNote();

    public function setGiornoIntero($giornoIntero);

    public function getGiornoIntero();

    public function setImportante($importante);

    public function getImportante();

    public function setCliente($cliente);

    public function getCliente();

    public function setGestore($gestore);

    public function getGestore();
    
}

?>
