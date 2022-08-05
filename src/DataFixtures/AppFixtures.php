<?php

namespace App\DataFixtures;

use App\Entity\Client;
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
        // $product = new Product();
        // $manager->persist($product);
        $client = new Client();
        $client -> setEmail('winigajordan@gmailcom');
        $client -> setPassword($this->hasher->hashPassword($client, '1234'));
        $client -> setTelephone('772570206');
        $client -> setNom('REMA');
        $client -> setPrenom('Winiga-Jordane');
        $manager->persist($client);
        
        $manager->flush();
    }
}
