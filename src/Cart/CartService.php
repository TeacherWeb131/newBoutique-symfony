<?php

namespace App\Cart;

use App\Entity\Cart;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    /**
     * La variable de session
     *
     * @var SessionInterface
     */
    protected $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function add(Product $product)
    {
        // Je récupère le tableau qui porte le nom "cart-items" dans ma session
        $cart = $this->session->get('cart-items', new Cart());

        // Si ce produit n'existe pas dans mon panier
        if ($cart->contains($product->getId()) === false) {
            // Je créé le couple produit:quantity
            $cart->add($product, 1);
        } else {
            // Sinon, je ne fais qu'augmenter de 1 la quantity
            $cartItem = $cart->get($product->getId());
            $cartItem->increment();
        }

        // Je remet mon tableau dans la session
        $this->session->set('cart-items', $cart);
    }

    public function remove(Product $product): bool
    {
        // Le but est de supprimer un produit du panier  (nécessite l'accès à la session)
        if (!$this->session->has('cart-items')) {
            return false;
        }

        // Je récupère ma liste de couples produit:quantity
        $cart = $this->session->get('cart-items');

        // Je supprime l'élément dont la clé est l'id du produit
        $cart->remove($product->getId());

        // Je remet mon panier dans la session
        $this->session->set('cart-items', $cart);

        return true;
    }

    public function empty(): bool
    {
        $this->session->remove('cart-items');
        return true;
    }

    /**
     * Récupère la liste des CartItem qui sont dans la session
     *
     * @return CartItem[]
     */
    public function getItems(): array
    {
        $cart = $this->session->get('cart-items', new Cart());

        return $cart->all();
    }
}
