<?php
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity]
#[ORM\Table(name: 'section')]
class Section
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int|null $id = null;

    #[ORM\Column(type: 'string')]
    private string $description;

    //line 4: collection of students (reference)
    #[ORM\JoinTable(name: 'section_student')]
    #[ORM\JoinColumn(name: 'section_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'student_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: Student::class)]
    private Collection $student;

    #[ORM\ManyToOne(targetEntity: Mirrortypesection::class, inversedBy:"section")]
    #[ORM\JoinColumn(name: 'mirrortypesection', referencedColumnName: 'id')]
    private Mirrortypesection|null $type = null;
    
    public function id (): int
    {
        return $this->id; 
    }

    public function description (): string
    {
        return $this->description; 
    }
    public function setDescription (string $description): void
    {
        $this -> description = $description; 
    }


    public function getStudent(): Collection
    {
        return $this->student; 
    }

    public function clearStudent(): void
    {
        $this->student->clear();
    }

    public function setStudent(Student $student): void
    {
        $this->student->add($student);
    }

    public function Type():Mirrortypesection
    {
        return $this->type; 
    }
    public function setType(Mirrortypesection $type): void
    {
        $this->type = $type;
    }

    public function setDestination(Student $student): void
    {
        if (!$this->student->contains($student)) {
            $this->student->add($student);
        }
    }

    public function removeStudent($student,$deleteStudent)
    {
        foreach ($student as $student) {
            if ($this->student->contains($deleteStudent)) {
                $this->student->removeElement($deleteStudent);
            }
        }
       return $student;
    } 

    //to initialize collection, collection to array (line 23) 
    public function __construct()
    {
        $this->student = new ArrayCollection();
    }


}

#[ORM\Entity]
#[ORM\Table(name: 'student')]
class Student
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int|null $id = null;

    #[ORM\Column(type: 'string')]
    private string $fname;

    #[ORM\Column(type: 'string')]
    private string $lname;

    //connection of m2m from section
    #[ManyToMany(targetEntity: Section::class, mappedBy: 'section')]
    private Collection $section;

    

    public function id (): int
    {
        return $this->id; 
    }

    public function fname (): string
    {
        return $this->fname; 
    }
    public function setfname (string $fname): void
    {
        $this -> fname = $fname; 
    }

    public function lname (): string
    {
        return $this->lname; 
    }
    public function setlname (string $lname): void
    {
        $this -> lname = $lname; 
    }
}

#[ORM\Entity]
#[ORM\Table(name: 'mirrortypesection')]
class Mirrortypesection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int|null $id = null;

    #[ORM\Column(type: 'string')]
    private string $description;

    #[ManyToMany(targetEntity: Section::class, mappedBy: 'section')]
    private Collection $section;

    public function id (): int
    {
        return $this->id; 
    }

    public function description (): string
    {
        return $this->description; 
    }
    public function setDescription (string $description): void
    {
        $this -> description = $description; 
    }

}