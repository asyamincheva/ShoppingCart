<?php

namespace Models\ViewModels\IndexViewModel;

class CreateViewModel
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}