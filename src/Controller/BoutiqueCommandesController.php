<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
use App\Repository\BoutiqueRepository;
use App\Repository\DetailsCommandeRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\Translation\t;

#[Route('/boutique/gestion')]
class BoutiqueCommandesController extends AbstractController
{

    private BoutiqueRepository $btqRipo;
    private ProduitRepository $prodRipo;
    private DetailsCommandeRepository $detailsRipo;
    private EntityManagerInterface $em;

    /**
     * @param BoutiqueRepository $btqRipo
     * @param ProduitRepository $prodRipo
     * @param DetailsCommandeRepository $detailsRipo
     * @param EntityManagerInterface $em
     */
    public function __construct(BoutiqueRepository $btqRipo, ProduitRepository $prodRipo, DetailsCommandeRepository $detailsRipo, EntityManagerInterface $em)
    {
        $this->btqRipo = $btqRipo;
        $this->prodRipo = $prodRipo;
        $this->detailsRipo = $detailsRipo;
        $this->em = $em;
    }


    #[Route('/commandes', name: 'app_boutique_commandes')]
    public function index(): Response
    {
        if (!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }
        $achats = $this->detailsRipo->findAll();
        $ventes = [];
        foreach ($achats as $item){
           if ($item->getProduit()->getBoutique()->getSlug() == $this->getUser()->getBoutique()->getSlug()){
               $ventes[]=$item;
           }
        }
        return $this->render('gestion_boutique/boutique_commandes/index.html.twig', [
            'ventes'=>$ventes
        ]);
    }

    #[Route('/commandes/{update}/{slug}', name: 'app_boutique_commandes_update')]
    public function update($update, $slug)
    {
        $details = $this->detailsRipo->findOneBy(['slug'=>$slug]);
        $details -> setEtat($update);
        $this->em->persist($details);
        $this->em->flush();
        return $this->redirectToRoute('app_boutique_commandes');

    }
}
