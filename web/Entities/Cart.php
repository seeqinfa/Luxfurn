<?php

class Cart
{
    public $furnitureID;
    public $name;
    public $price;
    public $image_url;
    public $category;
    public $quantity;

    public function __construct($furnitureID, $name, $price, $image_url, $category, $quantity = 1)
    {
        $this->furnitureID = $furnitureID;
        $this->name = $name;
        $this->price = $price;
        $this->image_url = $image_url;
        $this->category = $category;
        $this->quantity = $quantity;
    }

    public function getTotalItemPrice()
    {
        return $this->price * $this->quantity;
    }
}

?>