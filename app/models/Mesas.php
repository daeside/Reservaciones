<?php

use Phalcon\Mvc\Model\Resultset\Simple as Resultset;

class Mesas extends \Phalcon\Mvc\Model
{
    protected $id;
    protected $id_reserva;
    protected $id_restaurante;
    protected $capacidad;
    protected $estatus;
    protected $cuarto;
    protected $nombre;
    protected $fecha;
    protected $hora;
    protected $numero;
    protected $comentarios;

    public function getId()
    {
        return $this->id;
    }

    public function getIdReserva()
    {
        return $this->id_reserva;
    }

    public function getNombre()
    {
        return strtoupper($this->nombre);
    }

    public function getCuarto()
    {
        return $this->cuarto;
    }

    public function getFecha()
    {
        return $this->fecha;
    }

    public function getHora()
    {
        return $this->hora . ' PM';
    }

    public function getIdRestaurante()
    {
        return $this->id_restaurante;
    }

    public function getCapacidad()
    {
        return $this->capacidad . ' PAX';
    }

    public function getEstatus()
    {
        return $this->estatus;
    }

    public function getNumero()
    {
        return $this->numero;
    }

    public function getNotas()
    {
        return $this->comentarios;
    }

    public function setEstatus($estatus)
    {
        $this->estatus =$estatus;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("reservaciones");
        $this->setSource("mesas");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'mesas';
    }
    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Mesas[]|Mesas|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Mesas|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public static function disponibles($idres, $idhor, $fecha)
    {
        $query = "CALL mesas_disponibles($idres, $idhor, '$fecha')";
       $reservar = new Mesas();

       return new Resultset(
           null,
           $reservar,
           $reservar->getReadConnection()->query($query)
       );
    }

    public static function ocupadas($idres, $fecha, $idhora)
    {
        $query = "CALL mesas_ocupadas($idres, '$fecha', $idhora)";
       $reservar = new Mesas();

       return new Resultset(
           null,
           $reservar,
           $reservar->getReadConnection()->query($query)
       );
    }
}
