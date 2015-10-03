<?php

namespace Models\ViewModels\AdminViewModel;

class IndexViewModel {
    private $admins;

    public function __construct($admins)
    {
        $this->admins = $admins;
    }

    public function getAdmins()
    {
        return $this->admins;
    }
}