<?php

namespace Models\ViewModels\UserViewModel;

class ProfileViewModel
{
    private $username;
    private $isAdmin;
    private $balance;
    private $isEditor;
    private $isModerator;

    function __construct($username, $isAdmin, $balance, $isEditor, $isModerator)
    {
        $this->username = $username;
        $this->isAdmin = $isAdmin;
        $this->balance = $balance;
        $this->isEditor = $isEditor;
        $this->isModerator = $isModerator;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return mixed
     */
    public function getIsAdmin()
    {
        return $this->isAdmin;
    }

    /**
     * @return mixed
     */
    public function getIsEditor()
    {
        return $this->isEditor;
    }

    /**
     * @return mixed
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @return mixed
     */
    public function getIsModerator()
    {
        return $this->isModerator;
    }
}