<?php

namespace App\Controller;

use App\Entity\Adresse;
use App\Repository\ZoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdresseController extends AbstractController
{

    #[Route('/adresse', name: 'app_adresse')]
    public function index(ZoneRepository $zoneRip): Response
    {
        return $this->render('adresse/index.html.twig', [
           'zones' => $zoneRip->findAll(),
        ]);
    }

    #[Route('/adresse/add', name: 'app_adresse_add')]
    public function add(Request $request, ZoneRepository $zoneRip, EntityManagerInterface $em): Response
    {
        $data = $request->request;
        $add = new Adresse();
        $add->setClient($this->getUser());
        $add->setCode($data->get('code'));
        $add->setPays($data->get('code'));
        $add->setPrecisions($data->get('precision'));
        $add->setQuartier($data->get('quartier'));
        $add->setVille($data->get('ville'));
        $add->setZone($zoneRip->findOneBy(['slug'=>$data->get('zone')]));
        $em->persist($add);
        $em->flush();
        return $this->redirectToRoute('app_adresse');
    }
}
