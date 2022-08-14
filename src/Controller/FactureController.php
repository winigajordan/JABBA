<?php

namespace App\Controller;

use App\Repository\CommandeRepository;
use App\Service\PdfService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Laminas\Code\Generator\FileGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FactureController extends AbstractController
{
    private CommandeRepository $cmdRipo;

    /**
     * @param CommandeRepository $cmdRipo
     */
    public function __construct(CommandeRepository $cmdRipo)
    {
        $this->cmdRipo = $cmdRipo;

    }

    #[Route('/facture/{slug}', name: 'app_facture')]
    public function index($slug, PdfService $pdf)
    {
        $html =  $this->render('facture/facture.html.twig', [
            'cmd'=>$this->cmdRipo->findOneBy(['slug'=>$slug])
        ]);
        return $html;
    }
}
