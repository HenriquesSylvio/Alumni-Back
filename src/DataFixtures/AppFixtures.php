<?php

namespace App\DataFixtures;

use App\Entity\Event;
use App\Entity\Faculty;
use App\Entity\Job;
use App\Entity\LikePost;
use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use Faker;

class AppFixtures extends Fixture
{
    const DEFAULT_USER = ['email' => 'test@test.fr', 'password' => 'password', 'first_name' => 'test', 'last_name' => 'test', 'promo' => '2017', 'faculty_id' => '1'];

    private UserPasswordHasherInterface  $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager) : void
    {
        $faculty = new Faculty();
        $faculty->setName('Développement web');
        $manager->persist($faculty);

        $faculty1 = new Faculty();
        $faculty1->setName('Communication Graphique');
        $manager->persist($faculty1);

        $faculty2 = new Faculty();
        $faculty2->setName('Web Marketing');
        $manager->persist($faculty2);

        $faculty3 = new Faculty();
        $faculty3->setName('Community Management');
        $manager->persist($faculty3);

        $faculty4 = new Faculty();
        $faculty4->setName('Autre');
        $manager->persist($faculty4);

        $facultyArray = array($faculty, $faculty1, $faculty2, $faculty3);

        $user = new User();
        $user->setEmail('henriques.sylvio@outlook.fr');
        $user->setUsername('henriques.sylvio');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->passwordHasher->hashPassword($user, '54875487'));
        $user->setLastName('Henriques');
        $user->setFirstname('Sylvio');
        $user->setPromo(2017);
        $user->setAcceptAccount(true);
        $user->setBiography('test');
        $user->setUrlProfilePicture('test');
        $user->setFaculty($faculty4);
        $manager->persist($user);

        $post = new Post();
        $post->setContent('Ceci est un test');
        $post->setCreateAt(new \DateTime(date("d-m-Y")));
        $post->setAuthor($user);
        $user->setBiography('test');
        $user->setUrlProfilePicture('test');
        $manager->persist($post);

        $event = new Event();
        $event->setTitle('Ceci est un test');
        $event->setDescription('Ceci est un test');
        $event->setDate(new \DateTime("2022-7-9"));
        $event->setAuthor($user);
        $user->setBiography('test');
        $user->setUrlProfilePicture('test');
        $manager->persist($event);

        $job = new Job();
        $job->setTitle('Ceci est un titre test');
        $job->setDescription('Ceci est un test');
        $job->setCity('Rouen');
        $job->setCompany('Normandie Web School');
        $job->setAuthor($user);
        $job->setCreateAt(new \DateTime(date("d-m-Y")));
        $job->setCompensation('2000€ net/mois');
        $job->setFaculty($faculty);
        $manager->persist($job);

        $user = new User();
        $user->setEmail('admin@outlook.fr');
        $user->setUsername('admin');
        $user->setRoles(['ROLE_SUPER_ADMIN']);
        $user->setPassword($this->passwordHasher->hashPassword($user, '54875487'));
        $user->setLastName('admin');
        $user->setFirstname('admin');
        $user->setPromo(2017);
        $user->setAcceptAccount(true);
        $user->setFaculty($faculty4);
        $manager->persist($user);

        $postPrincipal = new Post();
        $postPrincipal->setContent('Ceci est un test');
        $postPrincipal->setCreateAt(new \DateTime(2022-4-21));
        $postPrincipal->setAuthor($user);
        $manager->persist($postPrincipal);

        $jobPrincipal = new Job();
        $jobPrincipal->setTitle('Ceci est un titre test');
        $jobPrincipal->setDescription('Ceci est un test');
        $jobPrincipal->setCity('Rouen');
        $jobPrincipal->setCompany('Normandie Web School');
        $jobPrincipal->setAuthor($user);
        $jobPrincipal->setCreateAt(new \DateTime(date("d-m-Y")));
        $jobPrincipal->setCompensation('2000€ net/mois');
        $jobPrincipal->setFaculty($faculty1);
        $manager->persist($jobPrincipal);

        for($nbPosts = 1; $nbPosts <= 30; $nbPosts++){
            $post = new Post();
            $post->setContent('Ceci est un test');
            $post->setCreateAt(new \DateTime(date("d-m-Y")));
            $post->setAuthor($user);
            $manager->persist($post);
        }

        for($nbComments = 1; $nbComments <= 30; $nbComments++){
            $comment = new Post();
            $comment->setContent('Ceci est un test');
            $comment->setCreateAt(new \DateTime(date("d-m-Y")));
            $comment->setAuthor($user);
            $comment->setMainPost($post);
            $comment->setParentPost($postPrincipal);
            $manager->persist($comment);
        }

        for($nbEvent = 1; $nbEvent <= 30; $nbEvent++){
            $event = new Event();
            $event->setTitle('Ceci est un test');
            $event->setDescription('Ceci est un test');
            $event->setDate(new \DateTime("2022-7-9"));
            $event->setAuthor($user);
            $manager->persist($event);
        }

        $user = new User();
        $user->setEmail('user@outlook.fr');
        $user->setUsername('user');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher->hashPassword($user, '54875487'));
        $user->setLastName('Henriques');
        $user->setFirstname('Sylvio');
        $user->setPromo(2017);
        $user->setAcceptAccount(true);
        $user->setFaculty($faculty);
        $manager->persist($user);


        for($nbPosts = 1; $nbPosts <= 30; $nbPosts++){
            $post = new Post();
            $post->setContent('Ceci est un test');
            $post->setCreateAt(new \DateTime(date("d-m-Y")));
            $post->setAuthor($user);
            $manager->persist($post);
        }

        for($nbComments = 1; $nbComments <= 30; $nbComments++){
            $comment = new Post();
            $comment->setContent('Ceci est un test');
            $comment->setCreateAt(new \DateTime(date("d-m-Y")));
            $comment->setAuthor($user);
            $comment->setMainPost($postPrincipal);
            $comment->setParentPost($postPrincipal);
            $manager->persist($post);
        }

        for($nbEvent = 1; $nbEvent <= 30; $nbEvent++){
            $event = new Event();
            $event->setTitle('Ceci est un test');
            $event->setDescription('Ceci est un test');
            $event->setDate(new \DateTime("2022-7-9"));
            $event->setAuthor($user);
            $manager->persist($event);
        }

        $user->setAcceptAccount(true);

        $faker = Faker\Factory::create('fr_FR');
        for($nbUsers = 1; $nbUsers <= 30; $nbUsers++){
            $user = new User();
            $user->setEmail($faker->email);
            $user->setUsername($faker->userName);
            if($nbUsers === 1)
            {
                $user->setRoles(['ROLE_ADMIN']);
                $user->setAcceptAccount(true);
                $user->setFaculty($faculty4);
            } else {
                $user->setRoles(['ROLE_USER']);
                $user->setAcceptAccount(false);
                $user->setFaculty($facultyArray[rand(0, count($facultyArray) - 1)]);
            }
            $user->setPassword($this->passwordHasher->hashPassword($user, '54875487'));
            $user->setLastName($faker->lastName);
            $user->setFirstname($faker->firstName);
            $user->setPromo(2017);
            $manager->persist($user);
            $post = new Post();
            $post->setContent('Ceci est un test');
            $post->setCreateAt(new \DateTime(date("d-m-Y")));
            $post->setAuthor($user);
            $manager->persist($post);
            $likePost = new LikePost();
            $likePost->setUsers($user);
            $likePost->setPost($postPrincipal);
            $manager->persist($likePost);
            $comment = new Post();
            $comment->setContent('Ceci est un test');
            $comment->setCreateAt(new \DateTime(date("d-m-Y")));
            $comment->setAuthor($user);
            $comment->setMainPost($postPrincipal);
            $comment->setParentPost($postPrincipal);
            $manager->persist($comment);
            $event = new Event();
            $event->setTitle('Ceci est un test');
            $event->setDescription('Ceci est un test');
            $event->setDate(new \DateTime("2022-7-9"));
            $event->setAuthor($user);
            $manager->persist($event);
            $job = new Job();
            $job->setTitle('Titre test');
            $job->setDescription('Ceci est un test');
            $job->setCity('Rouen');
            $job->setCompany('Normandie Web School');
            $job->setAuthor($user);
            $job->setCreateAt(new \DateTime(date("d-m-Y")));
            $job->setCompensation('2000€ net/mois');
            $job->setFaculty($facultyArray[rand(0, count($facultyArray) - 1)]);
            $manager->persist($job);
        }
        $manager->flush();
    }
}