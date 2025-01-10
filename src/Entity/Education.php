<?php

namespace App\Entity;

use App\Repository\EducationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EducationRepository::class)]
class Education
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    private ?string $flow = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(length: 50)]
    private ?string $inopolis_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $competence_level = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $competence_grow_level = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $result = null;

    #[ORM\Column(nullable: true)]
    private ?int $attempts = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $result_attempt_time = null;

    #[ORM\Column]
    private ?int $stage = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $external_proctoring = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $internal_proctoring = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $proctoring_photo = null;

    #[ORM\ManyToOne(inversedBy: 'education')]
    private ?Student $student = null;

    #[ORM\ManyToOne(inversedBy: 'education')]
    private ?Competence $competence = null;

    #[ORM\ManyToOne(inversedBy: 'education')]
    private ?Program $program = null;

    #[ORM\ManyToOne(inversedBy: 'education')]
    private ?Direction $direction = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $layout_date = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getFlow(): ?string
    {
        return $this->flow;
    }

    public function setFlow(string $flow): static
    {
        $this->flow = $flow;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getInopolisId(): ?string
    {
        return $this->inopolis_id;
    }

    public function setInopolisId(string $inopolis_id): static
    {
        $this->inopolis_id = $inopolis_id;

        return $this;
    }

    public function getCompetenceLevel(): ?string
    {
        return $this->competence_level;
    }

    public function setCompetenceLevel(?string $competence_level): static
    {
        $this->competence_level = $competence_level;

        return $this;
    }

    public function getCompetenceGrowLevel(): ?string
    {
        return $this->competence_grow_level;
    }

    public function setCompetenceGrowLevel(?string $competence_grow_level): static
    {
        $this->competence_grow_level = $competence_grow_level;

        return $this;
    }

    public function getResult(): ?string
    {
        return $this->result;
    }

    public function setResult(?string $result): static
    {
        $this->result = $result;

        return $this;
    }

    public function getAttempts(): ?int
    {
        return $this->attempts;
    }

    public function setAttempts(?int $attempts): static
    {
        $this->attempts = $attempts;

        return $this;
    }

    public function getResultAttemptTime(): ?string
    {
        return $this->result_attempt_time;
    }

    public function setResultAttemptTime(?string $result_attempt_time): static
    {
        $this->result_attempt_time = $result_attempt_time;

        return $this;
    }

    public function getStage(): ?int
    {
        return $this->stage;
    }

    public function setStage(int $stage): static
    {
        $this->stage = $stage;

        return $this;
    }

    public function getExternalProctoring(): ?string
    {
        return $this->external_proctoring;
    }

    public function setExternalProctoring(?string $external_proctoring): static
    {
        $this->external_proctoring = $external_proctoring;

        return $this;
    }

    public function getInternalProctoring(): ?string
    {
        return $this->internal_proctoring;
    }

    public function setInternalProctoring(?string $internal_proctoring): static
    {
        $this->internal_proctoring = $internal_proctoring;

        return $this;
    }

    public function getProctoringPhoto(): ?string
    {
        return $this->proctoring_photo;
    }

    public function setProctoringPhoto(?string $proctoring_photo): static
    {
        $this->proctoring_photo = $proctoring_photo;

        return $this;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): static
    {
        $this->student = $student;

        return $this;
    }

    public function getCompetence(): ?Competence
    {
        return $this->competence;
    }

    public function setCompetence(?Competence $competence): static
    {
        $this->competence = $competence;

        return $this;
    }

    public function getProgram(): ?Program
    {
        return $this->program;
    }

    public function setProgram(?Program $program): static
    {
        $this->program = $program;

        return $this;
    }

    public function getDirection(): ?Direction
    {
        return $this->direction;
    }

    public function setDirection(?Direction $direction): static
    {
        $this->direction = $direction;

        return $this;
    }

    public function getLayoutDate(): ?\DateTimeInterface
    {
        return $this->layout_date;
    }

    public function setLayoutDate(?\DateTimeInterface $layout_date): static
    {
        $this->layout_date = $layout_date;

        return $this;
    }
}
