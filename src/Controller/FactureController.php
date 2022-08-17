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
        if ($this->getUser()==null){
            return $this->redirectToRoute('app_login');
        }
        $cmd = $this->cmdRipo->findOneBy(['slug'=>$slug]);
        $coupons = [];
        $totalReduction = 0;
        foreach ($cmd->getCommandeReductions() as $key=>$reduction){
            $totalReduction += $reduction->getCode()->getReduction();
        }

        $html =  $this->render('facture/facture.html.twig', [
            'cmd'=>$this->cmdRipo->findOneBy(['slug'=>$slug]),
            'reduction'=>$totalReduction
        ]);
        return $html;
    }
}
