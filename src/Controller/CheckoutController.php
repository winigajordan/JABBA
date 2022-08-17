<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends AbstractController
{
    #[Route('/checkout', name: 'app_checkout')]
    public function index(SessionInterface $session,ProduitRepository $prodRipo ): Response
    {
        if ($this->getUser()->getAdresse()==null){
            return $this->redirectToRoute('app_adresse');
        }
        $cartItem = $session->get('panier', []);
        $coupons = $session->get('coupons', []);
        $panier =[];
        $total = 0;
        foreach ($cartItem as $slug => $qte){
            $prod = $prodRipo->findOneBy(['slug'=>$slug]);

            $totalItem=0;
            if($prod->isIsSolde()){
                $totalItem = $prod->getNewMontant()*$qte;
            } else {
                $totalItem = $prod->getMontant()*$qte;
            }
            $panier[]=[
                'item'=>$prod,
                'prix'=>$totalItem,
                'qte'=>$qte
            ];
            $total += $totalItem;

        }

        $couponItems = [];
        $totalReduction = 0;
        foreach ($coupons as $key=>$value){
            $couponItems[]=$value;
            if ($totalReduction<1){
                $totalReduction += $value['reduction'];
            }
        }
        $total = $total - $totalReduction*$total;
        $total = $total + $this->getUser()->getDefaultAdresse()->getZone()->getMontant();

        return $this->render('checkout/index.html.twig', [
            'user'=>$this->getUser(),
            'total'=>$total,
            'totalReduction' =>$totalReduction,
            'coupons'=>$couponItems,
            'cardItem'=>$panier
        ]);
    }

}
