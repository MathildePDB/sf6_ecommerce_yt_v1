<?php

namespace App\DataFixtures;

use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Faker;

class UsersFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordEncoder, private SluggerInterface $slugger)
    {
    }

    public function load(ObjectManager $manager): void
    {
        // création de l'admin unique
        $admin = new Users();
        $admin->setEmail('admin@demo.fr');
        $admin->setLastname('Pierre');
        $admin->setFirstname('Mathilde');
        $admin->setAddress('1 rue de la rue');
        $admin->setZipcode('75001');
        $admin->setCity('Paris');
        $admin->setPassword($this->passwordEncoder->hashPassword($admin, 'admin'));
        $admin->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        // fausses données en français avec Faker
        $faker = Faker\Factory::create('fr_FR');

        for ($usr = 1; $usr <= 5; $usr++) {
            $user = new Users();
            $user->setEmail($faker->email);
            $user->setLastname($faker->lastname);
            $user->setFirstname($faker->firstname);
            $user->setAddress($faker->streetAddress);
            $user->setZipcode(str_replace(' ','', $faker->postcode));
            $user->setCity($faker->city);
            $user->setPassword($this->passwordEncoder->hashPassword($user, 'secret'));
            
            $manager->persist($user);
        }

        $manager->flush();
    }
}
