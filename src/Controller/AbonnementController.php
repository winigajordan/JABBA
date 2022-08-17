<?php

namespace App\Controller;

use App\Entity\Abonnement;
use App\Repository\BoutiqueRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AbonnementController extends AbstractController
{
    #[Route('/abonnement/{slug}', name: 'app_abonnement')]
    public function index(EntityManagerInterface $em, ProduitRepository $prodRipo, $slug)
    {
        if ($this->getUser() == null){
            return $this->redirectToRoute('app_login');
        }
        $boutique = $prodRipo->findOneBy(['slug'=>$slug])->getBoutique();
        foreach ($this->getUser()->getAbonnements() as $key=>$value){
            if ($value->getBoutique()->getSlug()==$boutique->getSlug()){
                $em->remove($value);
                $em -> flush();
                return $this->redirectToRoute('app_details_produit', ['slug'=>$slug]);
            }
        }
        $abo = new Abonnement();
        $abo -> setBoutique($boutique);
        $abo -> setUser($this->getUser());
        $em -> persist($abo);
        $em -> flush();
        return $this->redirectToRoute('app_details_produit', ['slug'=>$slug]);
    }
}
