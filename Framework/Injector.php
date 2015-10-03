<?php

namespace Framework;

class Injector
{
    private static $_instance;
    private static $_config;

    private function __construct()
    {
        self::$_config = App::getInstance()->getConfig()->inject;
    }

    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new Injector();
        }

        return self::$_instance;
    }

    /**
     * @param String $config
     */
    public function setConfig($config)
    {
        if (is_string($config)) {
            self::$_config = App::getInstance()->getConfig()->$config;
        } else {
            throw new \Exception('Invalid configuration, string name is expected!', 500);
        }
    }
    public function loadDependencies($class)
    {
        $reflectionClass = new \ReflectionClass($class);
        $properties = $reflectionClass->getProperties();
        $regex = '/@(?:i|I)nject\s*(.+)/';

        foreach ($properties as $property) {
            $doc = $property->getDocComment();
            preg_match($regex, $doc, $matches);

            if (isset($matches[1]) && $matches[1] != null) {
                $name = trim($matches[1]);
                $dependency = self::$_config[$name];

                if ($dependency !== null) {
                    $property = $reflectionClass->getProperty($property->getName());
                    $property->setAccessible(true);
                    $property->setValue($class, new $dependency);

                    $dep = new $dependency;
                    $this->loadDependencies($dep);
                    $property->setValue($class, $dep);
                } else {
                    throw new \Exception("No path found for '$name', check your configuration! The default one is 'inject'.", 500);
                }
            }
        }
    }
}