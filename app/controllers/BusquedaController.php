<?php

use Phalcon\Http\Response;

class BusquedaController extends \Phalcon\Mvc\Controller
{
    public function initialize()
    {
        session_start();
        $this->view->setTemplateBefore('menu');
    }

    public function indexAction()
    {
        if(!$this->session->has("user"))
        {
           $response = new Response();
           $response->redirect('/Reservaciones');
           return $response;
        }
    }

    public function tickets($id)
    {
        $dato = "<button id=i" . $id . " class='waves-effect waves-light btn btn-small blue lighten-2' onclick=imprimir(this.id)><i class='material-icons'>print</i></button>";
        return $dato;  
    }

    public function opcion($id)
    {
        $dato = "";

        if($this->session->get("privilegios") != 0)
        {
            $dato = "<button id=c" . $id . " class='waves-effect waves-light btn btn-small red lighten-2' onclick=cancela(this.id)><i class='material-icons'>delete</i></button>";
        }
        
        return $dato;  
    }

    public function crearBusquedaAction()
    {
        $mesa = Reservaciones::buscar();
        $mesas = [];

       foreach ($mesa as $key => $value) 
        {
            $obj = new stdClass();
            $obj->reserva = $value->getId();
            $obj->restaurante = $value->nombre_restaurante;
            $obj->hora = $value->getHora();
            $obj->fecha = $value->getFecha();
            $obj->id = $value->getNumero();
            $obj->capacidad = $value->getCapacidad();
            $obj->cuarto = $value->getCuarto();
            $obj->nombre = $value->getNombre();
            $obj->notas = $value->getComentarios();
            $obj->imprimir = $this->tickets($value->getId());
            $obj->cancelar = $this->opcion($value->getId());

            array_push($mesas, $obj);
        }

        $datos = array('data' => $mesas);

        $response = new Response();
        $response->setContent(json_encode($datos));
        return $response;
    }
}