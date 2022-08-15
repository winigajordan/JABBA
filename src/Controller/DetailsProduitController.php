<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DetailsProduitController extends AbstractController
{
    #[Route('/details/produit', name: 'app_details_produit')]
    public function index(): Response
    {
        return $this->render('details_produit/index.html.twig', [
            'controller_name' => 'DetailsProduitController',
        ]);
    }
}
