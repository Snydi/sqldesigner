<?php

namespace Tests\Unit\Services;

use App\Models\Diagram;
use App\Models\User;
use App\Services\DiagramService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DiagramServiceTest extends TestCase
{
    use DatabaseTransactions;

    private DiagramService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = app(DiagramService::class);
    }

    public function testGetUserDiagrams()
    {
        $user = User::factory()->create();
        $diagram = Diagram::factory()->create(['user_id' => $user->id]);


        $result = $this->service->getUserDiagrams($user);


        $this->assertEquals($diagram->id, $result[0]->id);
    }

    public function testCreateDiagram()
    {
        $data = ['name' => 'test', 'user_id' => User::factory()->create()->id];


        $result = $this->service->createDiagram($data);


        $this->assertDatabaseHas(Diagram::class, $result->toArray());
    }

    public function testUpdateDiagram()
    {
        $diagram = Diagram::factory()->create();


        $result = $this->service->updateDiagram($diagram, ['name' => 'Updated name']);


        $this->assertTrue($result);
    }

    public function testDeleteDiagram()
    {
        $diagram = Diagram::factory()->create();


        $result = $this->service->deleteDiagram($diagram);


        $this->assertTrue($result);
        $this->assertDatabaseMissing(Diagram::class, $diagram->toArray());
    }

    public function testCreateScriptGeneratesValidSQL()
    {
        $user = User::factory()->create();

        $schema = json_encode([
            [
                'id' => 'users_table',
                'type' => 'table',
                'label' => 'users'
            ],
            [
                'id' => 'posts_table',
                'type' => 'table',
                'label' => 'posts'
            ],
            [
                'id' => 'users_id',
                'type' => 'row',
                'label' => 'id',
                'parentNode' => 'users_table',
                'data' => [
                    'keyMod' => 'PRIMARY KEY',
                    'sqlType' => 'INT',
                    'nullable' => false,
                    'unsigned' => true
                ]
            ],
            [
                'id' => 'users_email',
                'type' => 'row',
                'label' => 'email',
                'parentNode' => 'users_table',
                'data' => [
                    'keyMod' => 'UNIQUE',
                    'sqlType' => 'VARCHAR(255)',
                    'nullable' => false,
                    'unsigned' => false
                ]
            ],
            [
                'id' => 'users_name',
                'type' => 'row',
                'label' => 'name',
                'parentNode' => 'users_table',
                'data' => [
                    'keyMod' => null,
                    'sqlType' => 'VARCHAR(255)',
                    'nullable' => false,
                    'unsigned' => false
                ]
            ],
            [
                'id' => 'posts_id',
                'type' => 'row',
                'label' => 'id',
                'parentNode' => 'posts_table',
                'data' => [
                    'keyMod' => 'PRIMARY KEY',
                    'sqlType' => 'INT',
                    'nullable' => false,
                    'unsigned' => true
                ]
            ],
            [
                'id' => 'posts_user_id',
                'type' => 'row',
                'label' => 'user_id',
                'parentNode' => 'posts_table',
                'data' => [
                    'keyMod' => null,
                    'sqlType' => 'INT',
                    'nullable' => false,
                    'unsigned' => true
                ]
            ],
            [
                'id' => 'posts_title',
                'type' => 'row',
                'label' => 'title',
                'parentNode' => 'posts_table',
                'data' => [
                    'keyMod' => null,
                    'sqlType' => 'VARCHAR(255)',
                    'nullable' => false,
                    'unsigned' => false
                ]
            ],
            [
                'type' => 'foreignKey',
                'source' => 'posts_user_id',
                'target' => 'users_id'
            ]
        ]);

        $diagram = Diagram::create([
            'name' => 'test',
            'schema' => $schema,
            'script' => null,
            'user_id' => $user->id,
        ]);

        $script = $this->service->createScript($diagram->schema);


        $this->validateSQLStructure($script);


        $statements = array_filter(array_map('trim', explode(';', $script)));
        $this->assertCount(3, $statements, 'Should generate 3 SQL statements');

        $this->assertStringContainsString('`', $script, 'Should use backticks (MySQL syntax)');
    }

    public function testCreateScriptWithComplexSchema(): void
    {
        $schema = json_encode([
            [
                'id' => 'orders_table',
                'type' => 'table',
                'label' => 'orders'
            ],
            [
                'id' => 'order_id',
                'type' => 'row',
                'label' => 'id',
                'parentNode' => 'orders_table',
                'data' => [
                    'keyMod' => 'PRIMARY KEY',
                    'sqlType' => 'BIGINT',
                    'nullable' => false,
                    'unsigned' => true
                ]
            ],
            [
                'id' => 'order_amount',
                'type' => 'row',
                'label' => 'amount',
                'parentNode' => 'orders_table',
                'data' => [
                    'keyMod' => null,
                    'sqlType' => 'DECIMAL(10,2)',
                    'nullable' => false,
                    'unsigned' => false
                ]
            ],
            [
                'id' => 'order_status',
                'type' => 'row',
                'label' => 'status',
                'parentNode' => 'orders_table',
                'data' => [
                    'keyMod' => null,
                    'sqlType' => 'VARCHAR(50)',
                    'nullable' => false,
                    'unsigned' => false
                ]
            ]
        ]);

        $script = $this->service->createScript($schema);
        $this->validateSQLStructure($script);

        $this->assertStringContainsString('DECIMAL(10,2)', $script);
    }

    public function testCreateScriptWithMultipleForeignKeys(): void
    {
        $schema = json_encode([
            [
                'id' => 't1',
                'type' => 'table',
                'label' => 'table1'
            ],
            [
                'id' => 't2',
                'type' => 'table',
                'label' => 'table2'
            ],
            [
                'id' => 't3',
                'type' => 'table',
                'label' => 'table3'
            ],
            [
                'id' => 't1_id',
                'type' => 'row',
                'label' => 'id',
                'parentNode' => 't1',
                'data' => [
                    'keyMod' => 'PRIMARY KEY',
                    'sqlType' => 'INT',
                    'nullable' => false,
                    'unsigned' => true
                ]
            ],
            [
                'id' => 't2_id',
                'type' => 'row',
                'label' => 'id',
                'parentNode' => 't2',
                'data' => [
                    'keyMod' => 'PRIMARY KEY',
                    'sqlType' => 'INT',
                    'nullable' => false,
                    'unsigned' => true
                ]
            ],
            [
                'id' => 't2_t1_id',
                'type' => 'row',
                'label' => 'table1_id',
                'parentNode' => 't2',
                'data' => [
                    'keyMod' => null,
                    'sqlType' => 'INT',
                    'nullable' => false,
                    'unsigned' => true
                ]
            ],
            [
                'id' => 't3_id',
                'type' => 'row',
                'label' => 'id',
                'parentNode' => 't3',
                'data' => [
                    'keyMod' => 'PRIMARY KEY',
                    'sqlType' => 'INT',
                    'nullable' => false,
                    'unsigned' => true
                ]
            ],
            [
                'id' => 't3_t2_id',
                'type' => 'row',
                'label' => 'table2_id',
                'parentNode' => 't3',
                'data' => [
                    'keyMod' => null,
                    'sqlType' => 'INT',
                    'nullable' => false,
                    'unsigned' => true
                ]
            ],
            [
                'type' => 'fk1',
                'source' => 't2_t1_id',
                'target' => 't1_id'
            ],
            [
                'type' => 'fk2',
                'source' => 't3_t2_id',
                'target' => 't2_id'
            ]
        ]);

        $script = $this->service->createScript($schema);
        $this->validateSQLStructure($script);

        $statements = array_filter(array_map('trim', explode(';', $script)));
        $fkStatements = array_filter($statements, fn($s) => str_starts_with($s, 'ALTER TABLE'));

        $this->assertCount(2, $fkStatements, 'Should have 2 foreign key statements');
    }

    public function testCreateScriptWithEmptySchema(): void
    {
        $emptySchema = json_encode([]);
        $script = $this->service->createScript($emptySchema);

        $this->assertEquals('', $script, 'Empty schema should generate empty script');
    }

    public function testCreateScriptHandlesNullableCorrectly(): void
    {
        $schema = json_encode([
            [
                'id' => 'table1',
                'type' => 'table',
                'label' => 'test'
            ],
            [
                'id' => 'col1',
                'type' => 'row',
                'label' => 'nullable_col',
                'parentNode' => 'table1',
                'data' => [
                    'keyMod' => null,
                    'sqlType' => 'VARCHAR(100)',
                    'nullable' => true,
                    'unsigned' => false
                ]
            ],
            [
                'id' => 'col2',
                'type' => 'row',
                'label' => 'not_nullable_col',
                'parentNode' => 'table1',
                'data' => [
                    'keyMod' => null,
                    'sqlType' => 'INT',
                    'nullable' => false,
                    'unsigned' => true
                ]
            ]
        ]);

        $script = $this->service->createScript($schema);
        $this->validateSQLStructure($script);

        $this->assertStringContainsString('`nullable_col` VARCHAR(100) NULL', $script);
        $this->assertStringContainsString('`not_nullable_col` INT UNSIGNED NOT NULL', $script);
    }

    public function testCreateScriptWithInvalidJsonThrowsException(): void
    {
        $this->expectException(\ErrorException::class);

        $this->service->createScript('{invalid json');
    }

    public function testCreateScriptWithValidButEmptyObjects(): void
    {
        $schema = json_encode([[]]);

        $this->expectException(\ErrorException::class);
        $this->service->createScript($schema);
    }

    private function validateSQLStructure(string $sql): void
    {
        $statements = array_filter(array_map('trim', explode(';', $sql)));

        foreach ($statements as $statement) {
            if (empty($statement)) {
                continue;
            }

            try {
                if (str_starts_with(strtoupper($statement), 'CREATE TABLE')) {
                    if (!preg_match('/CREATE TABLE IF NOT EXISTS (\w+)\s*\((.*)\)/is', $statement, $matches)) {
                        $this->fail("Invalid CREATE TABLE syntax: $statement");
                    }

                    $columnsPart = trim($matches[2]);

                    $this->assertNotEmpty($columnsPart, "CREATE TABLE must have column definitions");

                    $columnLines = preg_split('/,\s*\n/', $columnsPart);
                    $hasColumns = false;

                    foreach ($columnLines as $line) {
                        $line = trim($line);
                        if (!empty($line)) {
                            $hasColumns = true;

                            if (preg_match('/^`(\w+)`\s+(\w+(?:\(\d+(?:,\d+)?\))?)/', $line)) {
                                continue;
                            } elseif (preg_match('/^(PRIMARY KEY|UNIQUE|FOREIGN KEY)/i', $line)) {
                                continue;
                            } else {
                                $this->fail("Invalid line in CREATE TABLE: $line");
                            }
                        }
                    }

                    $this->assertTrue($hasColumns, "CREATE TABLE must have at least one column or constraint");
                } elseif (str_starts_with(strtoupper($statement), 'ALTER TABLE')) {
                    $pattern = '/ALTER TABLE (\w+)\s+ADD FOREIGN KEY\s*\(`?(\w+)`?\)\s+REFERENCES (\w+)\(`?(\w+)`?\)/i';

                    $this->assertMatchesRegularExpression(
                        $pattern,
                        $statement,
                        "Invalid ALTER TABLE (foreign key) syntax: $statement"
                    );
                } else {
                    $this->fail("Unexpected SQL statement type: $statement");
                }
            } catch (\Exception $e) {
                $this->fail("Invalid SQL structure in statement: '$statement'\nError: " . $e->getMessage());
            }
        }
    }
}