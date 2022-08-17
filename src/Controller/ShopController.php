<?php

namespace App\Controller;

use App\Repository\BoutiqueRepository;
use App\Repository\CategorieProduitRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopController extends AbstractController
{
    private ProduitRepository $prodRipo;
    private BoutiqueRepository $btqRipo;
    private CategorieProduitRepository $catRipo;

    public function __construct(ProduitRepository $prodRipo, BoutiqueRepository $btqRipo, CategorieProduitRepository $catRipo)
    {
        $this->prodRipo = $prodRipo;
        $this->btqRipo = $btqRipo;
        $this->catRipo = $catRipo;
    }

    #[Route('/boutique/{filter}', name: 'app_boutique', defaults: ['filter'=>'all'])]
    public function index($filter): Response
    {
        //dd($boutique);

        $produits = null;

        if ($filter!='all'){
            if (strpos($filter, 'catProd-')!== false){
                $produits = $this->catRipo->findOneBy(['slug'=>$filter])->getProduits();
            }
            if (strpos($filter, 'btq' )!== false){
                $produits = $this->btqRipo->findOneBy(['slug'=>$filter])->getProduits();
            }
        } else {
            $produits = $this->prodRipo->findAll();
        }
        return $this->render('shop/index.html.twig', [
            'produits' => $produits,
            'categories' => $this->catRipo->findAll()
        ]);
    }
}
