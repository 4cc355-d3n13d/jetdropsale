<?php

namespace App\Models;

interface GoodsContract
{
    public function getImage();
    public function getTitle();
    public function getPrice();
    public function getAmount();
}
