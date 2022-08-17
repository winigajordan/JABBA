<?php

namespace App\Controller;

use App\Repository\CategorieProduitRepository;
use App\Repository\ProduitRepository;
use ContainerADFKH7j\getDoctrine_Orm_DefaultEntityManager_PropertyInfoExtractorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DetailsProduitController extends AbstractController
{
    private ProduitRepository $prodRipo;
    private CategorieProduitRepository $catRipo;

    public function __construct(ProduitRepository $prodRipo, CategorieProduitRepository $catRipo)
    {
        $this->prodRipo = $prodRipo;
        $this->catRipo = $catRipo;
    }

    #[Route('/details/produit/{slug}', name: 'app_details_produit')]
    public function index($slug, EntityManagerInterface $em): Response
    {
        $prod = $this->prodRipo->findOneBy(['slug'=>$slug]);
        $produits = $this->prodRipo->findBy(['categorie'=>$prod->getCategorie()]);
        foreach ($produits as $id => $p){
            if ($p->getSlug()==$prod->getSlug()){
                unset($produits[$id]);
            }
        }
        $prod->setViews($prod->getViews()+1);
        if (count($produits)>4){
            $produits = array_splice($produits, 0, 4);
        }
        $em->persist($prod);
        $em->flush();
        return $this->render('details_produit/index.html.twig', [
           'prod' => $prod,
            'produits'=>$produits
        ]);

    }
}
