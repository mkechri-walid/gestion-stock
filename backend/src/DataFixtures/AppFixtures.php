<?php

namespace App\DataFixtures;

use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // 1. Création de l'ADMIN
        $admin = new Utilisateur();
        $admin->setEmail('admin@admin.com');
        $admin->setNom('Mkechri');
        $admin->setPrenom('Walid');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setDateCreation(new \DateTimeImmutable());
        
        $password = $this->hasher->hashPassword($admin, 'admin123');
        $admin->setPassword($password);
        $manager->persist($admin);

        // 2. Création de l'EMPLOYÉ (Utilisateur normal)
        $employe = new Utilisateur();
        $employe->setEmail('employe@exemple.com');
        $employe->setNom('Dupont');
        $employe->setPrenom('Jean');
        $employe->setRoles(['ROLE_USER']);
        $employe->setDateCreation(new \DateTimeImmutable());
        
        $password = $this->hasher->hashPassword($employe, 'user123');
        $employe->setPassword($password);
        $manager->persist($employe);

        $manager->flush();
    }
}
