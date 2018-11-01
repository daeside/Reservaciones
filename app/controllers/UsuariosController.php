<?php

use Phalcon\Http\Response;

class UsuariosController extends \Phalcon\Mvc\Controller
{
    public function initialize()
    {
        session_start();
        $this->view->setTemplateBefore('menu');

        if($this->session->get("privilegios") == 1)
        {
            $this->view->setTemplateAfter('agregar');
        }
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

    public function changePassAction()
    {
        $response = new Response();
        
        if ($this->request->isPost()) 
        {
           if ($this->request->isAjax()) 
           {
              if (!empty($_POST["usrChangePassword"]) && isset($_POST["usrChangePasswordCon"]) && !empty($_POST["usrChangePasswordCon"]) && isset($_POST["usrChangePasswordCon"]))
              {
                $pass = $this->request->getPost('usrChangePassword');
                $passcon = $this->request->getPost('usrChangePasswordCon');

                if($pass == $passcon)
                {
                    $user = Usuarios::findFirstById($this->session->get("id"));
                    $user->setPassword($this->security->hash($pass));

                    if ($user->update() === true)
                    {
                        $response->setContent(1);
                    }
                    else
                    {
                       $response->setContent('Error al modificar contraseña');
                    }
                }
                else
                {
                    $response->setContent('Las contraseñas no coinciden');
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

    public function addUserAction()
    {
        $response = new Response();
        
        if ($this->request->isPost()) 
        {
           if ($this->request->isAjax()) 
           {
              if (!empty($_POST["addName"]) && isset($_POST["addName"]) && !empty($_POST["addApe"]) && isset($_POST["addApe"]))
              {
                $name = $this->request->getPost('addName');
                $ape = $this->request->getPost('addApe');
                $tipo = $this->request->getPost('tipo', null, 0);

                $user = new Usuarios();

                $user->setNombre($name);
                $user->setApellido($ape);
                $user->setPassword($this->security->hash('12345'));
                $user->setPrivilegios($tipo);

                if ($user->create() === true)
                {
                    $response->setContent(1);
                }
                else
                {
                    $response->setContent('Error al agregar usuario');
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
}

