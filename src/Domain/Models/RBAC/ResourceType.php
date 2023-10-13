<?php
namespace RavineRbac\Domain\Models\RBAC;

use DateTimeImmutable;
use DateTimeInterface;

class ResourceType
{
    public DateTimeInterface $createdAt;
    public DateTimeInterface $updatedAt;
    public function __construct(
        public string $name,
        public string $description,
        public bool $isActive = true
    ) {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }
}