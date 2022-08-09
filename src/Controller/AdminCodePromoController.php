<?php

namespace App\Controller;

use App\Entity\Code;
use App\Repository\CodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCodePromoController extends AbstractController
{
    private EntityManagerInterface $em;
    private CodeRepository $codeRipo;

    /**
     * @param EntityManagerInterface $em
     * @param CodeRepository $codeRipo
     */
    public function __construct(EntityManagerInterface $em, CodeRepository $codeRipo)
    {
        $this->em = $em;
        $this->codeRipo = $codeRipo;
    }


    #[Route('/admin/code/promo', name: 'app_admin_code_promo')]
    public function index(): Response
    {
        return $this->render('admin/admin_code_promo/index.html.twig', [
            'codes'=>$this->codeRipo->findAll(),
        ]);
    }

    #[Route('/admin/code/promo/add', name: 'app_admin_code_promo_add', methods: 'post')]
    public function add(Request $request): Response
    {

        $code = new Code();
        $code -> setCode($request->request->get('nom'));
        $code -> setReduction($request->request->get('pourcentage')*0.01);
        $code -> setEtat('VALIDE');

        $this->em->persist($code);
        $this->em->flush();
        return $this->redirectToRoute('app_admin_code_promo');
    }
}
