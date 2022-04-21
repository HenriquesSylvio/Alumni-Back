<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use Faker;

class UserFixtures extends Fixture
{
    const DEFAULT_USER = ['email' => 'test@test.fr', 'password' => 'password', 'first_name' => 'test', 'last_name' => 'test', 'birthday' => '25-09-1999', 'promo' => '25-09-1999'];

    private UserPasswordHasherInterface  $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager) : void
    {

        $user = new User();
        $user->setEmail("henriques.sylvio@outlook.fr");
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->passwordHasher->hashPassword($user, '54875487'));
        $user->setLastName("Henriques");
        $user->setFirstname("Sylvio");
        $user->setBirthday(new \DateTime(1999-9-25));
        $user->setPromo(new \DateTime(2021-9-01));
        $user->setAcceptAccount(true);
        $manager->persist($user);

        $user = new User();
        $user->setEmail("admin@outlook.fr");
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->passwordHasher->hashPassword($user, '54875487'));
        $user->setLastName("admin");
        $user->setFirstname("admin");
        $user->setBirthday(new \DateTime(1999-9-25));
        $user->setPromo(new \DateTime(2021-9-01));
        $user->setAcceptAccount(true);
        $manager->persist($user);

        $user = new User();
        $user->setEmail("user@outlook.fr");
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher->hashPassword($user, '54875487'));
        $user->setLastName("Henriques");
        $user->setFirstname("Sylvio");
        $user->setBirthday(new \DateTime(1999-9-25));
        $user->setPromo(new \DateTime(2021-9-01));
        $user->setAcceptAccount(true);
        $manager->persist($user);
        $user->setAcceptAccount(true);

        $faker = Faker\Factory::create('fr_FR');
        for($nbUsers = 1; $nbUsers <= 30; $nbUsers++){
            $user = new User();
            $user->setEmail($faker->email);
            if($nbUsers === 1)
            {
                $user->setRoles(['ROLE_ADMIN']);
                $user->setAcceptAccount(true);
            } else {
                $user->setRoles(['ROLE_USER']);
                $user->setAcceptAccount(false);
            }
            $user->setPassword($this->passwordHasher->hashPassword($user, '54875487'));
            $user->setLastName($faker->lastName);
            $user->setFirstname($faker->firstName);
            $user->setBirthday(new \DateTime(1999-9-25));
            $user->setPromo(new \DateTime(2021-9-01));
            $manager->persist($user);
        }

        $manager->flush();
    }
}