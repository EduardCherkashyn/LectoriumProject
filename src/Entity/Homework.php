<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HomeworkRepository")
 */
class Homework
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Student", inversedBy="homeworks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $student;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $gitHubRepository;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\HomeTask", inversedBy="homeworks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $homeTask;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Task", mappedBy="homeWork")
     */
    private $tasks;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): self
    {
        $this->student = $student;

        return $this;
    }

    public function getGitHubRepository(): ?string
    {
        return $this->gitHubRepository;
    }

    public function setGitHubRepository(string $gitHubRepository): self
    {
        $this->gitHubRepository = $gitHubRepository;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getHomeTask(): ?HomeTask
    {
        return $this->homeTask;
    }

    public function setHomeTask(?HomeTask $homeTask): self
    {
        $this->homeTask = $homeTask;

        return $this;
    }

    /**
     * @return Collection|Task[]
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setHomeWork($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->contains($task)) {
            $this->tasks->removeElement($task);
            // set the owning side to null (unless already changed)
            if ($task->getHomeWork() === $this) {
                $task->setHomeWork(null);
            }
        }

        return $this;
    }
}
