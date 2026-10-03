<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\DbType;
use App\Models\Diagram;

class SchemaDoctorService
{
    /** @return list<array<string, mixed>> */
    public function scan(Diagram $diagram): array
    {
        $schema = is_array($diagram->schema) ? $diagram->schema : [];
        $tables = array_values(array_filter($schema, fn (mixed $item): bool => is_array($item) && ($item['type'] ?? null) === 'table'));
        $rows = array_values(array_filter($schema, fn (mixed $item): bool => is_array($item) && ($item['type'] ?? null) === 'row'));
        $relationships = array_values(array_filter(
            $schema,
            fn (mixed $item): bool => is_array($item) && isset($item['source'], $item['target'])
        ));

        $tablesById = $this->indexById($tables);
        $rowsById = $this->indexById($rows);
        $rowsByTable = [];
        foreach ($rows as $row) {
            $rowsByTable[(string) ($row['parentNode'] ?? '')][] = $row;
        }

        $diagnostics = [
            ...$this->duplicateTableNames($tables),
            ...$this->tableDiagnostics($tables, $rowsByTable, $diagram->db_type ?? DbType::MYSQL),
            ...$this->rowDiagnostics($rowsByTable, $diagram->db_type ?? DbType::MYSQL),
            ...$this->relationshipDiagnostics($relationships, $rowsById, $tablesById),
        ];

        $severityOrder = ['error' => 0, 'warning' => 1, 'suggestion' => 2];
        usort($diagnostics, function (array $left, array $right) use ($severityOrder): int {
            return [
                $severityOrder[$left['severity']] ?? 9,
                $left['title'],
                (string) ($left['table_id'] ?? ''),
                (string) ($left['row_id'] ?? ''),
            ] <=> [
                $severityOrder[$right['severity']] ?? 9,
                $right['title'],
                (string) ($right['table_id'] ?? ''),
                (string) ($right['row_id'] ?? ''),
            ];
        });

        return $diagnostics;
    }

    /**
     * @param  list<array<string, mixed>>  $items
     * @return array<string, array<string, mixed>>
     */
    private function indexById(array $items): array
    {
        $indexed = [];
        foreach ($items as $item) {
            if (isset($item['id'])) {
                $indexed[(string) $item['id']] = $item;
            }
        }

        return $indexed;
    }

    /**
     * @param  list<array<string, mixed>>  $tables
     * @return list<array<string, mixed>>
     */
    private function duplicateTableNames(array $tables): array
    {
        $groups = [];
        foreach ($tables as $table) {
            $groups[$this->normalizeName($table['label'] ?? '')][] = $table;
        }

        $diagnostics = [];
        foreach ($groups as $normalizedName => $duplicates) {
            if ($normalizedName === '' || count($duplicates) < 2) {
                continue;
            }

            $name = (string) ($duplicates[0]['label'] ?? '');
            $diagnostics[] = $this->diagnostic(
                'table.duplicate-name',
                'error',
                'Duplicate table name',
                "More than one table is named \"{$name}\".",
                'Rename each table so its name is unique.',
                tableId: (string) ($duplicates[0]['id'] ?? ''),
                metadata: ['duplicate_ids' => array_values(array_map(fn (array $table): string => (string) ($table['id'] ?? ''), $duplicates))]
            );
        }

        return $diagnostics;
    }

    /**
     * @param  list<array<string, mixed>>  $tables
     * @param  array<string, list<array<string, mixed>>>  $rowsByTable
     * @return list<array<string, mixed>>
     */
    private function tableDiagnostics(array $tables, array $rowsByTable, DbType $dbType): array
    {
        $diagnostics = [];
        foreach ($tables as $table) {
            $tableId = (string) ($table['id'] ?? '');
            $tableName = (string) ($table['label'] ?? '');
            $primaryKeys = array_values(array_filter(
                $rowsByTable[$tableId] ?? [],
                fn (array $row): bool => strtoupper(trim((string) ($row['data']['keyMod'] ?? ''))) === 'PRIMARY KEY'
            ));

            if ($primaryKeys === []) {
                $diagnostics[] = $this->diagnostic(
                    'table.missing-primary-key',
                    'warning',
                    'Table has no primary key',
                    "The \"{$tableName}\" table does not define a primary key.",
                    'Add a primary-key column or mark an existing column as the primary key.',
                    tableId: $tableId
                );
            } elseif (count($primaryKeys) > 1) {
                $diagnostics[] = $this->diagnostic(
                    'table.invalid-primary-key',
                    'error',
                    'Table has multiple primary keys',
                    "The \"{$tableName}\" table defines more than one inline primary key.",
                    'Keep one primary-key column. Composite primary keys are not currently represented by multiple inline primary keys.',
                    tableId: $tableId,
                    metadata: ['row_ids' => array_values(array_map(fn (array $row): string => (string) ($row['id'] ?? ''), $primaryKeys))]
                );
            }

            if ($problem = $this->identifierProblem($tableName, $dbType)) {
                $diagnostics[] = $this->diagnostic(
                    'identifier.invalid',
                    'error',
                    'Invalid table identifier',
                    "The table name \"{$tableName}\" {$problem}.",
                    'Rename the table using a valid identifier for the selected database.',
                    tableId: $tableId
                );
            }
        }

        return $diagnostics;
    }

    /**
     * @param  array<string, list<array<string, mixed>>>  $rowsByTable
     * @return list<array<string, mixed>>
     */
    private function rowDiagnostics(array $rowsByTable, DbType $dbType): array
    {
        $diagnostics = [];
        foreach ($rowsByTable as $tableId => $rows) {
            $nameGroups = [];
            foreach ($rows as $row) {
                $nameGroups[$this->normalizeName($row['label'] ?? '')][] = $row;
            }

            foreach ($nameGroups as $normalizedName => $duplicates) {
                if ($normalizedName === '' || count($duplicates) < 2) {
                    continue;
                }

                $name = (string) ($duplicates[0]['label'] ?? '');
                $diagnostics[] = $this->diagnostic(
                    'column.duplicate-name',
                    'error',
                    'Duplicate column name',
                    "More than one column in this table is named \"{$name}\".",
                    'Rename each column in the table so its name is unique.',
                    tableId: $tableId,
                    rowId: (string) ($duplicates[0]['id'] ?? ''),
                    metadata: ['duplicate_ids' => array_values(array_map(fn (array $row): string => (string) ($row['id'] ?? ''), $duplicates))]
                );
            }

            foreach ($rows as $row) {
                $rowId = (string) ($row['id'] ?? '');
                $rowName = (string) ($row['label'] ?? '');
                if ($problem = $this->identifierProblem($rowName, $dbType)) {
                    $diagnostics[] = $this->diagnostic(
                        'identifier.invalid',
                        'error',
                        'Invalid column identifier',
                        "The column name \"{$rowName}\" {$problem}.",
                        'Rename the column using a valid identifier for the selected database.',
                        tableId: $tableId,
                        rowId: $rowId
                    );
                }

                $nullable = (bool) ($row['data']['nullable'] ?? false);
                $default = strtoupper(trim((string) ($row['data']['defaultValue'] ?? '')));
                if (! $nullable && $default === 'NULL') {
                    $diagnostics[] = $this->diagnostic(
                        'column.required-null-default',
                        'warning',
                        'Required column defaults to NULL',
                        "The required \"{$rowName}\" column explicitly uses NULL as its default.",
                        'Remove the NULL default or make the column nullable.',
                        tableId: $tableId,
                        rowId: $rowId
                    );
                }
            }
        }

        return $diagnostics;
    }

    /**
     * @param  list<array<string, mixed>>  $relationships
     * @param  array<string, array<string, mixed>>  $rowsById
     * @param  array<string, array<string, mixed>>  $tablesById
     * @return list<array<string, mixed>>
     */
    private function relationshipDiagnostics(array $relationships, array $rowsById, array $tablesById): array
    {
        $diagnostics = [];
        foreach ($relationships as $relationship) {
            $relationshipId = (string) ($relationship['id'] ?? '');
            $sourceId = (string) ($relationship['source'] ?? '');
            $targetId = (string) ($relationship['target'] ?? '');
            $source = $rowsById[$sourceId] ?? null;
            $target = $rowsById[$targetId] ?? null;

            if ($source === null || $target === null) {
                $missing = $source === null ? $sourceId : $targetId;
                $diagnostics[] = $this->diagnostic(
                    'relationship.missing-column',
                    'error',
                    'Relationship references a missing column',
                    "The relationship references column \"{$missing}\", which no longer exists.",
                    'Reconnect or remove this relationship.',
                    relationshipId: $relationshipId
                );
                continue;
            }

            $sourceTableId = (string) ($source['parentNode'] ?? '');
            $targetTableId = (string) ($target['parentNode'] ?? '');
            if (! isset($tablesById[$sourceTableId]) || ! isset($tablesById[$targetTableId])) {
                $missing = ! isset($tablesById[$sourceTableId]) ? $sourceTableId : $targetTableId;
                $diagnostics[] = $this->diagnostic(
                    'relationship.missing-table',
                    'error',
                    'Relationship references a missing table',
                    "The relationship references table \"{$missing}\", which no longer exists.",
                    'Reconnect or remove this relationship.',
                    relationshipId: $relationshipId
                );
                continue;
            }

            if (! $this->typesCompatible($source, $target)) {
                $sourceType = (string) ($source['data']['sqlType'] ?? '');
                $targetType = (string) ($target['data']['sqlType'] ?? '');
                $diagnostics[] = $this->diagnostic(
                    'relationship.type-mismatch',
                    'warning',
                    'Relationship column types do not match',
                    "The relationship connects {$sourceType} to {$targetType}.",
                    'Use compatible data types for both relationship columns.',
                    tableId: $sourceTableId,
                    rowId: $sourceId,
                    relationshipId: $relationshipId,
                    metadata: ['target_table_id' => $targetTableId, 'target_row_id' => $targetId]
                );
            }
        }

        return $diagnostics;
    }

    private function normalizeName(mixed $name): string
    {
        return mb_strtolower(trim((string) $name));
    }

    private function identifierProblem(string $identifier, DbType $dbType): ?string
    {
        if (trim($identifier) === '') {
            return 'is empty';
        }
        if (preg_match('/[\x00-\x1F\x7F]/u', $identifier) === 1) {
            return 'contains a control character';
        }

        $maxLength = match ($dbType) {
            DbType::MYSQL, DbType::MSACCESS => 64,
            DbType::POSTGRESQL => 63,
            DbType::ORACLE, DbType::SQLSERVER => 128,
            DbType::SQLITE => null,
        };

        if ($maxLength !== null && mb_strlen($identifier) > $maxLength) {
            return "is longer than the {$maxLength}-character limit";
        }

        return null;
    }

    /** @param array<string, mixed> $source @param array<string, mixed> $target */
    private function typesCompatible(array $source, array $target): bool
    {
        $sourceType = $this->typeFamily((string) ($source['data']['sqlType'] ?? ''));
        $targetType = $this->typeFamily((string) ($target['data']['sqlType'] ?? ''));
        if ($sourceType === '' || $targetType === '') {
            return true;
        }

        if ($sourceType !== $targetType) {
            return false;
        }

        $sourceUnsigned = (bool) ($source['data']['unsigned'] ?? false);
        $targetUnsigned = (bool) ($target['data']['unsigned'] ?? false);

        return $sourceUnsigned === $targetUnsigned;
    }

    private function typeFamily(string $type): string
    {
        $base = strtoupper(trim($type));
        $base = preg_replace('/\s+UNSIGNED\b/i', '', $base) ?? $base;
        $base = preg_replace('/\s*\(.*/', '', $base) ?? $base;
        $base = trim(strtok($base, ' ') ?: $base);

        return match ($base) {
            'TINYINT', 'SMALLINT', 'MEDIUMINT', 'INT', 'INTEGER', 'SERIAL', 'AUTOINCREMENT' => 'integer',
            'BIGINT', 'BIGSERIAL', 'LONG' => 'big-integer',
            'DECIMAL', 'NUMERIC', 'NUMBER', 'MONEY', 'SMALLMONEY' => 'decimal',
            'REAL', 'FLOAT', 'DOUBLE', 'BINARY_FLOAT', 'BINARY_DOUBLE' => 'floating',
            'CHAR', 'NCHAR', 'VARCHAR', 'VARCHAR2', 'NVARCHAR', 'NVARCHAR2', 'TEXT', 'TINYTEXT', 'MEDIUMTEXT', 'LONGTEXT', 'CLOB', 'NCLOB', 'LONGTEXTCHAR' => 'text',
            'UUID', 'UNIQUEIDENTIFIER', 'GUID' => 'uuid',
            'DATE' => 'date',
            'TIME' => 'time',
            'DATETIME', 'DATETIME2', 'SMALLDATETIME', 'TIMESTAMP', 'TIMESTAMPTZ' => 'datetime',
            'BOOL', 'BOOLEAN', 'BIT', 'YESNO' => 'boolean',
            default => $base,
        };
    }

    /**
     * @param  array<string, mixed>  $metadata
     * @return array<string, mixed>
     */
    private function diagnostic(
        string $ruleId,
        string $severity,
        string $title,
        string $message,
        string $recommendation,
        ?string $tableId = null,
        ?string $rowId = null,
        ?string $relationshipId = null,
        array $metadata = [],
    ): array {
        return [
            'rule_id' => $ruleId,
            'severity' => $severity,
            'title' => $title,
            'message' => $message,
            'recommendation' => $recommendation,
            'table_id' => $tableId,
            'row_id' => $rowId,
            'relationship_id' => $relationshipId,
            'metadata' => $metadata,
        ];
    }
}
