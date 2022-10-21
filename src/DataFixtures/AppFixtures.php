<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Client;
use App\Entity\Wallet;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }
    
    public function load(ObjectManager $manager): void
    {
        // ajout d'un client
        $client = new Client();
        //ne modifiez pas le mail de ce compte car cest sur ce compte que sont envoyé les frais de vente des autres
        $client -> setEmail('admin@wallet.com');
        $client -> setPassword($this->hasher->hashPassword($client, '1234'));
        $client -> setTelephone('+221753374211');
        $client -> setNom('Admin');
        $client -> setPrenom('Jabba');
        $manager->persist($client);
        $wallet = (new Wallet())
        ->setCompte($client)
        ->setEtat("VALIDE")
        ->setSolde(0);
        

        //ajout de l'admin 
        $admin = new Admin();
        $admin->setNom("Admin");
        $admin->setPrenom("Admin");
        $admin->setEmail("jabba@admin.com");
        $admin->setPassword($this->hasher->hashPassword($client, '1234'));
        $manager->persist($wallet);
        $manager->persist($admin);

        $manager->flush();
    }
}
