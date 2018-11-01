<?php

use Phalcon\Mvc\Model\Resultset\Simple as Resultset;

class Restaurante extends \Phalcon\Mvc\Model
{

    protected $id;
    protected $nombre;
    protected $estatus;
    protected $cierre;
    protected $apertura;

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function setEstatus($estatus)
    {
        $this->estatus = $estatus;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getCierre()
    {
        return $this->cierre;
    }

    public function getApertura()
    {
        return $this->apertura;
    }

    public function getEstatus()
    {
        return $this->estatus;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("reservaciones");
        $this->setSource("restaurante");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'restaurante';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Restaurante[]|Restaurante|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Restaurante|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public static function closeRes($fecha)
    {
       $query = "CALL restaurantes_cerrados('$fecha')";
       $reservar = new Restaurante();

      return new Resultset(
        null,
        $reservar,
        $reservar->getReadConnection()->query($query)
       );
    }

    public static function abrirClosRes($tipo, $idres, $cierre, $apertura)
    {
       $query = "CALL cerrar_abrir_rest('$tipo', $idres, '$cierre',  '$apertura')";
       $reservar = new Restaurante();

       return new Resultset(
        null,
        $reservar,
        $reservar->getReadConnection()->execute($query)
       );
    }

    public static function estatusRes($idres)
    {
       $query = "CALL estatus_rest($idres)";
       $reservar = new Restaurante();

       return new Resultset(
        null,
        $reservar,
        $reservar->getReadConnection()->query($query)
       );
    }

}
