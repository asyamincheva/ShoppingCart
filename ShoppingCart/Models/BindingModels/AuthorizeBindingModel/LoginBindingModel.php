<?php

namespace Models\BindingModels\AuthorizeBindingModel;

class LoginBindingModel
{
    private $username;
    private $password;

    function __construct(array $params)
    {
        $this->setUsername($params['username']);
        $this->setPassword($params['password']);
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    private  function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    private  function setPassword($password)
    {
        $this->password = $password;
    }
}