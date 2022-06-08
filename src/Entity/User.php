<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @UniqueEntity("email", message="Cette email est déjà utilisé")
 * @UniqueEntity("username", message="Ce nom d'utilisateur est déjà utilisé")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Groups("getUser", "getParticipation", "getSubscriber", "getPost", "getComment", "getEvent")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="L'email est obligatoire")
     * @Assert\Email(message="Email format invalide")
     * @Serializer\Groups("list", "getUser")
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @Serializer\Groups("list", "getUser")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Le mot de passe est obligatoire")
     * @Assert\Length(
     *     min=8,
     *     max=20,
     *     minMessage="Votre mot de passe doit être au moins de {{ limit }} caractères",
     *     maxMessage="Votre mot de passe ne peut pas dépasser les {{ limit }} caractères"
     * , groups={"editUser"}
     * )
     * @Assert\Regex(
     *     pattern="#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W)#",
     *     match=true,
     *     message="Les mots de passe doivent contenir au moins 8 caractères et contenir au moins une des catégories suivantes : majuscules, minuscules, chiffres et symboles."
     * )
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Votre prénom est obligatoire", groups={"editUser"})
     * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="Votre prénom ne peut pas contenir de chiffre"
     * , groups={"editUser"}
     * )
     * @Serializer\Groups("getUser", "getParticipation", "getSubscriber", "getPost", "getComment", "getEvent")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Votre nom est obligatoire", groups={"editUser"})
     * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="Votre nom ne peut pas contenir de chiffre"
     * , groups={"editUser"}
     * )
     * @Serializer\Groups("getUser", "getParticipation", "getSubscriber", "getPost", "getComment", "getEvent")
     */
    private $lastName;

    /**
     * @ORM\Column(type="date")
     * @Serializer\Groups("list", "getUser")
     */
    private $birthday;

    /**
     * @ORM\Column(type="date")
     * @Serializer\Groups("list", "getUser")
     */
    private $promo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Groups("getUser", "getParticipation", "getSubscriber", "getPost", "getComment", "getEvent")
     */
    private $biography;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Serializer\Groups("getUser", "getParticipation", "getSubscriber", "getPost", "getComment", "getEvent")
     */
    private $urlProfilePicture;

    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="author", orphanRemoval=true)
     */
    private $posts;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="author", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity=LikePost::class, mappedBy="likeBy", orphanRemoval=true)
     */
    private $likePosts;

    /**
     * @ORM\Column(type="boolean")
     */
    private $acceptAccount;

    /**
     * @ORM\OneToMany(targetEntity=Event::class, mappedBy="author", orphanRemoval=true)
     */
    private $events;

    /**
     * @ORM\OneToMany(targetEntity=Participate::class, mappedBy="participant", orphanRemoval=true)
     */
    private $participates;

    /**
     * @ORM\OneToMany(targetEntity=Subscribe::class, mappedBy="subscription", orphanRemoval=true)
     */
    private $subscribes;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="sentBy", orphanRemoval=true)
     */
    private $messages;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le nom d'utilisateur est obligatoire")
     */
    private $username;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->likePosts = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->participates = new ArrayCollection();
        $this->subscribes = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function getUsername(): string
    {
        return (string) $this->email;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getPromo(): ?\DateTimeInterface
    {
        return $this->promo;
    }

    public function setPromo(\DateTimeInterface $promo): self
    {
        $this->promo = $promo;

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setAuthor($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getAuthor() === $this) {
                $post->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LikePost>
     */
    public function getLikePosts(): Collection
    {
        return $this->likePosts;
    }

    public function addLikePost(LikePost $likePost): self
    {
        if (!$this->likePosts->contains($likePost)) {
            $this->likePosts[] = $likePost;
            $likePost->setUsers($this);
        }

        return $this;
    }

    public function removeLikePost(LikePost $likePost): self
    {
        if ($this->likePosts->removeElement($likePost)) {
            // set the owning side to null (unless already changed)
            if ($likePost->getUsers() === $this) {
                $likePost->setUsers(null);
            }
        }

        return $this;
    }

    public function getAcceptAccount(): ?bool
    {
        return $this->acceptAccount;
    }

    public function setAcceptAccount(bool $acceptAccount): self
    {
        $this->acceptAccount = $acceptAccount;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->setAuthor($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getAuthor() === $this) {
                $event->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Participate>
     */
    public function getParticipates(): Collection
    {
        return $this->participates;
    }

    public function addParticipate(Participate $participate): self
    {
        if (!$this->participates->contains($participate)) {
            $this->participates[] = $participate;
            $participate->setParticipant($this);
        }

        return $this;
    }

    public function removeParticipate(Participate $participate): self
    {
        if ($this->participates->removeElement($participate)) {
            // set the owning side to null (unless already changed)
            if ($participate->getParticipant() === $this) {
                $participate->setParticipant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Subscribe>
     */
    public function getSubscribes(): Collection
    {
        return $this->subscribes;
    }

    public function addSubscribe(Subscribe $subscribe): self
    {
        if (!$this->subscribes->contains($subscribe)) {
            $this->subscribes[] = $subscribe;
            $subscribe->setSubscription($this);
        }

        return $this;
    }

    public function removeSubscribe(Subscribe $subscribe): self
    {
        if ($this->subscribes->removeElement($subscribe)) {
            // set the owning side to null (unless already changed)
            if ($subscribe->getSubscription() === $this) {
                $subscribe->setSubscription(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setSentBy($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getSentBy() === $this) {
                $message->setSentBy(null);
            }
        }

        return $this;
    }

    public function getBiography(): ?string
    {
        return $this->biography;
    }

    public function setBiography(?string $biography): self
    {
        $this->biography = $biography;

        return $this;
    }

    public function getUrlProfilePicture(): ?string
    {
        return $this->urlProfilePicture;
    }

    public function setUrlProfilePicture(?string $urlProfilePicture): self
    {
        $this->urlProfilePicture = $urlProfilePicture;

        return $this;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }
}
