<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profil')]
class ProfileController extends AbstractController
{
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $hasher;

    /**
     * @param EntityManagerInterface $em
     * @param UserPasswordHasherInterface $hasher
     */
    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $hasher)
    {
        $this->em = $em;
        $this->hasher = $hasher;
    }


    #[Route('', name: 'app_profile')]
    public function index(): Response
    {
        $user = $this->getUser();
        if ($user==null){
            return $this->redirectToRoute('app_login');
        }
        return $this->render('profile/index.html.twig', [
          'user'=>$user,
            'montant'=>$user->getWallet()->getSolde()
        ]);
    }

    #[Route('/update/photo', name: 'update_photo')]
    public function updatePhoto(Request $request)
    {
        if (!empty($request->files->get("image"))){
            $img=$request->files->get("image");
            $imageName=uniqid().'.'.$img->guessExtension();
            $img->move($this->getParameter("profile"),$imageName);
            $this->getUser()->setProfileImage($imageName);
            $this->em->persist($this->getUser());
            $this->em->flush();
        }
        return $this->redirectToRoute('app_profile');
    }

    #[Route('/update/profile', name: 'update_profil')]
    public function updateProfil (Request $request)
    {
        $data = $request->request;
        $user=$this->getUser();
        $user->setNom($data->get('nom'));
        $user->setPrenom($data->get('prenom'));
        $user->setTelephone($data->get('telephone'));
        $user->setEmail($data->get('email'));
        $user-> setPassword($this->hasher->hashPassword($user, $data->get('password')));
        $this->em->persist($user);
        $this->em->flush();
        return $this->redirectToRoute('app_profile');
    }


}
