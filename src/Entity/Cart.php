<?php

namespace App\Entity;

class Cart
{
    /**
     * Le tableau qui contient les CartItem (en gros c'est notre panier)
     *
     * @var CartItem[]
     */
    protected $items = [];

    /**
     * Ajoute un couple produit:quantity (CartItem) dans le panier
     *
     * @param Product $product
     * @param integer $quantity
     * @return self
     */
    public function add(Product $product, int $quantity): self
    {
        $this->items[$product->getId()] = new CartItem($product, $quantity);
        return $this;
    }

    /**
     * Récupère un couple produit:quantity grâce à un identifiant de produit
     *
     * @param integer $productId
     * @return CartItem|null
     */
    public function get(int $productId): ?CartItem
    {
        if (empty($this->items[$productId])) {
            return null;
        }

        return $this->items[$productId];
    }

    /**
     * Indique si le tableau contient un couple dont l'identifiant du produit est celui qu'on passe en paramètre
     *
     * @param integer $productId
     * @return boolean
     */
    public function contains(int $productId): bool
    {
        return !empty($this->items[$productId]);
    }

    /**
     * Récupère une copie du tableau (par exemple pour boucler dessus)
     *
     * @return CartItem[]
     */
    public function all(): array
    {
        return array_slice($this->items, 0);
    }

    /**
     * Retire un produit du panier grâce à son identifiant
     *
     * @param integer $productId
     * @return self
     */
    public function remove(int $productId): self
    {
        unset($this->items[$productId]);
        return $this;
    }
}
