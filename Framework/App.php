<?php

namespace Framework;

use Framework\Database\SimpleDatabase;
use Framework\Routers\DefaultRouter;
use Framework\Routers\IRouter;
use Framework\Sessions\ISession;
use Framework\Sessions\DefaultSession;

require_once 'Autoloader.php';

class App
{
    private static $_instance = null;

    /**
     * @var Config
     */
    private $_config = null;
    private $_frontController = null;
    private $_router = null;
    private $_dbConnections = [];

    /**
     * @var ISession
     */
    private $_session = null;

    private function __construct()
    {
        set_exception_handler([ $this, '_exceptionHandler' ]);
        Autoloader::registerNamespace('Framework', dirname(__FILE__) . DIRECTORY_SEPARATOR);
        Autoloader::registerAutoLoad();
        $this->_config = Config::getInstance();
    }

    public function getConfigFolder()
    {
        return $this->_config->getConfigFolder();
    }

    public function setConfigFolder($path)
    {
        $this->_config->setConfigFolder($path);
    }

    public function getRouter()
    {
        return $this->_router;
    }

    function setRouter($router)
    {
        $this->_router = $router;
    }

    /**
     * @return App
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new App();
        }

        return self::$_instance;
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->_config;
    }

    public function getDbConnection($connection = 'default')
    {
        if (!$connection) {
            throw new \Exception('No connection string provided!', 500);
        }

        if (isset($this->_dbConnections[$connection])) {
            return $this->_dbConnections[$connection];
        }

        $dbConfig = $this->getConfig()->database;

        if (!$dbConfig[$connection]) {
            throw new \Exception('No valid connection string found in the configuration file!', 500);
        }

        $database = new \PDO(
            $dbConfig[$connection]['connection_url'],
            $dbConfig[$connection]['username'],
            $dbConfig[$connection]['password'],
            $dbConfig[$connection]['pdo_options']
        );

        $this->_dbConnections[$connection] = $database;

        return $database;
    }

    /**
     * @return ISession
     */
    public function getSession(){
        return $this->_session;
    }

    public function setSession(ISession $session){
        $this->_session = $session;
    }

    public function run()
    {
        if ($this->_config->getConfigFolder() == null) {
            $this->setConfigFolder('ShoppingCart/config');
        }

        $this->_frontController = FrontController::getInstance();

        if ($this->_router instanceof IRouter) {
            $this->_frontController->setRouter($this->_router);
        }

        if ($this->_router == null) {
            $this->_frontController->setRouter(new DefaultRouter());
        }

        if ($this->_session == null) {
            $sessionInfo = $this->_config->app['session'];

            if ($sessionInfo['auto_start']) {
                if ($sessionInfo['type'] == 'native') {
                    $this->_session = new DefaultSession(
                        $sessionInfo['name'],
                        $sessionInfo['lifetime'],
                        $sessionInfo['path'],
                        $sessionInfo['domain'],
                        $sessionInfo['secure']
                    );
                }
            }
        }

        $this->_frontController->dispatch();
    }

    public function _exceptionHandler($ex)
    {
        if ($this->_config && $this->_config->app['displayExceptions'] == true) {
            echo '<pre>' . print_r($ex, true) . '</pre>';
        } else {
            $this->displayError($ex->getCode(), $ex->getMessage());
        }
    }

    public function displayError($error, $message)
    {
        echo '<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">';
        echo '<div class="text-center">';

        $confError = isset($this->_config->errors[$error]) && $this->_config->errors[$error] != null;

        FormViewHelper::init()->initLink()->setValue('Go back')->setAttribute('href', $this->_config->app['default_path'])->create()->render();
        echo '<h1>Oooops, something went wrong . Error  ' . $error . '</h1>';

        if ($confError) {
            if ($confError == 'message') {
                echo '<h1>' . $message . '</h1>';
            } else {
                echo "<h1> $confError </h1>";
            }
        }

        echo '<img class="decoded shrinkToFit" src="http://www.decision-making-solutions.com/images/learning-from-mistakes-error-sign-iStock_000014363681XSmall.jpg" />';

        echo '</div>';
        exit;
    }

    public function __destruct()
    {
        if ($this->_session != null) {
            $this->_session->saveSession();
        }
    }

    public function getUsername()
    {
        return $this->_session->escapedUsername;
    }

    public function isLogged()
    {
        return $this->_session->escapedUsername !== null;
    }

    public function isAdmin()
    {
        return SimpleDatabase::isAdmin();
    }

    public function isEditor()
    {
        return SimpleDatabase::hasRole('editor');
    }

    public function isModerator()
    {
        return SimpleDatabase::hasRole('moderator');
    }
}