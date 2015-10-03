<?php

namespace Controllers;

use Framework\App;
use Framework\BaseController;
use Models\ViewModels\HelpViewModel\IndexViewModel;

class HelpController extends BaseController
{
    public function index()
    {
        $foundRoutes = $this->findAllRoutesInApp();
        $this->view->display(new IndexViewModel($foundRoutes));
    }

    /**
     * @Get
     * @Route("help/jsonRoutes")
     * @return array
     */
    public function jsonRoutes()
    {
        $foundRoutes = $this->findAllRoutesInApp();
        echo json_encode($foundRoutes);
    }

    /**
     * @Get
     * @Route("help/ajax")
     */
    public function ajax()
    {
        $this->view->appendToLayout('header', 'header');
        $this->view->appendToLayout('meta', 'meta');
        $this->view->appendToLayout('body', 'HelpController.ajax');
        $this->view->appendToLayout('footer', 'footer');
        $this->view->displayLayout('Layouts.ajax');
    }

    private function findBindingModels($doc)
    {
        $params = [];

        if (preg_match('/@param\s+\\\?([\s\S]+BindingModel)\s+\$/', $doc, $match)) {
            $bindingModelName = $match[1];
            $bindingModelsNamespace = App::getInstance()->getConfig()->app['namespaces']['Models'] . 'BindingModels/';
            $bindingModelsNamespace = str_replace('../', '', $bindingModelsNamespace);
            $bindingModelPath = str_replace('/', '\\', $bindingModelsNamespace . $bindingModelName);
            $bindingModelPath = substr($bindingModelPath, 13);
            $bindingReflection = new \ReflectionClass($bindingModelPath);
            $properties = $bindingReflection->getProperties();

            foreach ($properties as $property) {
                $name = $property->getName();
                $params[$name] = $name;
            }

            return $params;
        }

        return $params;
    }

    /**
     * @return array Found routes
     */
    private function findAllRoutesInApp()
    {
        $foundRoutes = array();
        // Config routes
        $configRoutes = App::getInstance()->getConfig()->routes;

        foreach ($configRoutes as $area => $namespace) {
            if (isset($namespace['controllers'])) {
                foreach ($namespace['controllers'] as $controller => $methods) {
                    foreach ($methods['methods'] as $newFunctionRoute => $originalFunction) {
                        $file = App::getInstance()->getConfig()->app['namespaces']['Controllers'];
                        //$file = $file . ucfirst($methods['goesTo']) . 'Controller';

                        if ($area !== '*') {
                            $file .= $area;
                            $file = $file . '\\' . ucfirst($methods['goesTo']) . 'Controller';
                        } else {
                            $file = $file . ucfirst($methods['goesTo']) . 'Controller';
                        }

                        $file = str_replace('../', '', $file);
                        $file = str_replace('/', '\\', $file);
                        $file = substr($file, 13);
                        $reflection = new \ReflectionMethod($file, $originalFunction);
                        $doc = $reflection->getDocComment();
                        $params = $this->findBindingModels($doc);
                        $requestMethod = null;

                        if (isset($methods['requestMethod'][$newFunctionRoute])) {
                            $requestMethod = $methods['requestMethod'][$newFunctionRoute];
                        } else {
                            // Methods without config request - checking controller for annotation
                            if ($methods['goesTo'] && $originalFunction) {
                                preg_match('/@(post|get|put|delete)/', strtolower($doc), $requestMethods);
                                $requestMethod = 'Get';

                                if (isset($requestMethods[1])) {
                                    $requestMethod = $requestMethods[1];
                                }
                            }
                        }

                        if ($area === '*') {
                            $route = '@' . strtoupper($requestMethod) . ' ' .
                                strtolower($controller . '/' . $newFunctionRoute);
                        } else {
                            $route = '@' . strtoupper($requestMethod) . ' ' .
                                strtolower($area . '/' . $controller . '/' . $newFunctionRoute);
                        }

                        $foundRoutes[$route] = $params;
                    }
                }
            }
        }

        // Custom routes and not listed ones
        $controllersFolder = App::getInstance()->getConfig()->app['namespaces']['Controllers'];
        $allFiles = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($controllersFolder));
        $phpFiles = new \RegexIterator($allFiles, '/\.php$/');

        foreach ($phpFiles as $file) {
            $controllerPath = str_replace('../', '', $file->getPathName());
            $controllerPath = str_replace('.php', '', $controllerPath);
            $normalizedPath = str_replace('/', '\\', $controllerPath);
            $normalizedPath = substr($normalizedPath, 13);
            $reflectionController = new \ReflectionClass(new $normalizedPath);
            $methods = $reflectionController->getMethods();

            foreach ($methods as $method) {
                $doc = $method->getDocComment();
                @$params = $this->findBindingModels($doc);
                $doc = strtolower($doc);
                preg_match('/@route\("(.*)"\)/', $doc, $matches);
                preg_match('/@(post|get|put|delete)/', $doc, $requestMethods);
                $route = isset($matches[1]) ? $matches[1] : null;
                $requestMethod = 'Get';

                if (isset($requestMethods[1])) {
                    $requestMethod = $requestMethods[1];
                }

                if ($route) {
                    $fullRoute = '@' . strtoupper($requestMethod) . ' ' . strtolower($route);
                    $foundRoutes[$fullRoute] = $params;
                }
            }
        }

        return $foundRoutes;
    }
}