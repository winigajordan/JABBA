<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{


    #[Route('/', name: 'app_accueil')]
    public function index(ProduitRepository $prodRipo): Response
    {

        $recent = $prodRipo->findBy(array(), ['views'=>'DESC']);
        $all = $prodRipo->findBy(array(), ['id'=>'DESC']);
        $all1 = [];
        $all2 = [];
        $vue1 = [];
        $vue2 = [];
        $vue1 = array_splice($recent, 0, 4);
        $vue2 = array_splice($recent, 0, 4);
        $all1 = array_splice($all, 0, 4);
        $all2 = array_splice($all, 0, 4);

        return $this->render('accueil/index.html.twig', [
            'vue1'=>$vue1,
            'vue2'=>$vue2,
            'all1'=>$all1,
            'all2'=>$all2
        ]);
    }


}
