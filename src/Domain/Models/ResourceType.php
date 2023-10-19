<?php

declare(strict_types=1);

namespace RavineRbac\Domain\Models;

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