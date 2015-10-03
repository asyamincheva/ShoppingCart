<?php

namespace Models\ViewModels\EditorViewModel\PromotionViewModel;

class AllViewModel
{
    private $promotions;

    function __construct($promotions)
    {
        $this->promotions = $promotions;
    }

    /**
     * @return mixed
     */
    public function getPromotions()
    {
        return $this->promotions;
    }
}