<?php

namespace App\Controller;

use App\Entity\Favoris;
use App\Repository\FactureRepository;
use App\Repository\FavorisRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FavorisController extends AbstractController
{
    private FavorisRepository $favRipo;
    private ProduitRepository $prodRipo;
    private EntityManagerInterface $em;

    public function __construct(FavorisRepository $favRipo, ProduitRepository $prodRipo, EntityManagerInterface $em)
    {
        $this->favRipo = $favRipo;
        $this->prodRipo = $prodRipo;
        $this->em = $em;
    }


    #[Route('/favoris', name: 'app_favoris')]
    public function index(): Response
    {
        $fav = $this->favRipo->findBy(['client'=>$this->getUser()]);
        return $this->render('favoris/index.html.twig', [
            'favoris'=>$fav
        ]);
    }

    #[Route('/favoris/update/{slug}', name: 'app_favoris_update')]
    public function add($slug)
    {
        $prod = $this->prodRipo->findOneBy(['slug'=>$slug]);
        $userFav = $this->favRipo->findBy(['client'=>$this->getUser()]);
        $verif = false;
        foreach ($userFav as $fav){
            if ($prod == $fav->getProduit()){
                $this->em->remove($fav);
                $verif = true;
            }
        }
        if ($verif == false){
            $newFav = new Favoris();
            $newFav -> setClient($this->getUser());
            $newFav -> setProduit($prod);
            $this->em->persist($newFav);
        }
        $this->em->flush();
        return $this->redirectToRoute('app_boutique');
    }
}