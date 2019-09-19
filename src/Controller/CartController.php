<?php

namespace App\Controller;

use App\Cart\CartService;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/cart/add/{id}", name="cart_add")
     *
     */
    public function add(Product $product, CartService $cartService)
    {
        $cartService->add($product);
        dd($cartService->getItems());
    }

    /**
     * @Route("/cart/remove/{id}", name="cart_remove")
     */
    public function remove(Product $product, CartService $cartService)
    {
        $cartService->remove($product);
        dd($cartService->getItems());
    }

    /**
     * @Route("/cart/empty", name="cart_empty")
     *
     */
    public function empty(CartService $cartService)
    {
        // Vider le panier (nécessite l'accès à la Session)
        $cartService->empty();
        dd($cartService->getItems());
    }
}
