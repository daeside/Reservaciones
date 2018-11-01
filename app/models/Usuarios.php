<?php

class Usuarios extends \Phalcon\Mvc\Model
{
    protected $id;
    protected $nombre;
    protected $apellido;
    protected $password;
    protected $privilegios;

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function setApellido($apellido)
    {
        $this->apellido = $apellido;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setPrivilegios($privilegios)
    {
        $this->privilegios = $privilegios;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNombre()
    {
        return strtoupper($this->nombre);
    }

    public function getApellido()
    {
        return strtoupper($this->apellido);
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getPrivilegios()
    {
        return $this->privilegios;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("reservaciones");
        $this->setSource("usuarios");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'usuarios';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Usuarios[]|Usuarios|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Usuarios|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
}
