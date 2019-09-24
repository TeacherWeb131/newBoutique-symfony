<?php

namespace App\Controller;

use App\Entity\Command;
use App\Cart\CartService;
use App\Form\AddressType;
use App\Entity\CommandProduct;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;

/**
 * @IsGranted("ROLE_USER")
 */
class CommandController extends AbstractController
{
    /**
     * @Route("/command", name="command_index")
     */
    public function index(Request $request, SessionInterface $session)
    {
        // Equivalent de l'annotation IsGranted("ROLE_USER")
        // $this->denyAccessUnlessGranted('ROLE_USER');

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
    public function payment(CartService $cartService, SessionInterface $session, Request $request)
    {
        // Si j'ai un token...
        if ($request->request->get('stripeToken')) {
            // ... Alors creation du paiement
            // Set your secret key: remember to change this to your live secret key in production
            // See your keys here: https://dashboard.stripe.com/account/apikeys
            \Stripe\Stripe::setApiKey('sk_test_ThVeowXhVDQBPa6PrnfAWCRO005Qq7aFcn');

            // Token is created using Checkout or Elements!
            // Get the payment token ID submitted by the form:
            $token = $request->request->get('stripeToken');
            $charge = \Stripe\Charge::create([
                'amount' => $cartService->getGrandTotal() * 100,
                'currency' => 'eur',
                'description' => 'Example charge',
                'source' => $token,
            ]);

            if ($charge->status === "succeeded") {
                return $this->redirectToRoute('command_process');
            }
        }

        return $this->render('command/payment.html.twig', [
            'total' => $cartService->getGrandTotal(),
            'address' => $session->get('command-address')
        ]);
    }

    /**
     * @Route("/process", name="command_process")
     */
    public function process(
        SessionInterface $session,
        CartService $cartService,
        ObjectManager $manager,
        ProductRepository $repo,
        Security $security
    ) {
        // Je crée une commande avec sa date et son adresse
        $commande = new Command();
        $commande->setCreatedAt(new \DateTime())
            ->setAddress($session->get('command-address'))
            // Security permet de récupérer le User dans un controller
            ->setUser($security->getUser());

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
