<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Wallet;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{

    private UserPasswordHasherInterface $hasher;
    private EntityManagerInterface $em;
    public function __construct(
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $em
    )
    {
        $this->hasher = $hasher;
        $this->em = $em;
    }

    #[Route('/register', name: 'app_register')]
    public function index(): Response
    {
        return $this->render('register/index.html.twig', [
            'controller_name' => 'RegisterController',
        ]);
    }

    #[Route('/acccount-creation', name:'account_creation', methods: 'POST')]
    public function  accountCreation(Request $request){

        $data = $request->request;
        $client = new Client();
        $client -> setEmail($data->get('email'));
        $client -> setNom($data->get('nom'));
        $client -> setPrenom($data->get('prenom'));
        $client -> setTelephone($data->get('telephone'));
        $client -> setPassword($this->hasher->hashPassword($client, $data->get('password')));
        $this -> em -> persist($client);
        $wallet = new Wallet();
        $wallet -> setSolde(0);
        $wallet -> setCompte($client);
        $wallet -> setEtat('VALIDE');
        $this -> em -> persist($wallet);
        $this -> em -> flush();
        return $this->redirectToRoute('app_login');

    }
}
