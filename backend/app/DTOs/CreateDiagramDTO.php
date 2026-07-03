<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Enums\DbType;
use App\Enums\DiagramAccess;

readonly class CreateDiagramDTO
{
    public function __construct(
        public string $name,
        public int $userId,
        public DbType $dbType = DbType::MYSQL,
        public ?DiagramAccess $shareAccess = null,
        public bool $library = false,
        public mixed $schema = null,
    ) {}
}
