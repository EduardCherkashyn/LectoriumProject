<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CourseRepository")
 */
class Course
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=2,max=15)
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $year;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Mentor", mappedBy="course")
     */
    private $mentors;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Student", mappedBy="course")
     */
    private $students;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Plan", mappedBy="course", cascade={"persist", "remove"})
     */
    private $plan;

    public function __construct()
    {
        $this->mentors = new ArrayCollection();
        $this->students = new ArrayCollection();
        $this->year = new \DateTime();
        $this->setPlan(new Plan());
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

    public function getYear(): ?\DateTimeInterface
    {
        return $this->year;
    }

    public function setYear(\DateTimeInterface $year): self
    {
        $this->year = $year;

        return $this;
    }

    /**
     * @return Collection|Mentor[]
     */
    public function getMentors(): Collection
    {
        return $this->mentors;
    }

    public function addMentor(Mentor $mentor): self
    {
        if (!$this->mentors->contains($mentor)) {
            $this->mentors[] = $mentor;
            $mentor->setCourse($this);
        }

        return $this;
    }

    public function removeMentor(Mentor $mentor): self
    {
        if ($this->mentors->contains($mentor)) {
            $this->mentors->removeElement($mentor);
            // set the owning side to null (unless already changed)
            if ($mentor->getCourse() === $this) {
                $mentor->setCourse(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Student[]
     */
    public function getStudents(): Collection
    {
        return $this->students;
    }

    public function addStudent(Student $student): self
    {
        if (!$this->students->contains($student)) {
            $this->students[] = $student;
            $student->setCourse($this);
        }

        return $this;
    }

    public function removeStudent(Student $student): self
    {
        if ($this->students->contains($student)) {
            $this->students->removeElement($student);
            // set the owning side to null (unless already changed)
            if ($student->getCourse() === $this) {
                $student->setCourse(null);
            }
        }

        return $this;
    }

    public function getPlan(): ?Plan
    {
        return $this->plan;
    }

    public function setPlan(?Plan $plan): self
    {
        $this->plan = $plan;

        // set (or unset) the owning side of the relation if necessary
        $newCourse = $plan === null ? null : $this;
        if ($newCourse !== $plan->getCourse()) {
            $plan->setCourse($newCourse);
        }

        return $this;
    }
}
