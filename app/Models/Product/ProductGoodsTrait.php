<?php

namespace App\Models\Product;

trait ProductGoodsTrait
{
    public function getImage()
    {
        return $this->image;
    }
    public function getTitle()
    {
        return $this->title;
    }

    public function getPrice()
    {
        return $this->price;
    }
    public function getAmount()
    {
        return $this->amount;
    }
}
