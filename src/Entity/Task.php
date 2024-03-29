<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::STRING, length: 80)]
    #[Assert\Length(
        min: 3, max: 80,
        minMessage: 'Le titre de la tâche doit faire au moins {{ limit }} caractères',
        maxMessage: 'Le titre de la tâche ne peut faire plus de {{ limit }} caractères')
    ]
    private string $title;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\NotBlank(message: 'Vous devez saisir du contenu.')]
    private string $content;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $done;

    #[ORM\ManyToOne(targetEntity: User::class, fetch: 'EAGER', inversedBy: 'tasks')]
    #[ORM\JoinColumn(referencedColumnName: 'id', nullable: false)]
    private User $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function toggle(bool $flag): self
    {
        $this->done = $flag;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    // Used by fixtures tests
    public function setDone(bool $done): self
    {
        $this->done = $done;

        return $this;
    }

    public function isDone(): bool
    {
        return $this->done;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Task
    {
        $this->user = $user;
        $user->addTask($this);

        return $this;
    }
}
