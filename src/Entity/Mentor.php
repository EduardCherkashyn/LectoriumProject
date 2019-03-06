<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-02-22
 * Time: 12:38
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\UserBaseClass;
use Doctrine\ORM\Mapping\Entity;

/** @Entity */
class Mentor extends UserBaseClass implements \JsonSerializable
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Course", inversedBy="mentors")
     * @ORM\JoinColumn(nullable=true)
     */
    private $course;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Video", mappedBy="mentor")
     */
    private $videos;


    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->videos = new ArrayCollection();
    }

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): self
    {
        $this->course = $course;

        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'apiToken' => $this->getApiToken(),
            'messages' => $this->getMessages()
        ];
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
            $video->setMentor($this);
        }

        return $this;
    }

    public function removeVideo(Video $video): self
    {
        if ($this->videos->contains($video)) {
            $this->videos->removeElement($video);
            // set the owning side to null (unless already changed)
            if ($video->getMentor() === $this) {
                $video->setMentor(null);
            }
        }

        return $this;
    }
}
