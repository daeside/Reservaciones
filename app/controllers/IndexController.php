<?php

use Phalcon\Http\Response;

class IndexController extends \Phalcon\Mvc\Controller
{
    public function initialize()
    {
        session_start();
    }

    public function indexAction()
    {

    }

    public function loginAction()
    {
        $response = new Response();
        
        if ($this->request->isPost()) 
        {
           if ($this->request->isAjax()) 
           {
              $usuario = $this->request->getPost('userReg');
              $pass = $this->request->getPost('passwordReg');
              $data = Usuarios::findFirstByNombre($usuario);

              if($data)
              {
                if ($this->security->checkHash($pass, $data->getPassword()) || $pass == $data->getPassword())
                {
                    $this->session->set('user', ucwords($data->getNombre()));
                    $this->session->set('id', $data->getId());
                    $this->session->set('privilegios', $data->getPrivilegios());
                    $response->setContent(1);
                }
                else
                {
                    $this->security->hash(rand());
                    $response->setContent('Contraseña incorrecta');
                }
              }
              else
              {
                  $response->setContent('No se encontro usuario');
              }
           }
        }

        return $response;
    }

    public function logoutAction()
    {
        $this->session->destroy();
        $response = new Response();
        $response->redirect('/Reservaciones');
        return $response;
    }
}