<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\CommandeReduction;
use App\Entity\DetailsCommande;
use App\Entity\Livraison;
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
        if (!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }
        return $this->render('commande/index.html.twig', [
            'commandes'=>$this->getUser()->getCommandes(),
            'montant'=>$this->getUser()->getWallet()->getSolde()
        ]);
    }

    #[Route('/commande/add', name: 'app_commande_add')]
    public function add(SessionInterface $session, Request $request)
    {
        if (!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }

       $cartItem = $session->get('panier');
       $commande = new Commande();
       $commande -> setSlug(uniqid('cmd-'));
       $commande -> setClient($this->getUser());
       $commande -> setDate(new \DateTime());

       //mise à jour de l'état de la commande
       if($request->request->get('pay')){
           $commande -> setEtat('PAY');
       } else {
           $commande -> setEtat('EN COURS');
       }
       $total = 0;

        $coupons = $session->get('coupons', []);
        $totalReduction = 0;
        if (count($coupons)>0){
            foreach ($coupons as $key=>$value){
                $cr = new CommandeReduction();
                $cr->setCommande($commande);
                $cr->setCode($this->codeRipo->findOneBy(['code'=>$value['code']]));
                $this->em->persist($cr);
                if ($totalReduction<1){
                    $totalReduction += $value['reduction'];
                }
            }
            $total = $total - $total*$totalReduction;
        }

       foreach ($cartItem as $slug => $qte){
          $details = new DetailsCommande();
          $prod = $this->prodRipo->findOneBy(['slug'=>$slug]);
          $details -> setProduit($prod);
          $details -> setEtat('EN COURS');
          $details -> setCommande($commande);
          $details -> setQuantite($qte);
          $details -> setSlug(uniqid('dtls-'));

          if($prod->isIsSolde()){
              $details->setPrix(($prod->getNewMontant() - $prod->getNewMontant()*$totalReduction)*$qte) ;
          } else {
            $details->setPrix(($prod->getMontant() - $prod->getMontant()*$totalReduction)*$qte) ;
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


       $livraison = new Livraison();
       $livraison->setCommande($commande);
       $livraison->setAdresse($this->getUser()->getDefaultAdresse());
       $this->em->persist($livraison);

       $commande->setMontant($total + $this->getUser()->getDefaultAdresse()->getZone()->getMontant());



       $this->em->persist($commande);
       $this->em->flush();
       $session->set('panier', []);
       $session->set('coupons', []);
       //dd('done');
       return $this->redirectToRoute('app_commande');
    }

    #[Route('/commande/details/{slug}', name: 'app_commande_details')]
    public function details($slug): Response
    {
        if ($this->getUser()==null){
            return $this->redirectToRoute('app_login');
        }
        return $this->render('commande/index.html.twig', [
            'commandes'=>$this->getUser()->getCommandes(),
            'selected'=>$this->cmdRipo->findOneBy(['slug'=>$slug])
        ]);
    }
}
