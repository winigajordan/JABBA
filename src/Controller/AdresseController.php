<?php

namespace App\Controller;

use App\Entity\Adresse;
use App\Repository\AdresseRepository;
use App\Repository\ZoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdresseController extends AbstractController
{

    #[Route('/adresse', name: 'app_adresse')]
    public function index(ZoneRepository $zoneRip, AdresseRepository $addRpio): Response
    {
        if ($this->getUser() == null){
            return $this->redirectToRoute('app_login');
        }
        $add = $addRpio->findOneBy(['client'=>$this->getUser()]);
        if ($add==null){
            $add = new Adresse();
        }
        return $this->render('adresse/index.html.twig', [
           'zones' => $zoneRip->findAll(),
            'montant'=>$this->getUser()->getWallet()->getSolde(),
            'add'=>$add
        ]);
    }

    #[Route('/adresse/add', name: 'app_adresse_add')]
    public function add(Request $request, ZoneRepository $zoneRip, EntityManagerInterface $em, AdresseRepository $addRpio): Response
    {
        $add = $addRpio->findOneBy(['client'=>$this->getUser()]);
        if ($add==null){
            $add = new Adresse();
        }
        $data = $request->request;
        $add->setClient($this->getUser());
        $add->setCode($data->get('code'));
        $add->setPays($data->get('pays'));
        $add->setPrecisions($data->get('precision'));
        $add->setQuartier($data->get('quartier'));
        $add->setVille($data->get('ville'));
        $add->setZone($zoneRip->findOneBy(['slug'=>$data->get('zone')]));
        $em->persist($add);
        $em->flush();
        return $this->redirectToRoute('app_adresse');
    }
}
