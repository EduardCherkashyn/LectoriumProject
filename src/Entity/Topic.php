<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TopicRepository")
 */
class Topic
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Plan", inversedBy="topics")
     * @ORM\JoinColumn(nullable=false)
     */
    private $plan;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\HomeTask", mappedBy="topic")
     */
    private $homeTasks;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Video", mappedBy="topic")
     */
    private $videos;

    public function __construct()
    {
        $this->homeTasks = new ArrayCollection();
        $this->videos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPlan(): ?Plan
    {
        return $this->plan;
    }

    public function setPlan(?Plan $plan): self
    {
        $this->plan = $plan;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(?int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|HomeTask[]
     */
    public function getHomeTasks(): Collection
    {
        return $this->homeTasks;
    }

    public function addHomeTask(HomeTask $homeTask): self
    {
        if (!$this->homeTasks->contains($homeTask)) {
            $this->homeTasks[] = $homeTask;
            $homeTask->setTopic($this);
        }

        return $this;
    }

    public function removeHomeTask(HomeTask $homeTask): self
    {
        if ($this->homeTasks->contains($homeTask)) {
            $this->homeTasks->removeElement($homeTask);
            // set the owning side to null (unless already changed)
            if ($homeTask->getTopic() === $this) {
                $homeTask->setTopic(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Video[]
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    public function addVideo(Video $video): self
    {
        if (!$this->videos->contains($video)) {
            $this->videos[] = $video;
            $video->setTopic($this);
        }

        return $this;
    }

    public function removeVideo(Video $video): self
    {
        if ($this->videos->contains($video)) {
            $this->videos->removeElement($video);
            // set the owning side to null (unless already changed)
            if ($video->getTopic() === $this) {
                $video->setTopic(null);
            }
        }

        return $this;
    }
}
