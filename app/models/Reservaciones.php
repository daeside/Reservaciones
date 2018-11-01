<?php

use Phalcon\Mvc\Model\Resultset\Simple as Resultset;

class Reservaciones extends \Phalcon\Mvc\Model
{
    protected $id;
    protected $folio;
    protected $cuarto;
    protected $nombre;
    protected $id_restaurante;
    protected $id_mesa;
    protected $id_horario;
    protected $fecha;
    protected $comentarios;
    protected $estatus;
    protected $fecha_reserva;
    protected $hora;
    protected $capacidad;
    protected $operador;
    protected $nombre_ope;
    protected $apellido;
    public $nombre_restaurante;
    protected $numero;

    public function setFolio($folio)
    {
        $this->folio = $folio;
    }

    public function setCuarto($cuarto)
    {
        $this->cuarto = $cuarto;
    }

    public function setNombre($nombre)
    {
        $this->nombre = strtoupper($nombre);
    }

    public function setIdRestaurante($id_restaurante)
    {
        $this->id_restaurante = $id_restaurante;
    }

    public function setIdMesa($id_mesa)
    {
        $this->id_mesa = substr($id_mesa, 1);
    }

    public function setIdHorario($id_horario)
    {
        $this->id_horario = $id_horario;
    }

    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }

    public function setComentarios($comentarios)
    {
        $this->comentarios = strtoupper($comentarios);
    }

    public function setEstatus($estatus)
    {
        $this->estatus = $estatus;
    }

    public function setFechaReserva($fecha_reserva)
    {
        $this->fecha_reserva = $fecha_reserva;
    }

    public function setOperador($operador)
    {
        $this->operador = $operador;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getFolio()
    {
        return $this->folio;
    }

    public function getCuarto()
    {
        return $this->cuarto;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getIdRestaurante()
    {
        return $this->id_restaurante;
    }

    public function getIdMesa()
    {
        return $this->id_mesa;
    }

    public function getIdHorario()
    {
        return $this->id_horario;
    }

    public function getHora()
    {
        return $this->hora . ' PM';
    }

    public function getCapacidad()
    {
        return $this->capacidad . ' PAX';
    }

    public function getFecha()
    {
        return $this->fecha;
    }

    public function getComentarios()
    {
        return $this->comentarios;
    }

    public function getEstatus()
    {
        return $this->estatus;
    }

    public function getFechaReserva()
    {
        return $this->fecha_reserva;
    }

    public function getOperador()
    {
        return $this->operador;
    }

    public function getNombreOpe()
    {
        return strtoupper($this->nombre_ope.' '.$this->apellido);
    }

    public function getNombreRestaurante()
    {
        return $this->$restaurante;
    }

    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("reservaciones");
        $this->setSource("reservaciones");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'reservaciones';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Reservaciones[]|Reservaciones|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Reservaciones|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public static function reportes($idres, $fecha, $tipo)
    {
       $query = "CALL reportes($idres, '$fecha', $tipo)";
       $reservar = new Reservaciones();

       return new Resultset(
           null,
           $reservar,
           $reservar->getReadConnection()->query($query)
       );
    }

    public static function buscar()
    {
       $query = "CALL busquedas()";
       $reservar = new Reservaciones();

       return new Resultset(
           null,
           $reservar,
           $reservar->getReadConnection()->query($query)
       );
    }

    public static function tickets($id)
    {
       $query = "CALL tickets($id)";
       $reservar = new Reservaciones();

     //  return $reservar->getReadConnection()->query($query)->fetch();

       return new Resultset(
        null,
        $reservar,
        $reservar->getReadConnection()->query($query)
       );
    }
}
