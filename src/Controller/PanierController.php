<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    private ProduitRepository $prodRipo;

    /**
     * @param ProduitRepository $prodRipo
     */
    public function __construct(ProduitRepository $prodRipo)
    {
        $this->prodRipo = $prodRipo;
    }

    #[Route('/panier', name: 'app_panier')]
    public function index(SessionInterface $session): Response
    {
        $cartItem = $session->get('panier', []);

        $panier =[];
        $total = 0;
        foreach ($cartItem as $slug => $qte){
            $prod = $this->prodRipo->findOneBy(['slug'=>$slug]);
            $panier[]=[
                'item'=>$prod,
                'qte'=>$qte
            ];
            $totalItem=0;
            if($prod->isIsSolde()){
                $totalItem = $prod->getNewMontant()*$qte;
            } else {
                $totalItem = $prod->getMontant()*$qte;
            }
            $total += $totalItem;

        }
        //dd($panier);
        return $this->render('panier/index.html.twig', [
            'items' => $panier,
            'total' => $total
        ]);
    }

    #[Route('/panier/add', name: 'add_item')]
    public function addItem( Request $request, SessionInterface $session) : Response{
        if (!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }
        //dd($request->request);
        $panier = $session->get('panier', []);
        $qte = $request->request->get('qte');
        $id = $request->request->get('id');
        if ($qte<1){
            if (isset($panier[$id])){
                unset($panier[$id]);
            }
        } else {
            $panier[$id]=$qte;
        }

        $session->set('panier', $panier);
        return $this->redirectToRoute('app_panier');
    }

}
