<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Event;
use App\Entity\LikePost;
use App\Entity\Post;
use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use Faker;

class AppFixtures extends Fixture
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
        $user->setEmail('henriques.sylvio@outlook.fr');
        $user->setUsername('henriques.sylvio');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->passwordHasher->hashPassword($user, '54875487'));
        $user->setLastName("Henriques");
        $user->setFirstname("Sylvio");
        $user->setBirthday(new \DateTime(1999-9-25));
        $user->setPromo(new \DateTime(2021-9-01));
        $user->setAcceptAccount(true);
        $user->setBiography("test");
        $user->setUrlProfilePicture("test");
        $manager->persist($user);

        $tag = new Tag();
        $tag->setLabel("Offre d'emploi");
        $manager->persist($tag);

        $post = new Post();
        $post->setContent("Ceci est un test");
        $post->setCreateAt(new \DateTime(2022-4-21));
        $post->setAuthor($user);
        $post->setTag($tag);
        $user->setBiography("test");
        $user->setUrlProfilePicture("test");
        $manager->persist($post);

        $event = new Event();
        $event->setTitle("Ceci est un test");
        $event->setDescription("Ceci est un test");
        $event->setDate(new \DateTime(2022-4-21));
        $event->setAuthor($user);
        $user->setBiography("test");
        $user->setUrlProfilePicture("test");
        $manager->persist($event);

        $user = new User();
        $user->setEmail("admin@outlook.fr");
        $user->setUsername('admin');
        $user->setRoles(['ROLE_SUPER_ADMIN']);
        $user->setPassword($this->passwordHasher->hashPassword($user, '54875487'));
        $user->setLastName("admin");
        $user->setFirstname("admin");
        $user->setBirthday(new \DateTime(1999-9-25));
        $user->setPromo(new \DateTime(2021-9-01));
        $user->setAcceptAccount(true);
        $manager->persist($user);

        $postPrincipal = new Post();
        $postPrincipal->setContent("Ceci est un test");
        $postPrincipal->setCreateAt(new \DateTime(2022-4-21));
        $postPrincipal->setAuthor($user);
        $postPrincipal->setTag($tag);
        $manager->persist($postPrincipal);

        for($nbPosts = 1; $nbPosts <= 30; $nbPosts++){
            $post = new Post();
            $post->setContent("Ceci est un test");
            $post->setCreateAt(new \DateTime(2022-4-21));
            $post->setAuthor($user);
            $post->setTag($tag);
            $manager->persist($post);
        }

        for($nbComments = 1; $nbComments <= 30; $nbComments++){
            $comment = new Comment();
            $comment->setContent("Ceci est un test");
            $comment->setCreateAt(new \DateTime(2022-4-21));
            $comment->setAuthor($user);
            $comment->setPost($post);
            $manager->persist($comment);
        }

        for($nbEvent = 1; $nbEvent <= 30; $nbEvent++){
            $event = new Event();
            $event->setTitle("Ceci est un test");
            $event->setDescription("Ceci est un test");
            $event->setDate(new \DateTime(date("d-m-Y")));
            $event->setAuthor($user);
            $manager->persist($event);
        }

        $user = new User();
        $user->setEmail("user@outlook.fr");
        $user->setUsername('user');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher->hashPassword($user, '54875487'));
        $user->setLastName("Henriques");
        $user->setFirstname("Sylvio");
        $user->setBirthday(new \DateTime(1999-9-25));
        $user->setPromo(new \DateTime(2021-9-01));
        $user->setAcceptAccount(true);
        $manager->persist($user);


        for($nbPosts = 1; $nbPosts <= 30; $nbPosts++){
            $post = new Post();
            $post->setContent("Ceci est un test");
            $post->setCreateAt(new \DateTime(2022-4-21));
            $post->setAuthor($user);
            $post->setTag($tag);
            $manager->persist($post);
        }

        for($nbComments = 1; $nbComments <= 30; $nbComments++){
            $comment = new Comment();
            $comment->setContent("Ceci est un test");
            $comment->setCreateAt(new \DateTime(2022-4-21));
            $comment->setAuthor($user);
            $comment->setPost($post);
            $manager->persist($comment);
        }

        for($nbEvent = 1; $nbEvent <= 30; $nbEvent++){
            $event = new Event();
            $event->setTitle("Ceci est un test");
            $event->setDescription("Ceci est un test");
            $event->setDate(new \DateTime(2022-4-21));
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
            $post = new Post();
            $post->setContent("Ceci est un test");
            $post->setCreateAt(new \DateTime(2022-4-21));
            $post->setAuthor($user);
            $post->setTag($tag);
            $manager->persist($post);
            $likePost = new LikePost();
            $likePost->setUsers($user);
            $likePost->setPost($postPrincipal);
            $manager->persist($likePost);
            $comment = new Comment();
            $comment->setContent("Ceci est un test");
            $comment->setCreateAt(new \DateTime(2022-4-21));
            $comment->setAuthor($user);
            $comment->setPost($postPrincipal);
            $manager->persist($comment);
            $event = new Event();
            $event->setTitle("Ceci est un test");
            $event->setDescription("Ceci est un test");
            $event->setDate(new \DateTime(2022-4-21));
            $event->setAuthor($user);
            $manager->persist($event);
        }

        $manager->flush();
    }
}