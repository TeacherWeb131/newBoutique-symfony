<?php

namespace App\Controller;

use App\Cart\CartService;
use App\Entity\Command;
use App\Entity\CommandProduct;
use App\Form\AddressType;
use App\Repository\ProductRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CommandController extends AbstractController
{
    /**
     * @Route("/command", name="command_index")
     */
    public function index(Request $request, SessionInterface $session)
    {
        $form = $this->createForm(AddressType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // getData() extrait les données du formulaire dans un tableau associatif nomchamp=>value
            $data = $form->getData();
            // On envoie l'adresse dans la session set(clé, valeur)
            $session->set('command-address', $data['address']);
            // et on redirige vers la page de payment
            return $this->redirectToRoute("command_payment");
        }

        return $this->render('command/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/payment", name="command_payment")
     *
     */
    public function payment(CartService $cartService, SessionInterface $session)
    {
        return $this->render('command/payment.html.twig', [
            'total' => $cartService->getGrandTotal(),
            'address' => $session->get('command-address')
        ]);
    }

    /**
     * @Route("/process", name="command_process")
     */
    public function process(SessionInterface $session, CartService $cartService, ObjectManager $manager, ProductRepository $repo)
    {
        // Je crée une commande avec sa date et son adresse
        $commande = new Command();
        $commande->setCreatedAt(new \DateTime())
            ->setAddress($session->get('command-address'));

        $manager->persist($commande);

        // Je crée les CommandProduct
        foreach ($cartService->getItems() as $item) {
            // Je crée une command
            $commandProduct = new CommandProduct();

            // PATCH LE PLUS CON :
            $product = $repo->find($item->getProduct()->getId());

            // Et pour chaque Command, Je crée un CommandProduct
            $commandProduct->setProduct($product)
                ->setQuantity($item->getQuantity())
                ->setCommand($commande);

            $manager->persist($commandProduct);
        }
        $manager->flush();

        // On vide le panier
        $cartService->empty();

        // Affichache de l'éta de la commande (réussie)
        return $this->render('command/success.html.twig', [
            'commande' => $commande
        ]);
    }
}
