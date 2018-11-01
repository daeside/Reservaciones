<?php

use Phalcon\Http\Response;

class TicketController extends \Phalcon\Mvc\Controller
{
    public function initialize()
    {
        session_start();
    }

    public function indexAction($id)
    {
        if(!$this->session->has("user"))
        {
           $response = new Response();
           $response->redirect('/Reservaciones');
           return $response;
        }
        else
        {
            $ticket = Reservaciones::tickets($id);
            $this->view->ticket = $ticket;
        }
    }

    public function numeroAction()
    {
        $id = substr($this->request->getPost('iderepor'), 1);
        return $id;
    }
}