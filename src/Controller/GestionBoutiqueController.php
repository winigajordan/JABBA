<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\Boutique;
use App\Entity\Produit;
use App\Entity\User;
use App\Repository\BoutiqueRepository;
use App\Repository\CategorieProduitRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class GestionBoutiqueController extends AbstractController
{
    private BoutiqueRepository $boutiqueRepository;
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $hasher;
    private ProduitRepository $prodRipo;
    private CategorieProduitRepository $catRipo;

    public function __construct(
        BoutiqueRepository $boutiqueRepository,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        ProduitRepository $prodRipo,
        CategorieProduitRepository $catRipo,
    )
    {
        $this -> boutiqueRepository = $boutiqueRepository;
        $this -> em = $em;
        $this->hasher = $hasher;
        $this->prodRipo = $prodRipo;
        $this->catRipo = $catRipo;
    }
    #[Route('/boutique/gestion', name: 'app_gestion_boutique')]
    public function index(): Response
    {
        $user = $this->getUser();
        if (!$user){
            return $this->redirectToRoute('app_login');
        } else {
            if ($user->getBoutique() == null){
                return $this->render('gestion_boutique/boutique_creation/index.html.twig');
            }
            $boutique = $this->boutiqueRepository->findOneBy(['client'=>$user]);
            return $this->render('gestion_boutique/index.html.twig', [
                'boutique'=>$boutique,
                'cats'=>$this->catRipo->findAll()
            ]);
        }
    }

    #[Route('/boutique/creation', name: 'shop_creation', methods: 'POST')]
    public function shopCreation(Request $request){
            $boutique = new Boutique();
            $boutique -> setClient($this->getUser());
            $boutique -> setNom($request->request->get('nom'));
            $boutique -> setNote(0);
            $boutique -> setSlug(uniqid('btq-'));
            $boutique -> setEtat('DEMANDE');

            //ENVOYER UN MAIL A L'ADMIN POUR LUI NOTIFIER LA DEMANDE DE CREATION DE BOUTIQUE
            $this -> em -> persist($boutique);
            $this -> em -> flush();
            $this -> addFlash('success', 'Boutique crée avec succès');
            return $this -> redirectToRoute('app_gestion_boutique');
    }

    #[Route('/boutique/name/update', name: 'shop_name_update', methods: 'POST')]
    public function updateName(Request $request){
        $boutique = $this->getUser() -> getBoutique();
        $boutique -> setNom($request->request->get('nom'));
        $this -> em -> persist($boutique);
    /*
        $admin = new Admin();
        $admin -> setNom("REMA");
        $admin -> setPrenom("Jordan");
        $admin -> setEmail("admin@admin.com");
        $admin -> setPassword($this->hasher->hashPassword($admin,'1234' ));
        $this -> em -> persist($admin);
    */
        $this -> em -> flush();
        return $this -> redirectToRoute('app_gestion_boutique');
    }

    #[Route('/boutique/produit/add', name: 'shop_product_add', methods: 'POST')]
    public function addProduit(Request $request){
        $data = $request->request;
        //dd($data);
        $produit = new Produit();
        $catId = $data->get('categorie');
        $cat = $this->catRipo->find($catId);
        $produit -> setCategorie($cat);
        $produit -> setEtat('VALIDE');
        $produit -> setBoutique($boutique = $this->getUser() -> getBoutique());
        $produit -> setLibelle($data->get('nom'));
        $produit -> setMontant($data -> get('prix'));
        $produit -> setDescription($data->get('description'));
        $produit -> setTaille($data->get('taille'));
        $produit -> setSlug(uniqid('prdt-'));
        $produit -> setViews(0);
        if ($data->get('isSolde')){

            $produit->setIsSolde(true);
            $new = floatval($data->get('solde'))*0.01*$produit->getMontant();
            $produit->setNewMontant($produit->getMontant()-$new);
        } else {
            $produit->setIsSolde(false);
            $produit->setNewMontant($produit->getMontant());
        }

        $img=$request->files->get("image");
        $imageName=uniqid().'.'.$img->guessExtension();
        $img->move($this->getParameter("products"),$imageName);
        $produit->setImage($imageName);
        $this->em->persist($produit);
        $this->em->flush();
        return $this -> redirectToRoute('app_gestion_boutique');
    }




}
