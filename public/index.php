<?php

use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Url as UrlProvider;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;

// Definimos algunas rutas constantes para localizar recursos
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('LIB_PATH', APP_PATH . '/library');

// Registramos un autoloader
$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . '/controllers/',
        APP_PATH . '/models/',
        LIB_PATH . '/Excel/',
    ]
);

$loader->registerFiles(
    [
       'PHPExcel.php',
    ]
);

$loader->register();

// Crear un DI
$di = new FactoryDefault();

// Configurar el componente vista
$di->set(
    'view',
    function () 
    {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);

// Configurar la base de datos
$di->set(
    'db',
    function () {
        return new DbAdapter(
            [
                'host'     => '127.0.0.1',
                'username' => 'root',
                'password' => '',
                'dbname'   => 'reservaciones',
                'charset' => 'utf8',
                'options'  => array(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true)
            ]
        );
    }
);

// Configurar una URI base para generar todas las URIs incluyendo la carpeta "prueba"
$di->set(
    'url',
    function () 
    {
        $url = new UrlProvider();
        $url->setBaseUri('');
        return $url;
    }
);

$application = new Application($di);

try 
{
    // Gestionar la consulta
    $response = $application->handle();
    $response->send();
} 
catch (Exception $e) 
{
    echo 'Excepción: ', $e->getMessage();
}