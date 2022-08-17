<?php

namespace App\Controller;

use App\Entity\Zone;
use App\Repository\ZoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/admin/zone')]
class AdminZoneController extends AbstractController
{
    private EntityManagerInterface $em;
    private ZoneRepository $zoneRipo;

    /**
     * @param EntityManagerInterface $em
     * @param ZoneRepository $zoneRipo
     */
    public function __construct(EntityManagerInterface $em, ZoneRepository $zoneRipo)
    {
        $this->em = $em;
        $this->zoneRipo = $zoneRipo;
    }


    #[Route('/', name: 'app_admin_zone')]
    public function index(): Response
    {
        return $this->render('admin/admin_zone/index.html.twig', [
            'zones' => $this->zoneRipo->findAll(),
        ]);
    }

    #[Route('/add', name: 'app_admin_zone_add')]
    public function add(Request $request): Response
    {
        $data = $request->request;
        $zone = new Zone();
        $zone->setLibelle($data->get('nom'));
        $zone->setQuartiers($data->get('quartiers'));
        $zone->setMontant($data->get('montant'));
        $zone->setSlug(uniqid('zone'));
        $this->em->persist($zone);
        $this->em->flush();
        return $this->redirectToRoute('app_admin_zone');
    }
}
