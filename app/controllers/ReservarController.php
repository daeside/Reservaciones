<?php

date_default_timezone_set("America/Cancun");
use Phalcon\Http\Response;

class ReservarController extends \Phalcon\Mvc\Controller
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

       if($this->session->get("privilegios") == 0)
       {
          $response = new Response();
          $response->redirect('/Reservaciones/busqueda');
          return $response;
       }
    }

    public function selectAjaxAction()
    {
        $restaurante = Restaurante::find();
        $restaurantes = [];

       foreach ($restaurante as $key => $value) 
        {
            $obj = new stdClass();
            $obj->id = $value->getId();
            $obj->nombre = $value->getNombre();

            array_push($restaurantes, $obj);
        }

        $response = new Response();
        $response->setContent(json_encode($restaurantes));
        return $response;
    }

    public function selectFechAction($fecha)
    {
        $restaurante = Restaurante::closeRes($fecha);
        $restaurantes = [];

       foreach ($restaurante as $key => $value) 
        {
            $obj = new stdClass();
            $obj->id = $value->getId();
            $obj->nombre = $value->getNombre();

            array_push($restaurantes, $obj);
        }

        $response = new Response();
        $response->setContent(json_encode($restaurantes));
        return $response;
    }

    public function horaSelectAction($tipo)
    {
        $horario = Horario::hoario12($tipo);
        $horarios = [];

       foreach ($horario as $key => $value) 
        {
            $obj = new stdClass();
            $obj->id = $value->getId();
            $obj->hora = $value->getHora();

            array_push($horarios, $obj);
        }

        $response = new Response();
        $response->setContent(json_encode($horarios));
        return $response;
    }

    public function estatus($status)
    {
        $dato = "<p style='color:red;'>RESERVADA</p>";

        if($status == 1)
        {
            $dato = "<p style='color:green;'>DISPONIBLE</p>";
        }
        return $dato;
    }

    public function opcion($id)
    {
        $dato = "<button id=r" . $id . " class='waves-effect waves-light btn btn-small green lighten-2' onclick=reserva(this.id)><i class='material-icons'>save</i></button>";
        return $dato;  
    }

    public function mesasDisp($idres, $fecha, $idhor)
    {
        $mesa = Mesas::disponibles($idres, $idhor, $fecha);
        $mesas = [];

       foreach ($mesa as $key => $value) 
        {
            $obj = new stdClass();
            $obj->id = $value->getNumero();
            $obj->capacidad = $value->getCapacidad();
            $obj->estatus = $this->estatus(1);
            $obj->cuarto = '';
            $obj->nombre = '';
            $obj->notas = '';
            $obj->fecha = '';
            $obj->hora = '';
            $obj->opcion = $this->opcion($value->getId());

            array_push($mesas, $obj);
        }
        return $mesas;
    }

    public function mesasOcup($idres, $fecha, $idhor)
    {
        $mesa = Mesas::ocupadas($idres, $fecha, $idhor);
        $mesas = [];

       foreach ($mesa as $key => $value) 
        {
            $obj = new stdClass();
            $obj->id = $value->getNumero();
            $obj->capacidad = $value->getCapacidad();
            $obj->estatus = $this->estatus(0);
            $obj->cuarto = $value->getCuarto();
            $obj->nombre = $value->getNombre();
            $obj->notas = $value->getNotas();
            $obj->fecha = $value->getFecha();
            $obj->hora = $value->getHora();
            $obj->opcion = "";

            array_push($mesas, $obj);
        }
        return $mesas;
    }

    public function crearTabAction($idres, $fecha, $idhor)
    {
        $response = new Response();
        $disponibles = $this->mesasDisp($idres, $fecha, $idhor);
        $ocupadas = $this->mesasOcup($idres, $fecha, $idhor);

        if(count($disponibles) == 0)
        {
            $todas = json_encode($ocupadas);
        }
        elseif(count($ocupadas) == 0)
        {
            $todas = json_encode($disponibles);
        }
        else
        {
            $todas = substr(json_encode($disponibles), 0, -1) . ',' . substr(json_encode($ocupadas), 1);
        }

        $datos = array('data' => json_decode($todas));
        $response->setContent(json_encode($datos));
        return $response;
    }

    public function crearAction()
    {
        $response = new Response(); 
        
        if ($this->request->isPost()) 
        {
           if ($this->request->isAjax()) 
           {
              if (!empty($_POST["resNombre"]) && isset($_POST["resNombre"]) && !empty($_POST["resList"]) && isset($_POST["resList"]) && !empty($_POST["resFecha"]) && isset($_POST["resFecha"]) && !empty($_POST["horaList"]) && isset($_POST["horaList"]) && !empty($_POST["resNotas"]) && isset($_POST["resNotas"]))
              {
                 $reservar = new Reservaciones();
                 $reservar->setFolio($this->request->getPost('resFolio'));
                 $reservar->setCuarto($this->request->getPost('resCuarto'));
                 $reservar->setNombre($this->request->getPost('resNombre'));
                 $reservar->setIdRestaurante($this->request->getPost('resList'));
                 $reservar->setIdMesa($this->request->getPost('id-mesa'));
                 $reservar->setIdHorario($this->request->getPost('horaList'));
                 $reservar->setFecha($this->request->getPost('resFecha'));
                 $reservar->setComentarios($this->request->getPost('resNotas'));
                 $reservar->setEstatus(1);
                 $reservar->setFechaReserva(date('Y-m-d'));
                 $reservar->setOperador($this->session->get("id"));

                 if($reservar->create() === true)
                 {
                    $response->setContent(1);
                 }
                 else
                 {
                    $response->setContent('Error al generar reserva');
                 }
              }
              else
              {
                $response->setContent('Faltan campos por llenar');
              }
           }
        }
        return $response;
    }

    public function cancelarAction()
    {
        $response = new Response();
        
        if ($this->request->isPost()) 
        {
           if ($this->request->isAjax()) 
           {
              $id = substr($this->request->getPost('id'), 1);
              $cancelar = Reservaciones::findFirstById($id);
              $cancelar->setEstatus(0);

              if ($cancelar->update() === true)
              {
                $response->setContent(1);
              }
              else
              {
                $response->setContent('Error al cancelar reserva');
              }
           }
        }
        return $response;
    }
}