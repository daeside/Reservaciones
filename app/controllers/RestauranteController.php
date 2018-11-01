<?php

use Phalcon\Http\Response;

class RestauranteController extends \Phalcon\Mvc\Controller
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

    public function openCloseAction()
    {
        $response = new Response();
        
        if ($this->request->isPost()) 
        {
           if ($this->request->isAjax()) 
           {
              if (!empty($_POST["resOpenClos"]) && isset($_POST["resOpenClos"]) && !empty($_POST["closFecha"]) && isset($_POST["closFecha"]) && !empty($_POST["openFecha"]) && isset($_POST["openFecha"]))
              {
                $rest = $this->request->getPost('resOpenClos');
                $operacion = $this->request->getPost('opnCls');
                $fechaCierre = $this->request->getPost('closFecha');
                $fechaApertura = $this->request->getPost('openFecha');

                $restaurante = Restaurante::abrirClosRes($operacion, $rest, $fechaCierre, $fechaApertura);

                if ($restaurante)
                {
                    $response->setContent(1);
                }
                else
                {
                    $response->setContent('Error al cambiar estatus');
                }
              }
              else
              {
                $response->setContent('Se requieren todos los campos');
              }
           }
        }

        return $response;
    }

    public function opcion($cierre, $apertura)
    {
        $dato = "";
        $dato = "<button class='waves-effect waves-light btn btn-small green lighten-2' onclick='reabrir(\"" . $cierre . "\", \"" . $apertura . "\")'><i class='material-icons'>lock_open</i></button>";
        
        return $dato;  
    }

    public function estatusAction($rest)
    {
        $this->view->disable();
        $restaurante = Restaurante::estatusRes($rest);
        $restaurantes = [];

        foreach ($restaurante as $key => $value) 
        {
            $obj = new stdClass();
            $obj->nombre = $value->getNombre();
            $obj->cierre = $value->getCierre();
            $obj->apertura = $value->getApertura();
            $obj->abrir = $this->opcion($value->getCierre(), $value->getApertura());

            array_push($restaurantes, $obj);
        }

        $datos = array('data' => $restaurantes);

        $response = new Response();
        $response->setContent(json_encode($datos));
        return $response;
    }

}

