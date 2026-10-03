<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Enums\DbType;
use App\Models\Diagram;
use App\Services\SchemaDoctorService;
use PHPUnit\Framework\TestCase;

class SchemaDoctorServiceTest extends TestCase
{
    public function test_it_reports_structural_schema_problems(): void
    {
        $diagram = new Diagram([
            'db_type' => DbType::MYSQL,
            'schema' => [
                $this->table('t1', 'users'),
                $this->table('t2', 'USERS'),
                $this->row('r1', 'id', 't1', 'INT(11)', 'PRIMARY KEY'),
                $this->row('r2', 'id', 't1', 'INT(11)'),
                $this->row('r3', 'account_id', 't2', 'VARCHAR(255)', null, false, 'NULL'),
                $this->edge('e1', 'r1', 'r3'),
                $this->edge('e2', 'missing-row', 'r1'),
            ],
        ]);

        $diagnostics = (new SchemaDoctorService)->scan($diagram);
        $ruleIds = array_column($diagnostics, 'rule_id');

        $this->assertContains('table.duplicate-name', $ruleIds);
        $this->assertContains('column.duplicate-name', $ruleIds);
        $this->assertContains('table.missing-primary-key', $ruleIds);
        $this->assertContains('column.required-null-default', $ruleIds);
        $this->assertContains('relationship.type-mismatch', $ruleIds);
        $this->assertContains('relationship.missing-column', $ruleIds);
    }

    public function test_valid_schema_has_no_diagnostics(): void
    {
        $diagram = new Diagram([
            'db_type' => DbType::POSTGRESQL,
            'schema' => [
                $this->table('t1', 'users'),
                $this->table('t2', 'orders'),
                $this->row('r1', 'id', 't1', 'INTEGER', 'PRIMARY KEY'),
                $this->row('r2', 'id', 't2', 'SERIAL', 'PRIMARY KEY'),
                $this->row('r3', 'user_id', 't2', 'INT'),
                $this->edge('e1', 'r3', 'r1'),
            ],
        ]);

        $this->assertSame([], (new SchemaDoctorService)->scan($diagram));
    }

    public function test_identifier_limits_follow_the_selected_dialect(): void
    {
        $diagram = new Diagram([
            'db_type' => DbType::POSTGRESQL,
            'schema' => [
                $this->table('t1', str_repeat('a', 64)),
                $this->row('r1', 'id', 't1', 'INTEGER', 'PRIMARY KEY'),
            ],
        ]);

        $diagnostics = (new SchemaDoctorService)->scan($diagram);

        $this->assertSame('identifier.invalid', $diagnostics[0]['rule_id']);
        $this->assertSame('error', $diagnostics[0]['severity']);
    }

    /** @return array<string, mixed> */
    private function table(string $id, string $label): array
    {
        return ['id' => $id, 'type' => 'table', 'label' => $label, 'data' => []];
    }

    /** @return array<string, mixed> */
    private function row(
        string $id,
        string $label,
        string $tableId,
        string $sqlType,
        ?string $keyMod = null,
        bool $nullable = false,
        ?string $defaultValue = null,
    ): array {
        return [
            'id' => $id,
            'type' => 'row',
            'label' => $label,
            'parentNode' => $tableId,
            'data' => [
                'sqlType' => $sqlType,
                'keyMod' => $keyMod,
                'nullable' => $nullable,
                'unsigned' => false,
                'defaultValue' => $defaultValue,
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function edge(string $id, string $source, string $target): array
    {
        return ['id' => $id, 'type' => 'chickenFoot', 'source' => $source, 'target' => $target];
    }
}
