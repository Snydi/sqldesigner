<?php

namespace App\Services;

use App\Models\Diagram;
use App\Models\User;
use App\Repositories\DiagramRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class DiagramService
{
    protected DiagramRepositoryInterface $diagramRepository;

    public function __construct(DiagramRepositoryInterface $diagramRepository)
    {
        $this->diagramRepository = $diagramRepository;
    }

    public function getUserDiagrams(User $user): Collection
    {
        return $this->diagramRepository->all($user);
    }

    public function createDiagram(array $data): Diagram
    {
        return $this->diagramRepository->create($data);
    }

    public function updateDiagram(Diagram $diagram, array $data): bool
    {
        return $this->diagramRepository->update($diagram, $data);
    }

    public function deleteDiagram(Diagram $diagram): bool
    {
        return $this->diagramRepository->delete($diagram);
    }

    public function validateSQL(string $sql, string $dbType = 'mysql'): array
    {
        return $dbType === 'postgresql'
            ? $this->validatePostgreSQL($sql)
            : $this->validateMySQL($sql);
    }

    private function validateMySQL(string $sql): array
    {
        $connection = DB::connection('mysql_validation');
        $createdTables = [];

        try {
            foreach ($this->parseStatements($sql) as $statement) {
                try {
                    $connection->statement($statement);
                } catch (\Exception $e) {
                    return ['valid' => false, 'error' => $e->getMessage(), 'statement' => $statement];
                }

                if (preg_match('/CREATE\s+TABLE(?:\s+IF\s+NOT\s+EXISTS)?\s+`?(\w+)`?/i', $statement, $m)) {
                    $createdTables[] = $m[1];
                }
            }

            return ['valid' => true];
        } finally {
            $connection->statement('SET FOREIGN_KEY_CHECKS=0');
            foreach (array_reverse($createdTables) as $table) {
                $connection->statement("DROP TABLE IF EXISTS `$table`");
            }
            $connection->statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    private function validatePostgreSQL(string $sql): array
    {
        $connection = DB::connection('pgsql');
        $connection->beginTransaction();

        try {
            foreach ($this->parseStatements($sql) as $statement) {
                try {
                    $connection->statement($statement);
                } catch (\Exception $e) {
                    $connection->rollBack();
                    return ['valid' => false, 'error' => $e->getMessage(), 'statement' => $statement];
                }
            }

            $connection->rollBack();
            return ['valid' => true];
        } catch (\Exception $e) {
            $connection->rollBack();
            return ['valid' => false, 'error' => $e->getMessage()];
        }
    }

    public function createScript(string $schema, string $dbType = 'mysql'): string
    {
        $isPg = $dbType === 'postgresql';
        $q    = $isPg ? '"' : '`';

        $tables = collect();
        $rows = collect();
        $connections = collect();

        foreach (json_decode($schema, true) as $item) {
            match ($item['type'] ?? null) {
                'table' => $tables->push(['id' => $item['id'], 'name' => $item['label'], 'unique_together' => $item['data']['uniqueTogether'] ?? []]),
                'row'   => $rows->push([
                    'id'           => $item['id'],
                    'name'         => $item['label'],
                    'table_id'     => $item['parentNode'],
                    'key_mod'      => match ($item['data']['keyMod'] ?? null) { null, 'None' => null, default => $item['data']['keyMod'] },
                    'sql_type'     => $item['data']['sqlType'] ?? 'VARCHAR(255)',
                    'nullable'     => ($item['data']['nullable'] ?? false) ? 'NULL' : 'NOT NULL',
                    'unsigned'     => ($item['data']['unsigned'] ?? false) ? 'UNSIGNED' : null,
                    'default_value' => $item['data']['defaultValue'] ?? null,
                    'comment'      => $item['data']['comment'] ?? null,
                ]),
                default => isset($item['sourceNode']['id'], $item['targetNode']['id'])
                    ? $connections->push([
                        'source_id' => $item['sourceNode']['id'],
                        'target_id' => $item['targetNode']['id'],
                    ])
                    : null,
            };
        }

        $tablesById = $tables->keyBy('id');
        $rowsById   = $rows->keyBy('id');
        $script     = '';

        foreach ($tables as $table) {
            $tableRows = $rows->where('table_id', $table['id']);
            $tableColumnNames = $tableRows->pluck('name')->all();

            $allDefs = $tableRows->map(function ($row) use ($isPg, $q) {
                $typeWord = strtoupper(preg_replace('/[\s(].*/', '', $row['sql_type']));
                if (in_array($typeWord, ['INDEX', 'KEY', 'CONSTRAINT', 'FOREIGN', 'CHECK', 'PRIMARY', 'UNIQUE'])) return null;
                $parts = ["  {$q}{$row['name']}{$q} {$row['sql_type']}"];
                if (!$isPg && $row['unsigned']) $parts[] = $row['unsigned'];
                $parts[] = $row['nullable'];
                if ($row['key_mod'] && $row['key_mod'] !== 'FOREIGN KEY') $parts[] = $row['key_mod'];
                if ($row['default_value'] !== null && $row['default_value'] !== '') $parts[] = "DEFAULT '{$row['default_value']}'";
                if (!$isPg && $row['comment'] !== null && $row['comment'] !== '') $parts[] = "COMMENT '{$row['comment']}'";
                return implode(' ', array_filter($parts));
            })->filter()->values()->all();

            foreach ($table['unique_together'] as $constraintCols) {
                $validCols = array_values(array_filter($constraintCols, fn($c) => in_array($c, $tableColumnNames)));
                if (count($validCols) >= 2) {
                    $quotedCols = implode(', ', array_map(fn($c) => "{$q}{$c}{$q}", $validCols));
                    $constraintName = 'uq_' . $table['name'] . '_' . implode('_', $validCols);
                    $allDefs[] = $isPg
                        ? "  CONSTRAINT {$q}{$constraintName}{$q} UNIQUE ({$quotedCols})"
                        : "  UNIQUE KEY {$q}{$constraintName}{$q} ({$quotedCols})";
                }
            }

            $script .= "CREATE TABLE IF NOT EXISTS {$q}{$table['name']}{$q} (\n";
            $script .= implode(",\n", $allDefs) . "\n";
            $script .= ");\n\n";
        }

        foreach ($connections as $connection) {
            $sourceRow = $rowsById->get($connection['source_id']);
            $targetRow = $rowsById->get($connection['target_id']);

            if (!$sourceRow || !$targetRow) continue;

            $tableName       = $tablesById->get($sourceRow['table_id'])['name'];
            $targetTableName = $tablesById->get($targetRow['table_id'])['name'];

            $script .= "ALTER TABLE {$q}$targetTableName{$q}\nADD FOREIGN KEY ({$q}{$targetRow['name']}{$q}) REFERENCES {$q}$tableName{$q}({$q}{$sourceRow['name']}{$q});\n\n";
        }

        return $script;
    }

    public function createJson(string $schema): string
    {
        $tables = collect();
        $rows = collect();
        $connections = collect();

        foreach (json_decode($schema, true) as $item) {
            match ($item['type'] ?? null) {
                'table' => $tables->push(['id' => $item['id'], 'name' => $item['label']]),
                'row'   => $rows->push([
                    'id'            => $item['id'],
                    'name'          => $item['label'],
                    'table_id'      => $item['parentNode'],
                    'key'           => match ($item['data']['keyMod'] ?? null) { null, 'None' => null, default => $item['data']['keyMod'] },
                    'type'          => $item['data']['sqlType'] ?? 'VARCHAR(255)',
                    'nullable'      => $item['data']['nullable'] ?? false,
                    'unsigned'      => $item['data']['unsigned'] ?? false,
                    'default_value' => $item['data']['defaultValue'] ?? null,
                    'comment'       => $item['data']['comment'] ?? null,
                ]),
                default => isset($item['sourceNode']['id'], $item['targetNode']['id'])
                    ? $connections->push([
                        'source_id' => $item['sourceNode']['id'],
                        'target_id' => $item['targetNode']['id'],
                    ])
                    : null,
            };
        }

        $tablesById = $tables->keyBy('id');
        $rowsById   = $rows->keyBy('id');

        $result = [
            'tables'      => [],
            'foreignKeys' => [],
        ];

        foreach ($tables as $table) {
            $columns = $rows->where('table_id', $table['id'])->map(function ($row) {
                $col = [
                    'name'     => $row['name'],
                    'type'     => $row['type'],
                    'nullable' => $row['nullable'],
                ];
                if ($row['unsigned'])      $col['unsigned']      = true;
                if ($row['key'])           $col['key']           = $row['key'];
                if ($row['default_value']) $col['default_value'] = $row['default_value'];
                if ($row['comment'])       $col['comment']       = $row['comment'];
                return $col;
            })->values()->all();

            $result['tables'][] = [
                'name'    => $table['name'],
                'columns' => $columns,
            ];
        }

        foreach ($connections as $connection) {
            $sourceRow = $rowsById->get($connection['source_id']);
            $targetRow = $rowsById->get($connection['target_id']);

            if (!$sourceRow || !$targetRow) continue;

            $result['foreignKeys'][] = [
                'table'            => $tablesById->get($sourceRow['table_id'])['name'],
                'column'           => $sourceRow['name'],
                'referencesTable'  => $tablesById->get($targetRow['table_id'])['name'],
                'referencesColumn' => $targetRow['name'],
            ];
        }

        return json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function createSchema(string $script): string
    {
        $tables = [];
        $rows = [];
        $connections = [];
        $tableX = 50;

        $pendingFks = [];

        foreach ($this->parseStatements($script) as $statement) {
            if (preg_match('/CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?["`]?(\w+)["`]?\s*\((.*)\)/is', $statement, $m)) {
                $tableX += 400;
                $tableId  = uniqid();
                $tables[] = $this->buildTableNode($tableId, $m[1], $tableX);
                $parsed   = $this->parseColumns($m[2], $tableId, $tableX);
                array_push($rows, ...$parsed['rows']);
                if (!empty($parsed['uniqueTogether'])) {
                    $tables[count($tables) - 1]['data']['uniqueTogether'] = $parsed['uniqueTogether'];
                }
                foreach ($this->parseInlineForeignKeys($m[2]) as $fk) {
                    $pendingFks[] = array_merge($fk, ['sourceTable' => $m[1]]);
                }
            } elseif (preg_match('/ALTER\s+TABLE\s+["`]?(\w+)["`]?\s+ADD\s+(?:CONSTRAINT\s+["`]?\w+["`]?\s+)?FOREIGN\s+KEY\s*\(["`]?(\w+)["`]?\)\s+REFERENCES\s+["`]?(\w+)["`]?\s*\(["`]?(\w+)["`]?\)/i', $statement, $m)) {
                $connection = $this->resolveConnection($tables, $rows, $m[1], $m[2], $m[3], $m[4]);
                if ($connection) $connections[] = $connection;
            }
        }

        foreach ($pendingFks as $fk) {
            $connection = $this->resolveConnection($tables, $rows, $fk['sourceTable'], $fk['sourceCol'], $fk['targetTable'], $fk['targetCol']);
            if ($connection) $connections[] = $connection;
        }

        return json_encode(array_merge($tables, $rows, $connections), JSON_PRETTY_PRINT);
    }

    // --- Private helpers ---

    private function parseStatements(string $sql): array
    {
        return array_filter(array_map('trim', explode(';', $sql)));
    }

    private function buildTableNode(string $id, string $name, int $x, int $y = 50): array
    {
        return [
            'id'               => $id,
            'type'             => 'table',
            'dimensions'       => ['width' => 350, 'height' => 40],
            'computedPosition' => ['x' => $x, 'y' => $y, 'z' => 1000],
            'handleBounds'     => ['source' => null, 'target' => null],
            'selected'         => false,
            'dragging'         => false,
            'resizing'         => false,
            'initialized'      => false,
            'isParent'         => true,
            'position'         => ['x' => $x, 'y' => $y],
            'data'             => ['toolbarPosition' => 'top', 'toolbarVisible' => true, 'editing' => false, 'color' => '#3d7a5c', 'uniqueTogether' => []],
            'events'           => (object)[],
            'label'            => $name,
            'style'            => [
                'display' => 'flex', 'border' => '1px solid #3d7a5c',
                'background' => '#3d7a5c', 'borderColor' => '#3d7a5c', 'color' => 'white',
                'width' => '350px', 'height' => '40px',
                'alignItems' => 'center', 'justifyContent' => 'space-between',
                'borderRadius' => '6px 6px 0 0',
            ],
        ];
    }

    private function buildRowNode(string $id, string $tableId, string $name, int $x, int $y, int $index, array $data): array
    {
        return [
            'id'               => $id,
            'type'             => 'row',
            'dimensions'       => ['width' => 350, 'height' => 40],
            'computedPosition' => ['x' => $x, 'y' => $y, 'z' => 1001],
            'handleBounds'     => [
                'source' => [
                    ['id' => null, 'type' => 'source', 'nodeId' => $id, 'position' => 'right', 'x' => 345, 'y' => 16, 'width' => 8, 'height' => 8],
                    ['id' => null, 'type' => 'source', 'nodeId' => $id, 'position' => 'left',  'x' => -3,  'y' => 16, 'width' => 8, 'height' => 8],
                ],
                'target' => null,
            ],
            'draggable'        => false,
            'selected'         => false,
            'dragging'         => false,
            'resizing'         => false,
            'initialized'      => false,
            'isParent'         => false,
            'position'         => ['x' => 0, 'y' => 40 + ($index * 40)],
            'data'             => $data,
            'events'           => (object)[],
            'label'            => $name,
            'style'            => [
                'display' => 'flex', 'border' => '1px solid #898989',
                'borderColor' => '#898989', 'background' => '#ffffff', 'color' => '#000000',
                'width' => '350px', 'height' => '40px',
                'alignItems' => 'center', 'justifyContent' => 'space-between',
            ],
            'parentNode'       => $tableId,
        ];
    }

    private function splitColumnDefinitions(string $content): array
    {
        $lines  = [];
        $current = '';
        $depth  = 0;

        foreach (str_split(preg_replace('/\s+/', ' ', trim($content))) as $char) {
            if ($char === '(') $depth++;
            elseif ($char === ')') $depth--;

            if ($char === ',' && $depth === 0) {
                $lines[]  = trim($current);
                $current = '';
            } else {
                $current .= $char;
            }
        }

        if ($current !== '') $lines[] = trim($current);

        return $lines;
    }

    private function parseColumns(string $tableContent, string $tableId, int $tableX, int $tableY = 50): array
    {
        $lines                 = $this->splitColumnDefinitions($tableContent);
        $constraints           = [];
        $uniqueTogetherConstraints = [];
        $usedNames             = [];
        $rows                  = [];
        $index                 = 0;

        foreach ($lines as $line) {
            // Table-level PRIMARY KEY (single column)
            if (preg_match('/^(?:CONSTRAINT\s+["`]?\w+["`]?\s+)?PRIMARY\s+KEY\s*\(\s*["`]?(\w+)["`]?\s*\)/i', $line, $m)) {
                $constraints[$m[1]] = 'PRIMARY KEY';
            }
            // Table-level UNIQUE constraint (single or multi-column)
            elseif (preg_match('/^(?:CONSTRAINT\s+["`]?\w+["`]?\s+)?UNIQUE(?:\s+KEY(?:\s+["`]?\w+["`]?)?)?\s*\(\s*([^)]+)\s*\)/i', $line, $m)) {
                $cols = array_values(array_filter(array_map(
                    fn($c) => trim(str_replace(['`', '"'], '', $c)),
                    explode(',', $m[1])
                )));
                if (count($cols) === 1) {
                    $constraints[$cols[0]] = 'UNIQUE';
                } elseif (count($cols) >= 2) {
                    $uniqueTogetherConstraints[] = $cols;
                }
            }
        }

        foreach ($lines as $line) {
            if (preg_match('/^(?:CONSTRAINT\s|PRIMARY\s+KEY|UNIQUE\s+(?:KEY|INDEX)|UNIQUE\s*\(|FOREIGN\s+KEY|KEY\s|INDEX\s|CHECK\s*\()/i', $line)) continue;
            if (!preg_match('/^["`]?(\w+)["`]?\s+([a-zA-Z]+)(?:\(([^)]+)\))?(?:\s+(UNSIGNED))?(?:\s+(NOT\s+NULL|NULL))?(?:\s+(PRIMARY\s+KEY|UNIQUE))?(?:\s+DEFAULT\s+\'([^\']*)\')?(?:\s+COMMENT\s+\'([^\']*)\')?/i', $line, $m)) continue;

            $baseName = $m[1];
            $name     = $baseName;
            $counter  = 1;
            while (in_array($name, $usedNames)) $name = $baseName . '_' . $counter++;
            $usedNames[] = $name;

            $sqlType      = strtoupper($m[2]) . (isset($m[3]) && $m[3] !== '' ? "($m[3])" : '');
            $unsigned     = isset($m[4]) && strtoupper($m[4]) === 'UNSIGNED';
            $nullable     = isset($m[5]) ? strtoupper($m[5]) === 'NULL' : true;
            $keyMod       = isset($m[6]) ? strtoupper(preg_replace('/\s+/', ' ', $m[6])) : ($constraints[$baseName] ?? null);
            $defaultValue = $m[7] ?? '';
            $comment      = $m[8] ?? '';

            $rowId = uniqid();
            $rows[] = $this->buildRowNode($rowId, $tableId, $name, $tableX, $tableY + 40 + ($index * 40), $index, [
                'editing' => false, 'showModal' => false, 'showOptionsModal' => false,
                'keyMod'  => $keyMod ?? 'None',
                'sqlType' => $sqlType, 'nullable' => $nullable, 'unsigned' => $unsigned,
                'defaultValue' => $defaultValue, 'comment' => $comment,
            ]);
            $index++;
        }

        return ['rows' => $rows, 'uniqueTogether' => $uniqueTogetherConstraints];
    }

    private function parseInlineForeignKeys(string $tableContent): array
    {
        $fks = [];
        foreach ($this->splitColumnDefinitions($tableContent) as $line) {
            if (preg_match('/(?:CONSTRAINT\s+["`]?\w+["`]?\s+)?FOREIGN\s+KEY\s*\(["`]?(\w+)["`]?\)\s+REFERENCES\s+["`]?(\w+)["`]?\s*\(["`]?(\w+)["`]?\)/i', $line, $m)) {
                $fks[] = ['sourceCol' => $m[1], 'targetTable' => $m[2], 'targetCol' => $m[3]];
            }
        }
        return $fks;
    }

    private function resolveConnection(array $tables, array $rows, string $sourceTable, string $sourceCol, string $targetTable, string $targetCol): ?array
    {
        $findTableId = fn(string $name) => collect($tables)->where('label', $name)->value('id');
        $findRow     = fn(?string $tableId, string $col) => $tableId
            ? collect($rows)->first(fn($r) => $r['parentNode'] === $tableId && $r['label'] === $col)
            : null;

        $sourceRow = $findRow($findTableId($sourceTable), $sourceCol);
        $targetRow = $findRow($findTableId($targetTable), $targetCol);

        if (!$sourceRow || !$targetRow) return null;

        $targetTableNode = collect($tables)->first(fn($t) => $t['id'] === $targetRow['parentNode']);
        $color = $targetTableNode['data']['color'] ?? '#3d7a5c';

        return [
            'id'           => uniqid('e-'),
            'type'         => 'chickenFoot',
            'source'       => $sourceRow['id'],
            'target'       => $targetRow['id'],
            'sourceHandle' => 'source-right',
            'targetHandle' => 'target-left',
            'updatable'    => true,
            'style'        => ['stroke' => $color],
            'data'         => ['relationshipType' => 'one-to-many', 'markerStart' => 'url(#chickenFoot)', 'markerEnd' => 'none', 'color' => $color],
        ];
    }
}
