<?php

namespace App\Entity;

use App\Repository\QuizRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: QuizRepository::class)]
class Quiz
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(groups:'id')]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $isVerified = null;

    #[ORM\Column(length: 255)]
    #[Groups(groups:'title')]
    private ?string $title = null;

    #[ORM\ManyToOne(inversedBy: 'quizzes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(groups:'category')]
    private ?Category $category = null;

    #[ORM\ManyToOne(inversedBy: 'quizzes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(groups:'level')]
    private ?Level $level = null;

    #[ORM\OneToMany(mappedBy: 'quiz', targetEntity: Question::class,cascade: ['persist'] )]
    private Collection $questions;

    #[ORM\OneToMany(mappedBy: 'quiz', targetEntity: Game::class,cascade: ['persist', 'remove'])]
    private Collection $games;

    #[ORM\ManyToOne(inversedBy: 'quizzes')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $userId = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'favoritesQuizzes')]
    private Collection $usersFavorites;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->games = new ArrayCollection();
        $this->usersFavorites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getLevel(): ?Level
    {
        return $this->level;
    }

    public function setLevel(?Level $level): static
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @return Collection<int, Question>
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): static
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
            $question->setQuiz($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): static
    {
        if ($this->questions->removeElement($question)) {
            // set the owning side to null (unless already changed)
            if ($question->getQuiz() === $this) {
                $question->setQuiz(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): static
    {
        if (!$this->games->contains($game)) {
            $this->games->add($game);
            $game->setQuiz($this);
        }

        return $this;
    }

    public function removeGame(Game $game): static
    {
        if ($this->games->removeElement($game)) {
            // set the owning side to null (unless already changed)
            if ($game->getQuiz() === $this) {
                $game->setQuiz(null);
            }
        }

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->userId;
    }

    public function setUserId(?User $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsersFavorites(): Collection
    {
        return $this->usersFavorites;
    }

    public function addUsersFavorite(User $usersFavorite): static
    {
        if (!$this->usersFavorites->contains($usersFavorite)) {
            $this->usersFavorites->add($usersFavorite);
        }

        return $this;
    }

    public function removeUsersFavorite(User $usersFavorite): static
    {
        $this->usersFavorites->removeElement($usersFavorite);

        return $this;
    }

    public function __toString()
    {
        return $this->getTitle();
    }
}
