<?php

namespace App\Services;

use App\Models\Diagram;
use App\Models\User;
use App\Repositories\DiagramRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

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

    public function createScript(string $schema): string
    {
        $script = '';
        $schema = json_decode($schema, true);
        $tables = [];
        $rows = [];
        $connections = [];

        foreach ($schema as $item) {
            if (isset($item['type'])) {
                if ($item['type'] === 'table') {
                    $tables[] = [
                        'id' => $item['id'],
                        'name' => $item['label'],
                    ];
                } elseif ($item['type'] === 'row') {
                    $rows[] = [
                        'id' => $item['id'],
                        'name' => $item['label'],
                        'table_id' => $item['parentNode'],
                        'key_mod' => isset($item['data']['keyMod']) && $item['data']['keyMod'] !== 'None' && $item['data']['keyMod'] !== null ? $item['data']['keyMod'] : null,
                        'sql_type' => $item['data']['sqlType'] ?? 'VARCHAR(255)',
                        'nullable' => isset($item['data']['nullable']) && $item['data']['nullable'] ? 'NULL' : 'NOT NULL',
                        'unsigned' => isset($item['data']['unsigned']) && $item['data']['unsigned'] ? 'UNSIGNED' : null,
                    ];
                }
            } elseif (isset($item['source']) && isset($item['target'])) {
                $connections[] = [
                    'source_id' => $item['source'],
                    'target_id' => $item['target'],
                ];
            }
        }

        $tables = collect($tables);
        $rows = collect($rows);
        $connections = collect($connections);

        foreach ($tables as $table) {
            $script .= "CREATE TABLE IF NOT EXISTS `{$table['name']}` (\n";
            $tableRows = $rows->where('table_id', $table['id'])->all();

            $columnDefinitions = [];

            foreach ($tableRows as $row) {
                $columnDef = "  `{$row['name']}` {$row['sql_type']}";

                if ($row['unsigned']) {
                    $columnDef .= " UNSIGNED";
                }

                $columnDef .= " {$row['nullable']}";

                if ($row['key_mod']) {
                    $columnDef .= " {$row['key_mod']}";
                }

                $columnDefinitions[] = $columnDef;
            }

            $script .= implode(",\n", $columnDefinitions) . "\n";
            $script .= ");\n\n";
        }

        foreach ($connections as $connection) {
            $sourceRow = $rows->where('id', $connection['source_id'])->first();
            $targetRow = $rows->where('id', $connection['target_id'])->first();

            if (!$sourceRow || !$targetRow) {
                continue;
            }

            $tableName = $tables->where('id', $sourceRow['table_id'])->value('name');
            $targetTableName = $tables->where('id', $targetRow['table_id'])->value('name');

            $script .= "ALTER TABLE `$tableName`\n";
            $script .= "ADD FOREIGN KEY (`{$sourceRow['name']}`) REFERENCES `$targetTableName`(`{$targetRow['name']}`);\n\n";
        }

        return $script;
    }

    public function createSchema($script): string
    {
        $tables = [];
        $rows = [];
        $connections = [];

        $statements = array_filter(array_map('trim', explode(";", $script)));

        $tableX = 50;
        $tableY = 50;
        $tableSpacing = 400;

        $rowYOffset = 40;

        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (empty($statement)) continue;


            if (preg_match('/CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?`?(\w+)`?\s*\((.*)\)/is', $statement, $matches)) {
                $tableName = $matches[1];
                $tableContent = $matches[2];

                $tableId = uniqid();
                $tableX += $tableSpacing;

                $tables[] = [
                    'id' => $tableId,
                    'type' => 'table',
                    'dimensions' => [
                        'width' => 350,
                        'height' => 40
                    ],
                    'computedPosition' => [
                        'x' => $tableX,
                        'y' => $tableY,
                        'z' => 1000
                    ],
                    'handleBounds' => [
                        'source' => null,
                        'target' => null
                    ],
                    'selected' => false,
                    'dragging' => false,
                    'resizing' => false,
                    'initialized' => false,
                    'isParent' => true,
                    'position' => [
                        'x' => $tableX,
                        'y' => $tableY
                    ],
                    'data' => [
                        'toolbarPosition' => 'top',
                        'toolbarVisible' => true,
                        'editing' => false
                    ],
                    'events' => (object)[],
                    'label' => $tableName,
                    'style' => [
                        'display' => 'flex',
                        'border' => '1px solid #10b981',
                        'background' => '#ff6029',
                        'borderColor' => '#ff6029',
                        'color' => 'white',
                        'borderRadius' => '5px',
                        'width' => '350px',
                        'height' => '40px',
                        'alignItems' => 'center',
                        'justifyContent' => 'space-between'
                    ]
                ];


                $tableContent = preg_replace('/\s+/', ' ', $tableContent);
                $tableContent = trim($tableContent);

                $lines = [];
                $current = '';
                $parenCount = 0;

                for ($i = 0; $i < strlen($tableContent); $i++) {
                    $char = $tableContent[$i];

                    if ($char === '(') {
                        $parenCount++;
                    } elseif ($char === ')') {
                        $parenCount--;
                    }

                    if ($char === ',' && $parenCount === 0) {
                        $lines[] = trim($current);
                        $current = '';
                    } else {
                        $current .= $char;
                    }
                }

                if (!empty($current)) {
                    $lines[] = trim($current);
                }


                $constraints = [];
                foreach ($lines as $line) {
                    if (preg_match('/^(PRIMARY\s+KEY|UNIQUE)\s*\(\s*`?(\w+)`?\s*\)$/i', $line, $matches)) {
                        $constraintType = strtoupper(preg_replace('/\s+/', ' ', $matches[1]));
                        $columnName = $matches[2];
                        $constraints[$columnName] = $constraintType;
                    }
                }


                $rowIndex = 0;
                $usedColumnNames = [];

                foreach ($lines as $line) {
                    if (preg_match('/^(PRIMARY\s+KEY|UNIQUE)\s*\(/i', $line)) {
                        continue;
                    }


                    if (preg_match('/^`?(\w+)`?\s+([a-zA-Z]+)(?:\(([^)]+)\))?(?:\s+(UNSIGNED))?(?:\s+(NOT\s+NULL|NULL))?(?:\s+(PRIMARY\s+KEY|UNIQUE))?/i', $line, $matches)) {
                        $columnName = $matches[1];


                        $baseName = $columnName;
                        $counter = 1;
                        while (in_array($columnName, $usedColumnNames)) {
                            $columnName = $baseName . '_' . $counter;
                            $counter++;
                        }
                        $usedColumnNames[] = $columnName;

                        $typeName = strtoupper($matches[2]);


                        $typeParams = '';
                        if (isset($matches[3]) && $matches[3] !== '') {
                            $typeParams = "({$matches[3]})";
                        }
                        $sqlType = $typeName . $typeParams;


                        $unsigned = isset($matches[4]) && strtoupper($matches[4]) === 'UNSIGNED';


                        $nullable = true;
                        if (isset($matches[5])) {
                            $nullable = strtoupper($matches[5]) === 'NULL';
                        }


                        $keyMod = null;
                        if (isset($matches[6])) {
                            $keyMod = strtoupper(preg_replace('/\s+/', ' ', $matches[6]));
                        }


                        if (!$keyMod && isset($constraints[$baseName])) {
                            $keyMod = $constraints[$baseName];
                        }

                        $rowId = uniqid();
                        $rowY = $tableY + 40 + ($rowIndex * $rowYOffset);

                        $rows[] = [
                            'id' => $rowId,
                            'type' => 'row',
                            'dimensions' => [
                                'width' => 350,
                                'height' => 40
                            ],
                            'computedPosition' => [
                                'x' => $tableX,
                                'y' => $rowY,
                                'z' => 1001
                            ],
                            'handleBounds' => [
                                'source' => [
                                    [
                                        'id' => null,
                                        'type' => 'source',
                                        'nodeId' => $rowId,
                                        'position' => 'right',
                                        'x' => 345,
                                        'y' => 16,
                                        'width' => 8,
                                        'height' => 8
                                    ],
                                    [
                                        'id' => null,
                                        'type' => 'source',
                                        'nodeId' => $rowId,
                                        'position' => 'left',
                                        'x' => -3,
                                        'y' => 16,
                                        'width' => 8,
                                        'height' => 8
                                    ]
                                ],
                                'target' => null
                            ],
                            'draggable' => false,
                            'selected' => false,
                            'dragging' => false,
                            'resizing' => false,
                            'initialized' => false,
                            'isParent' => false,
                            'position' => [
                                'x' => 0,
                                'y' => 40 + ($rowIndex * 40)
                            ],
                            'data' => [
                                'editing' => false,
                                'showModal' => false,
                                'showOptionsModal' => false,
                                'keyMod' => $keyMod ?? 'None',
                                'sqlType' => $sqlType,
                                'nullable' => $nullable,
                                'unsigned' => $unsigned
                            ],
                            'events' => (object)[],
                            'label' => $columnName,
                            'style' => [
                                'display' => 'flex',
                                'border' => '1px solid #10b981',
                                'borderColor' => '#898989',
                                'background' => '#ffffff',
                                'color' => '#000000',
                                'borderRadius' => '5px',
                                'width' => '350px',
                                'height' => '40px',
                                'alignItems' => 'center',
                                'justifyContent' => 'space-between'
                            ],
                            'parentNode' => $tableId
                        ];

                        $rowIndex++;
                    }
                }
            } elseif (preg_match('/ALTER\s+TABLE\s+`?(\w+)`?\s+ADD\s+FOREIGN\s+KEY\s*\(`?(\w+)`?\)\s+REFERENCES\s+`?(\w+)`?\s*\(`?(\w+)`?\)/i', $statement, $fkMatches)) {
                $sourceTable = $fkMatches[1];
                $sourceColumn = $fkMatches[2];
                $targetTable = $fkMatches[3];
                $targetColumn = $fkMatches[4];

                $sourceTableId = null;
                foreach ($tables as $table) {
                    if ($table['label'] === $sourceTable) {
                        $sourceTableId = $table['id'];
                        break;
                    }
                }

                $targetTableId = null;
                foreach ($tables as $table) {
                    if ($table['label'] === $targetTable) {
                        $targetTableId = $table['id'];
                        break;
                    }
                }

                $sourceRow = null;
                $targetRow = null;

                if ($sourceTableId) {
                    foreach ($rows as $row) {
                        if ($row['parentNode'] === $sourceTableId && $row['label'] === $sourceColumn) {
                            $sourceRow = $row;
                            break;
                        }
                    }
                }

                if ($targetTableId) {
                    foreach ($rows as $row) {
                        if ($row['parentNode'] === $targetTableId && $row['label'] === $targetColumn) {
                            $targetRow = $row;
                            break;
                        }
                    }
                }

                if ($sourceRow && $targetRow) {
                    $connections[] = [
                        'source' => $sourceRow['id'],
                        'target' => $targetRow['id'],
                    ];
                }
            }
        }

        $schema = array_merge($tables, $rows, $connections);
        return json_encode($schema, JSON_PRETTY_PRINT);
    }
}