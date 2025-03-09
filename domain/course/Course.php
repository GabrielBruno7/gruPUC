<?php

namespace domain\course;

class Course
{
    private string $id;
    private string $name;
    private string $type;
    private int $period;
    private ?CoursePersistenceInterface $persistence = null;

    public function __construct(CoursePersistenceInterface $persistence)
    {
        $this->persistence = $persistence;
    }

    public function getPersistence(): CoursePersistenceInterface
    {
        return $this->persistence;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setPeriod(int $period): self
    {
        $this->period = $period;

        return $this;
    }

    public function getPeriod(): int
    {
        return $this->period;
    }

    public function loadById(): Course
    {
        if (!$this->getPersistence()->loadById($this)) {
            throw new \Exception("Erro ao carregar o curso.");
        }
    
        return $this;
    }    
}

