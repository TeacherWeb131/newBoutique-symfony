<?php

namespace App\Entity;

class CartItem
{
    /**
     * Le produit concerné
     *
     * @var Product
     */
    protected $product;

    /**
     * La quantité que contient le panier
     *
     * @var int
     */
    protected $quantity;

    public function __construct(Product $product, int $quantity)
    {
        $this->product = $product;
        $this->quantity = $quantity;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;
        return $this;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function increment(): self
    {
        $this->quantity++;
        return $this;
    }
}
