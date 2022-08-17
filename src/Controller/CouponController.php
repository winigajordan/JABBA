<?php

namespace App\Controller;

use App\Repository\CodeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CouponController extends AbstractController
{
    #[Route('/coupon/', name: 'app_coupon')]
    public function index(Request $request, SessionInterface $session, CodeRepository $codeRipo ): Response
    {
        //dd($request->request->get('code'));
        $code = $codeRipo->findOneBy(['code'=>$request->request->get('code'), 'etat'=>'VALIDE']);
        if ($code==null){
            $this->addFlash('error', 'Code promo invalide');
        } else {
            $coupons = $session->get('coupons', []);
            foreach ($coupons as $id=>$value){
                if ($value['code']==$code->getCode()){
                    $this->addFlash('error', 'Code promo déjà utilisé');
                    return $this->redirectToRoute('app_checkout');
                }
            }

            $coupons[]=[
                'code'=>$code->getCode(),
                'reduction'=>$code->getReduction()
            ];
            $this->addFlash('success', 'Code promo appliqué avec succès');
            $session->set('coupons', $coupons);
        }
        return $this->redirectToRoute('app_checkout');
    }
}
