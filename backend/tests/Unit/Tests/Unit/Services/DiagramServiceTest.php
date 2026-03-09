<?php

namespace Tests\Unit\Services;

use App\Models\Diagram;
use App\Models\User;
use App\Services\DiagramService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
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

    public function testCreateScriptSingleTableIsExecutable()
    {
        $schema = json_encode([
            ['id' => 't1', 'type' => 'table', 'label' => 'products'],
            ['id' => 'r1', 'type' => 'row', 'label' => 'id', 'parentNode' => 't1', 'data' => ['keyMod' => 'PRIMARY KEY', 'sqlType' => 'INT', 'nullable' => false, 'unsigned' => true]],
            ['id' => 'r2', 'type' => 'row', 'label' => 'name', 'parentNode' => 't1', 'data' => ['keyMod' => null, 'sqlType' => 'VARCHAR(255)', 'nullable' => false, 'unsigned' => false]],
            ['id' => 'r3', 'type' => 'row', 'label' => 'price', 'parentNode' => 't1', 'data' => ['keyMod' => null, 'sqlType' => 'DECIMAL(10,2)', 'nullable' => false, 'unsigned' => false]],
        ]);

        $script = $this->service->createScript($schema);

        $this->assertNotEmpty($script);
        $this->executeSQLAndValidate($script);
    }

    public function testCreateScriptColumnModifiersAreExecutable()
    {
        $schema = json_encode([
            ['id' => 't1', 'type' => 'table', 'label' => 'orders'],
            ['id' => 'r1', 'type' => 'row', 'label' => 'id', 'parentNode' => 't1', 'data' => ['keyMod' => 'PRIMARY KEY', 'sqlType' => 'BIGINT', 'nullable' => false, 'unsigned' => true]],
            ['id' => 'r2', 'type' => 'row', 'label' => 'ref_code', 'parentNode' => 't1', 'data' => ['keyMod' => 'UNIQUE', 'sqlType' => 'VARCHAR(64)', 'nullable' => false, 'unsigned' => false]],
            ['id' => 'r3', 'type' => 'row', 'label' => 'total', 'parentNode' => 't1', 'data' => ['keyMod' => null, 'sqlType' => 'DECIMAL(12,4)', 'nullable' => false, 'unsigned' => true]],
            ['id' => 'r4', 'type' => 'row', 'label' => 'note', 'parentNode' => 't1', 'data' => ['keyMod' => null, 'sqlType' => 'TEXT', 'nullable' => true, 'unsigned' => false]],
        ]);

        $script = $this->service->createScript($schema);

        $this->assertNotEmpty($script);
        $this->executeSQLAndValidate($script);
    }

    public function testCreateScriptForeignKeyIsExecutable()
    {
        $schema = json_encode([
            ['id' => 't1', 'type' => 'table', 'label' => 'users'],
            ['id' => 't2', 'type' => 'table', 'label' => 'posts'],
            ['id' => 'r1', 'type' => 'row', 'label' => 'id', 'parentNode' => 't1', 'data' => ['keyMod' => 'PRIMARY KEY', 'sqlType' => 'INT', 'nullable' => false, 'unsigned' => true]],
            ['id' => 'r2', 'type' => 'row', 'label' => 'name', 'parentNode' => 't1', 'data' => ['keyMod' => null, 'sqlType' => 'VARCHAR(255)', 'nullable' => false, 'unsigned' => false]],
            ['id' => 'r3', 'type' => 'row', 'label' => 'id', 'parentNode' => 't2', 'data' => ['keyMod' => 'PRIMARY KEY', 'sqlType' => 'INT', 'nullable' => false, 'unsigned' => true]],
            ['id' => 'r4', 'type' => 'row', 'label' => 'user_id', 'parentNode' => 't2', 'data' => ['keyMod' => null, 'sqlType' => 'INT', 'nullable' => false, 'unsigned' => true]],
            ['id' => 'r5', 'type' => 'row', 'label' => 'title', 'parentNode' => 't2', 'data' => ['keyMod' => null, 'sqlType' => 'VARCHAR(255)', 'nullable' => false, 'unsigned' => false]],
            ['sourceNode' => ['id' => 'r4'], 'targetNode' => ['id' => 'r1']],
        ]);

        $script = $this->service->createScript($schema);

        $this->assertNotEmpty($script);
        $this->executeSQLAndValidate($script);
    }

    public function testCreateScriptMultipleForeignKeysAreExecutable()
    {
        $schema = json_encode([
            ['id' => 't1', 'type' => 'table', 'label' => 'authors'],
            ['id' => 't2', 'type' => 'table', 'label' => 'categories'],
            ['id' => 't3', 'type' => 'table', 'label' => 'books'],
            ['id' => 'r1', 'type' => 'row', 'label' => 'id', 'parentNode' => 't1', 'data' => ['keyMod' => 'PRIMARY KEY', 'sqlType' => 'INT', 'nullable' => false, 'unsigned' => false]],
            ['id' => 'r2', 'type' => 'row', 'label' => 'id', 'parentNode' => 't2', 'data' => ['keyMod' => 'PRIMARY KEY', 'sqlType' => 'INT', 'nullable' => false, 'unsigned' => false]],
            ['id' => 'r3', 'type' => 'row', 'label' => 'id', 'parentNode' => 't3', 'data' => ['keyMod' => 'PRIMARY KEY', 'sqlType' => 'INT', 'nullable' => false, 'unsigned' => false]],
            ['id' => 'r4', 'type' => 'row', 'label' => 'author_id', 'parentNode' => 't3', 'data' => ['keyMod' => null, 'sqlType' => 'INT', 'nullable' => false, 'unsigned' => false]],
            ['id' => 'r5', 'type' => 'row', 'label' => 'category_id', 'parentNode' => 't3', 'data' => ['keyMod' => null, 'sqlType' => 'INT', 'nullable' => false, 'unsigned' => false]],
            ['sourceNode' => ['id' => 'r4'], 'targetNode' => ['id' => 'r1']],
            ['sourceNode' => ['id' => 'r5'], 'targetNode' => ['id' => 'r2']],
        ]);

        $script = $this->service->createScript($schema);

        $this->assertNotEmpty($script);
        $this->executeSQLAndValidate($script);
    }

    public function testCreateSchemaFromBasicSQL()
    {
        $sql = "CREATE TABLE users (
                    id INT UNSIGNED NOT NULL PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    email VARCHAR(255) NOT NULL UNIQUE
                );
    
                CREATE TABLE posts (
                        id INT UNSIGNED NOT NULL PRIMARY KEY,
                        user_id INT UNSIGNED NOT NULL,
                        title VARCHAR(255) NOT NULL
                );
    
                ALTER TABLE posts
                ADD FOREIGN KEY (user_id) REFERENCES users(id);";

        $schema = $this->service->createSchema($sql);

        $this->assertJson($schema);

        $schemaArray = json_decode($schema, true);

        $this->assertIsArray($schemaArray);

        $tables = array_filter($schemaArray, fn($item) => isset($item['type']) && $item['type'] === 'table');
        $this->assertCount(2, $tables);

        $tableNames = array_column($tables, 'label');
        $this->assertContains('users', $tableNames);
        $this->assertContains('posts', $tableNames);

        $rows = array_filter($schemaArray, fn($item) => isset($item['type']) && $item['type'] === 'row');
        $this->assertCount(6, $rows);

        $primaryKeys = array_filter($rows, fn($row) => isset($row['data']['keyMod']) && $row['data']['keyMod'] === 'PRIMARY KEY');
        $this->assertCount(2, $primaryKeys);

        $uniqueKeys = array_filter($rows, fn($row) => isset($row['data']['keyMod']) && $row['data']['keyMod'] === 'UNIQUE');
        $this->assertCount(1, $uniqueKeys);

        $connections = array_filter($schemaArray, fn($item) => isset($item['source']) && isset($item['target']));
        $this->assertCount(1, $connections);
    }

    public function testCreateSchemaWithSeparateConstraints()
    {
        $sql = "CREATE TABLE products (
                    id INT NOT NULL,
                    name VARCHAR(100) NOT NULL,
                    price DECIMAL(10,2) NOT NULL,
                    PRIMARY KEY (id),
                    UNIQUE (name)
                );";
        $schema = $this->service->createSchema($sql);

        $schemaArray = json_decode($schema, true);

        $rows = array_filter($schemaArray, fn($item) => isset($item['type']) && $item['type'] === 'row');

        $idRow = null;
        foreach ($rows as $row) {
            if ($row['label'] === 'id') {
                $idRow = $row;
                break;
            }
        }

        $this->assertNotNull($idRow, 'Should find id row');
        $this->assertEquals('PRIMARY KEY', $idRow['data']['keyMod'] ?? null, "ID row should have PRIMARY KEY constraint");

        $nameRow = null;
        foreach ($rows as $row) {
            if ($row['label'] === 'name') {
                $nameRow = $row;
                break;
            }
        }

        $this->assertNotNull($nameRow, 'Should find name row');
        $this->assertEquals('UNIQUE', $nameRow['data']['keyMod'] ?? null, "Name row should have UNIQUE constraint");
    }


    public function testCreateSchemaWithNullableColumns()
    {
        $sql = "CREATE TABLE items (
                    id INT PRIMARY KEY,
                    name VARCHAR(100) NOT NULL,
                    description TEXT NULL,
                    created_at TIMESTAMP NULL
                );";

        $schema = $this->service->createSchema($sql);
        $schemaArray = json_decode($schema, true);

        $rows = array_filter($schemaArray, fn($item) => $item['type'] === 'row');

        $nameRow = current(array_filter($rows, fn($row) => $row['label'] === 'name'));
        $this->assertFalse($nameRow['data']['nullable']);

        $descRow = current(array_filter($rows, fn($row) => $row['label'] === 'description'));
        $this->assertTrue($descRow['data']['nullable']);
    }

    public function testCreateSchemaWithComplexDataTypes()
    {
        $sql = "CREATE TABLE transactions (
                    id BIGINT UNSIGNED NOT NULL PRIMARY KEY,
                    amount DECIMAL(12,4) NOT NULL,
                    status ENUM('pending', 'completed', 'failed') NOT NULL,
                    metadata JSON NULL,
                    created_at DATETIME(6) NOT NULL
                );";

        $schema = $this->service->createSchema($sql);

        $this->assertJson($schema);

        $schemaArray = json_decode($schema, true);
        $rows = array_filter($schemaArray, fn($item) => isset($item['type']) && $item['type'] === 'row');

        $this->assertCount(5, $rows);

        $amountRow = null;
        foreach ($rows as $row) {
            if ($row['label'] === 'amount') {
                $amountRow = $row;
                break;
            }
        }

        $this->assertNotNull($amountRow, 'Should find amount row');
        $this->assertEquals('DECIMAL(12,4)', $amountRow['data']['sqlType']);

        $statusRow = null;
        foreach ($rows as $row) {
            if ($row['label'] === 'status') {
                $statusRow = $row;
                break;
            }
        }

        $this->assertNotNull($statusRow, 'Should find status row');
        $this->assertEquals("ENUM('pending', 'completed', 'failed')", $statusRow['data']['sqlType']);

        // Check DATETIME with precision
        $createdAtRow = null;
        foreach ($rows as $row) {
            if ($row['label'] === 'created_at') {
                $createdAtRow = $row;
                break;
            }
        }

        $this->assertNotNull($createdAtRow, 'Should find created_at row');
        $this->assertEquals('DATETIME(6)', $createdAtRow['data']['sqlType']);
    }

    public function testCreateSchemaWithMultipleForeignKeys()
    {
        $sql = "CREATE TABLE authors (
                    id INT PRIMARY KEY,
                    name VARCHAR(100) NOT NULL
                 );
        
                CREATE TABLE categories (
                    id INT PRIMARY KEY,
                    name VARCHAR(100) NOT NULL
                );
        
                CREATE TABLE books (
                    id INT PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    author_id INT NOT NULL,
                    category_id INT NOT NULL
                );
        
                ALTER TABLE books
                    ADD FOREIGN KEY (author_id) REFERENCES authors(id);
        
                ALTER TABLE books
                    ADD FOREIGN KEY (category_id) REFERENCES categories(id);";

        $schema = $this->service->createSchema($sql);
        $schemaArray = json_decode($schema, true);

        $connections = array_filter($schemaArray, fn($item) => isset($item['source']) && isset($item['target']));

        $this->assertCount(2, $connections, "Should have 2 foreign key connections");
    }

    public function testCreateSchemaWithIfNotExistsClause()
    {
        $sql = "CREATE TABLE IF NOT EXISTS users (
                    id INT PRIMARY KEY,
                    name VARCHAR(100) NOT NULL
                );";

        $schema = $this->service->createSchema($sql);

        $this->assertJson($schema);

        $schemaArray = json_decode($schema, true);
        $tables = array_filter($schemaArray, fn($item) => $item['type'] === 'table');

        $this->assertCount(1, $tables);
        $this->assertEquals('users', reset($tables)['label']);
    }

    public function testCreateSchemaWithBackticks()
    {
        $sql = "CREATE TABLE `users` (
                    `id` INT UNSIGNED NOT NULL PRIMARY KEY,
                    `full_name` VARCHAR(255) NOT NULL
                );";

        $schema = $this->service->createSchema($sql);

        $this->assertJson($schema);

        $schemaArray = json_decode($schema, true);
        $rows = array_filter($schemaArray, fn($item) => $item['type'] === 'row');

        $idRow = current(array_filter($rows, fn($row) => $row['label'] === 'id'));
        $this->assertEquals('INT', $idRow['data']['sqlType']);

        $nameRow = current(array_filter($rows, fn($row) => $row['label'] === 'full_name'));
        $this->assertEquals('VARCHAR(255)', $nameRow['data']['sqlType']);
    }

    public function testCreateSchemaEmptyScript()
    {
        $sql = "";
        $schema = $this->service->createSchema($sql);

        $this->assertEquals('[]', $schema);
    }

    public function testCreateSchemaInvalidSQL()
    {
        $sql = "INVALID SQL SYNTAX HERE";
        $schema = $this->service->createSchema($sql);

        $this->assertEquals('[]', $schema);
    }


    public function testRoundTripConversion()
    {
        $originalSchema = json_encode([
            [
                'id' => 't1',
                'type' => 'table',
                'label' => 'users'
            ],
            [
                'id' => 't2',
                'type' => 'table',
                'label' => 'posts'
            ],
            [
                'id' => 'r1',
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
                'id' => 'r2',
                'type' => 'row',
                'label' => 'name',
                'parentNode' => 't1',
                'data' => [
                    'keyMod' => null,
                    'sqlType' => 'VARCHAR(255)',
                    'nullable' => false,
                    'unsigned' => false
                ]
            ],
            [
                'id' => 'r3',
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
                'id' => 'r4',
                'type' => 'row',
                'label' => 'user_id',
                'parentNode' => 't2',
                'data' => [
                    'keyMod' => null,
                    'sqlType' => 'INT',
                    'nullable' => false,
                    'unsigned' => true
                ]
            ],
            [
                'id' => 'r5',
                'type' => 'row',
                'label' => 'title',
                'parentNode' => 't2',
                'data' => [
                    'keyMod' => null,
                    'sqlType' => 'VARCHAR(255)',
                    'nullable' => false,
                    'unsigned' => false
                ]
            ],
            [
                'sourceNode' => ['id' => 'r4'],
                'targetNode' => ['id' => 'r1'],
            ]
        ]);


        $sql = $this->service->createScript($originalSchema);

        $this->executeSQLAndValidate($sql);

        $newSchema = $this->service->createSchema($sql);

        $originalArray = json_decode($originalSchema, true);
        $newArray = json_decode($newSchema, true);

        $originalTables = array_filter($originalArray, fn($item) => isset($item['type']) && $item['type'] === 'table');
        $newTables = array_filter($newArray, fn($item) => isset($item['type']) && $item['type'] === 'table');

        $this->assertEqualsCanonicalizing(
            array_column($originalTables, 'label'),
            array_column($newTables, 'label'),
            "Table names should match"
        );

        $originalRows = array_filter($originalArray, fn($item) => isset($item['type']) && $item['type'] === 'row');
        $newRows = array_filter($newArray, fn($item) => isset($item['type']) && $item['type'] === 'row');

        $this->assertCount(count($originalRows), $newRows, "Should have same number of rows. Original: " . count($originalRows) . ", New: " . count($newRows));

        $isConnection = fn($item) => (isset($item['sourceNode']) && isset($item['targetNode']))
            || (isset($item['source']) && isset($item['target']) && !isset($item['type']));
        $originalConnections = array_filter($originalArray, $isConnection);
        $newConnections = array_filter($newArray, $isConnection);

        $this->assertCount(count($originalConnections), $newConnections, "Should have same number of foreign key connections");
    }


    private function executeSQLAndValidate(string $sql): void
    {
        $connection = DB::connection('mysql_validation');
        $createdTables = [];

        try {
            $statements = array_filter(array_map('trim', explode(';', $sql)));

            foreach ($statements as $statement) {
                if (empty($statement)) {
                    continue;
                }

                try {
                    $connection->statement($statement);
                } catch (\Exception $e) {
                    $this->fail("MySQL rejected statement:\n\n$statement\n\nError: " . $e->getMessage());
                }

                if (preg_match('/CREATE\s+TABLE(?:\s+IF\s+NOT\s+EXISTS)?\s+`?(\w+)`?/i', $statement, $matches)) {
                    $createdTables[] = $matches[1];
                }
            }
        } finally {
            $connection->statement('SET FOREIGN_KEY_CHECKS=0');
            foreach (array_reverse($createdTables) as $table) {
                $connection->statement("DROP TABLE IF EXISTS `$table`");
            }
            $connection->statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }
}