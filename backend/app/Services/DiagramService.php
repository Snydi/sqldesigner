<?php

namespace App\Services;

use App\Models\Diagram;
use App\Repositories\DiagramRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;


class DiagramService
{
    protected DiagramRepositoryInterface $diagramRepository;

    public function __construct(DiagramRepositoryInterface $diagramRepository)
    {
        $this->diagramRepository = $diagramRepository;
    }

    public function getUserDiagrams($request): Collection
    {
        return $this->diagramRepository->all($request);
    }

    public function getDiagramById(int $id): Diagram
    {
        return $this->diagramRepository->find($id);
    }

    public function createDiagram($request)
    {
        return $this->diagramRepository->create($request);
    }

    public function updateDiagram(int $id, array $data): void
    {
        $this->diagramRepository->update($id, $data);
    }

    public function deleteDiagram(int $id): void
    {
        $this->diagramRepository->delete($id);
    }

    public function createScript($schema): string
    {
        $script = '';
        $schema = json_decode($schema);
        $tables = [];
        $rows = [];
        $connections = [];

        foreach ($schema as $item) {
            if ($item->type === 'table') {
                $tables[] = [
                    'id' => $item->id,
                    'name' => $item->label,
                ];
            } elseif ($item->type === 'row') {
                $rows[] = [
                    'id' => $item->id,
                    'name' => $item->label,
                    'table_id' => $item->parentNode,
                    'key_mod' => $item->data->keyMod == 'None' ? null : $item->data->keyMod,
                    'sql_type' => $item->data->sqlType,
                    'nullable' => $item->data->nullable ? 'NULL' : 'NOT NULL',
                    'unsigned' => $item->data->unsigned ? 'UNSIGNED' : null,
                ];
            } else {
                $connections[] = [
                    'source_id' => $item->source,
                    'target_id' => $item->target,
                ];
            }
        }

        $tables = collect($tables);
        $rows = collect($rows);
        $connections = collect($connections);

        foreach ($tables as $table) {
            $script .= "CREATE TABLE IF NOT EXISTS {$table['name']} (\n";
            foreach ($rows->where('table_id', $table['id'])->all() as $row) {
                $script .= "\t`$row[name]` $row[sql_type]";

                if ($row['unsigned']) {
                    $script .= " $row[unsigned]";
                }

                $script .= " $row[nullable],\n";

                if ($row['key_mod']) {
                    $script .= "\t$row[key_mod] (`$row[name]`),\n";
                }
            }
            $script = rtrim($script, "\n,");
            $script .= "\n";
            $script .= ");\n\n";
        }
        foreach ($connections as $connection) {
            $sourceRow = $rows->where('id', $connection['target_id'])->first();
            $targetRow = $rows->where('id', $connection['source_id'])->first();

            $tableName = $tables->where('id', $sourceRow['table_id'])->value('name');
            $targetTableName = $tables->where('id', $targetRow['table_id'])->value('name');

            $script .= "ALTER TABLE $tableName\nADD FOREIGN KEY ($sourceRow[name]) REFERENCES $targetTableName($targetRow[name]);\n\n";
        }
        return $script;
    }

//    public static function createSchema($script): string
//    {
//
//        $tables = [];
//        $rows = [];
//        $connections = [];
//
//
//        $statements = array_filter(array_map('trim', explode(";", $script)));
//
//        foreach ($statements as $statement) {
//            // Handle CREATE TABLE statements
//            if (preg_match('/CREATE TABLE IF NOT EXISTS `?(\w+)`? \((.*?)\)/si', $statement, $matches)) {
//                $tableName = $matches[1];
//                $columnDefinitions = $matches[2];
//
//
//                $tableId = uniqid('table_');
//                $tables[] = [
//                    'id' => $tableId,
//                    'type' => 'table',
//                    'label' => $tableName,
//                ];
//
//
//                preg_match_all('/`?(\w+)`? (\w+)(?: UNSIGNED)?(?: (NOT NULL|NULL))?(?:,|$)/i', $columnDefinitions, $columnMatches, PREG_SET_ORDER);
//
//                foreach ($columnMatches as $columnMatch) {
//                    $rowId = uniqid('row_');
//                    $rows[] = [
//                        'id' => $rowId,
//                        'type' => 'row',
//                        'label' => $columnMatch[1], // column name
//                        'parentNode' => $tableId, // table ID
//                        'data' => (object) [
//                            'sqlType' => $columnMatch[2],
//                            'nullable' => ($columnMatch[3] ?? '') === 'NULL',
//                            'unsigned' => strpos($columnDefinitions, $columnMatch[1] . ' UNSIGNED') !== false,
//                            'keyMod' => null, // Initialize without keyMod, updated below if necessary
//                        ],
//                    ];
//                }
//            }
//
//
//            if (preg_match('/ALTER TABLE `?(\w+)`? ADD FOREIGN KEY \((\w+)\) REFERENCES `?(\w+)`?\((\w+)\)/i', $statement, $matches)) {
//                $sourceTable = $matches[1];
//                $sourceColumn = $matches[2];
//                $targetTable = $matches[3];
//                $targetColumn = $matches[4];
//
//
//                $sourceRow = current(array_filter($rows, fn($r) => $r['label'] === $sourceColumn && $tables[array_search($sourceTable, array_column($tables, 'label'))]['id'] === $r['parentNode']));
//                $targetRow = current(array_filter($rows, fn($r) => $r['label'] === $targetColumn && $tables[array_search($targetTable, array_column($tables, 'label'))]['id'] === $r['parentNode']));
//
//                if ($sourceRow && $targetRow) {
//                    $connections[] = [
//                        'source' => $sourceRow['id'],
//                        'target' => $targetRow['id'],
//                        'type' => 'foreign_key'
//                    ];
//                }
//            }
//        }
//
//
//        $schema = array_merge($tables, $rows, $connections);
//        return json_encode($schema, JSON_PRETTY_PRINT);
//    }
}
