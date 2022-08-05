<?php

namespace App\Controller;

use App\Entity\Boutique;
use App\Entity\User;
use App\Repository\BoutiqueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GestionBoutiqueController extends AbstractController
{
    private BoutiqueRepository $boutiqueRepository;
    private EntityManagerInterface $em;
    public function __construct(
        BoutiqueRepository $boutiqueRepository,
        EntityManagerInterface $em
    )
    {
        //$this -> user = $this->getUser();
        $this -> boutiqueRepository = $boutiqueRepository;
        $this -> em = $em;
    }
    #[Route('/boutique/gestion', name: 'app_gestion_boutique')]
    public function index(): Response
    {
        $this -> user = $this->getUser();
        if (!$this->user){
            return $this->redirectToRoute('app_login');
        } else {
            $boutique = $this->boutiqueRepository->findOneBy(['client'=>$this->user]);
            return $this->render('gestion_boutique/index.html.twig', [
                'boutique'=>$boutique
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
        $this -> em -> flush();
        return $this -> redirectToRoute('app_gestion_boutique');
    }


}
