<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-02-22
 * Time: 12:39
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;

/** @Entity */
class Student extends UserBaseClass
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Course", inversedBy="students")
     * @ORM\JoinColumn(nullable=true)
     */
    private $course;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Homework", mappedBy="student")
     */
    private $homeworks;

    public function __construct()
    {
        $this->homeworks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection|Homework[]
     */
    public function getHomeworks(): Collection
    {
        return $this->homeworks;
    }

    public function addHomework(Homework $homework): self
    {
        if (!$this->homeworks->contains($homework)) {
            $this->homeworks[] = $homework;
            $homework->setStudent($this);
        }

        return $this;
    }

    public function removeHomework(Homework $homework): self
    {
        if ($this->homeworks->contains($homework)) {
            $this->homeworks->removeElement($homework);
            // set the owning side to null (unless already changed)
            if ($homework->getStudent() === $this) {
                $homework->setStudent(null);
            }
        }

        return $this;
    }
}