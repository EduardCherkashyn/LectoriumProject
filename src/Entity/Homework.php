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
     * @ORM\Column(type="string", length=255, nullable=true)
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
}
