<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\DetailsCommande;
use App\Entity\Vente;
use App\Repository\ClientRepository;
use App\Repository\CodeRepository;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use App\Repository\WalletRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController
{
    private EntityManagerInterface $em;
    private CommandeRepository $cmdRipo;
    private ProduitRepository $prodRipo;
    private CodeRepository $codeRipo;
    private ClientRepository $clientRipo;


    public function __construct(ClientRepository $clientRipo, EntityManagerInterface $em, CommandeRepository $cmdRipo, ProduitRepository $prodRipo, CodeRepository $codeRipo)
    {
        $this->em = $em;
        $this->cmdRipo = $cmdRipo;
        $this->prodRipo = $prodRipo;
        $this->codeRipo = $codeRipo;
        $this->clientRipo = $clientRipo;
    }

    #[Route('/commande', name: 'app_commande')]
    public function index(): Response
    {
        return $this->render('commande/index.html.twig', [
            'commandes'=>$this->getUser()->getCommandes()
        ]);
    }

    #[Route('/commande/add', name: 'app_commande_add', methods: 'post')]
    public function add(Request $request, SessionInterface $session)
    {

       $cartItem = $session->get('panier');
       $commande = new Commande();
       $commande -> setEtat('EN COURS');
       $commande -> setSlug(uniqid('cmd-'));
       $commande -> setClient($this->getUser());
       $commande -> setDate(new \DateTime());
       $total = 0;
       foreach ($cartItem as $slug => $qte){
          $details = new DetailsCommande();
          $prod = $this->prodRipo->findOneBy(['slug'=>$slug]);
          $details -> setProduit($prod);
          $details -> setEtat('EN COURS');
          $details -> setCommande($commande);
          $details -> setQuantite($qte);
          $details -> setSlug(uniqid('dtls-'));

          if($prod->isIsSolde()){
              $details->setPrix($prod->getNewMontant()*$qte) ;
          } else {
            $details->setPrix($prod->getMontant()*$qte) ;
          }
          $total += $details->getPrix();
          $this->em->persist($details);

          //creation des transaction
           $vente = new Vente();
           $wallet = $details->getProduit()->getBoutique()->getClient()->getWallet();
           $vente -> setDetails($details);
           $vente -> setDate(new \DateTime());
           $vente -> setMonatnt($details->getPrix() - $details->getPrix() * 0.1);
           $vente -> setWallet($wallet);
           $vente -> setReference(uniqid('vte-'));
           $this -> em -> persist($vente);

           // rechargement du porte-feuille du vendeur
           $wallet -> setSolde($wallet->getSolde() + $vente->getMonatnt());
           $this -> em ->persist($wallet);

           // rechargement du compte admin
           $admin = $this->clientRipo->findOneBy(['email'=>'admin@wallet.com']);
           $adminWallet = $admin->getWallet();
           $adminWallet -> setSolde($adminWallet->getSolde()+($details->getPrix() - $details->getPrix() * 0.9));
           $this -> em -> persist($adminWallet);

       }
       $coupon = $request->request->get('code');
       if ($coupon) {
           $code = $this->codeRipo->findOneBy(['code'=>$coupon, 'etat'=>'VALIDE']);
           if ($code){
               $commande->setCode($code);
               $commande->setMontant($total - $total * $code->getReduction());
           } else {
               $this->addFlash('warning', 'Code promo erroné');
               return $this->redirectToRoute('app_panier');
           }
       } else {
           $commande -> setMontant($total);
       }
       $this->em->persist($commande);
       $this->em->flush();
       $session->set('panier', []);
       dd('done');
       return $this->redirectToRoute('app_commande');
    }
}
