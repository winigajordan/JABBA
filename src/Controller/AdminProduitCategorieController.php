<?php

namespace App\Controller;

use App\Entity\CategorieProduit;
use App\Repository\CategorieProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminProduitCategorieController extends AbstractController
{
    private EntityManagerInterface $em;
    private CategorieProduitRepository $catRipo;

    /**
     * @param EntityManagerInterface $em
     * @param CategorieProduitRepository $catRipo
     */
    public function __construct(
        EntityManagerInterface $em,
        CategorieProduitRepository $catRipo
    )
    {
        $this->em = $em;
        $this->catRipo = $catRipo;
    }


    #[Route('/admin/produit/categorie', name: 'app_admin_produit_categorie')]
    public function index(): Response
    {
        return $this->render('admin/admin_produit_categorie/index.html.twig', [
            'categories' => $this->catRipo->findAll()
        ]);
    }

    #[Route('/admin/produit/categorie/add', name : 'add_categorie', methods: 'POST')]
    public function addCategorie(Request $request){
        $cat = new CategorieProduit();
        $cat -> setLibelle($request->request->get('nom'));
        $cat -> setSlug(uniqid('catProd-'));
        $this -> em -> persist($cat);
        $this->em->flush();
        return $this->redirectToRoute('app_admin_produit_categorie');
    }
}
