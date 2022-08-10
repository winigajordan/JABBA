<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
use App\Repository\BoutiqueRepository;
use App\Repository\DetailsCommandeRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BoutiqueCommandesController extends AbstractController
{

    private BoutiqueRepository $btqRipo;
    private ProduitRepository $prodRipo;
    private DetailsCommandeRepository $detailsRipo;

    /**
     * @param BoutiqueRepository $btqRipo
     * @param ProduitRepository $prodRipo
     * @param DetailsCommandeRepository $detailsRipo
     */
    public function __construct(BoutiqueRepository $btqRipo, ProduitRepository $prodRipo, DetailsCommandeRepository $detailsRipo)
    {
        $this->btqRipo = $btqRipo;
        $this->prodRipo = $prodRipo;
        $this->detailsRipo = $detailsRipo;
    }


    #[Route('/boutique/commandes', name: 'app_boutique_commandes')]
    public function index(): Response
    {
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
}
