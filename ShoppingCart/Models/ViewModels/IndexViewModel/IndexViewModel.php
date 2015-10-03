<?php

namespace Models\ViewModels\IndexViewModel;

class IndexViewModel {
    private $data;

    public function __construct($data){
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