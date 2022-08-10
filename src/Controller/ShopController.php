<?php

namespace App\Controller;

use App\Repository\BoutiqueRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopController extends AbstractController
{
    private ProduitRepository $prodRipo;
    private BoutiqueRepository $btqRipo;

    /**
     * @param ProduitRepository $prodRipo
     * @param BoutiqueRepository $btqRipo
     */
    public function __construct(ProduitRepository $prodRipo, BoutiqueRepository $btqRipo)
    {
        $this->prodRipo = $prodRipo;
        $this->btqRipo = $btqRipo;
    }

    #[Route('/boutique/{boutique}', name: 'app_boutique', defaults: ['boutique'=>'all'])]
    public function index($boutique): Response
    {
        //dd($boutique);
        $produits = null;
        if ($boutique!='all'){
            $produits = $this->btqRipo->findOneBy(['slug'=>$boutique])->getProduits();
        } else {
            $produits = $this->prodRipo->findAll();
        }
        return $this->render('shop/index.html.twig', [
            'controller_name' => 'ShopController',
            'boutiques' => $this -> btqRipo -> findAll(),
            'produits' => $produits
        ]);
    }
}
