<?php

use Phalcon\Mvc\Model\Resultset\Simple as Resultset;

class Horario extends \Phalcon\Mvc\Model
{
    protected $id;
    protected $hora;
    protected $id_restaurante;

    public function getId()
    {
        return $this->id;
    }

    public function getHora()
    {
        return $this->hora . ' PM';
    }

    public function getIdRestaurante()
    {
        return $this->id_restaurante;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("reservaciones");
        $this->setSource("horario");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'horario';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Horario[]|Horario|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Horario|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public static function hoario12($restaurante)
    {
        $query = "CALL horarios_12($restaurante)";
       $reservar = new Mesas();

       return new Resultset(
           null,
           $reservar,
           $reservar->getReadConnection()->query($query)
       );
    }

}
