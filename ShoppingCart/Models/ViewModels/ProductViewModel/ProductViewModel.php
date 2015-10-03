<?php

namespace Models\ViewModels\ProductViewModel;

class ProductViewModel
{
    private $id;
    private $name;
    private $description;
    private $price;
    private $quantity;
    private $category;
    private $promotion;
    private $givenReviews;

    public function __construct($id, $name, $description, $price, $quantity, $category, $promotion = null, $givenReviews = [])
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->category = $category;
        $this->promotion = $promotion;
        $this->givenReviews = $givenReviews;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return mixed
     */
    public function getPromotion()
    {
        return $this->promotion;
    }

    /**
     * @return array
     */
    public function getGivenReviews()
    {
        return $this->givenReviews;
    }
}