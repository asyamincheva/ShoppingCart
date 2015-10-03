<?php

namespace Models\ViewModels\ProductViewModel;

class IndexViewModel
{
    private $products;
    private $start;
    private $end;

    public function __construct($products, $start, $end)
    {
        $this->products = $products;
        $this->start = $start;
        $this->end = $end;
    }

    /**
     * @return mixed
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @return mixed
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @return mixed
     */
    public function getEnd()
    {
        return $this->end;
    }
}