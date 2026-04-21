<?php

namespace Tests\Unit\Services;

use App\Models\Diagram;
use App\Services\DiagramSqlService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DiagramSqlServiceTest extends TestCase
{
    use DatabaseTransactions;

    private DiagramSqlService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = app(DiagramSqlService::class);
    }

    // --- importSchema / exportScript ---

    public function testImportSchema(): void
    {
        $diagram = Diagram::factory()->create();
        $sql = "CREATE TABLE users (id INT PRIMARY KEY, name VARCHAR(255) NOT NULL);";
        $schema = $this->service->importSchema($diagram, json_encode($sql));
        $arr = json_decode($schema, true);
        $this->assertCount(1, array_filter($arr, fn($i) => ($i['type'] ?? null) === 'table'));
    }

    public function testExportScript(): void
    {
        $schema = json_encode([
            ['id' => 't1', 'type' => 'table', 'label' => 'users'],
            ['id' => 'r1', 'type' => 'row', 'label' => 'id', 'parentNode' => 't1', 'data' => ['keyMod' => 'PRIMARY KEY', 'sqlType' => 'INT', 'nullable' => false, 'unsigned' => false]],
        ]);
        $diagram = Diagram::factory()->create(['schema' => $schema, 'db_type' => 'mysql']);
        $result = $this->service->exportScript($diagram);
        $this->assertJson($result);
        $this->assertStringContainsString('users', json_decode($result));
    }

    public function testCreateScriptMySQLSkipsInvalidConnection(): void
    {
        $schema = json_encode([
            ['id' => 't1', 'type' => 'table', 'label' => 'users'],
            ['id' => 'r1', 'type' => 'row', 'label' => 'id', 'parentNode' => 't1', 'data' => ['keyMod' => 'PRIMARY KEY', 'sqlType' => 'INT', 'nullable' => false, 'unsigned' => false]],
            ['sourceNode' => ['id' => 'nonexistent'], 'targetNode' => ['id' => 'r1']],
            ['ignored_item' => true],
        ]);
        $script = $this->service->createScript($schema);
        $this->assertStringNotContainsString('FOREIGN KEY', $script);
    }

    public function testCreateScriptMySQLUniqueTogetherInvalidColsSkipped(): void
    {
        $schema = json_encode([
            ['id' => 't1', 'type' => 'table', 'label' => 'things', 'data' => ['uniqueTogether' => [['nonexistent']]]],
            ['id' => 'r1', 'type' => 'row', 'label' => 'id', 'parentNode' => 't1', 'data' => ['keyMod' => 'PRIMARY KEY', 'sqlType' => 'INT', 'nullable' => false, 'unsigned' => false]],
        ]);
        $this->assertStringNotContainsString('UNIQUE KEY', $this->service->createScript($schema));
    }

    public function testCreateScriptSkipsIndexLikeRows(): void
    {
        $schema = json_encode([
            ['id' => 't1', 'type' => 'table', 'label' => 'tbl'],
            ['id' => 'r1', 'type' => 'row', 'label' => 'id', 'parentNode' => 't1', 'data' => ['keyMod' => 'PRIMARY KEY', 'sqlType' => 'INT', 'nullable' => false, 'unsigned' => false]],
            ['id' => 'r2', 'type' => 'row', 'label' => 'idx', 'parentNode' => 't1', 'data' => ['keyMod' => null, 'sqlType' => 'INDEX', 'nullable' => false, 'unsigned' => false]],
        ]);
        $script = $this->service->createScript($schema);
        $this->assertStringNotContainsString('`idx`', $script);
    }

    // --- createScript PostgreSQL ---

    public function testCreateScriptPostgresqlIdentifiers(): void
    {
        $schema = json_encode([
            ['id' => 't1', 'type' => 'table', 'label' => 'users'],
            ['id' => 'r1', 'type' => 'row', 'label' => 'id', 'parentNode' => 't1', 'data' => ['keyMod' => 'PRIMARY KEY', 'sqlType' => 'SERIAL', 'nullable' => false, 'unsigned' => false]],
        ]);
        $script = $this->service->createScript($schema, 'postgresql');
        $this->assertStringContainsString('"users"', $script);
        $this->assertStringNotContainsString('`', $script);
    }

    public function testCreateScriptPostgresqlStripsUnsigned(): void
    {
        $schema = json_encode([
            ['id' => 't1', 'type' => 'table', 'label' => 'products'],
            ['id' => 'r1', 'type' => 'row', 'label' => 'id', 'parentNode' => 't1', 'data' => ['keyMod' => 'PRIMARY KEY', 'sqlType' => 'INTEGER', 'nullable' => false, 'unsigned' => true]],
        ]);
        $this->assertStringNotContainsString('UNSIGNED', $this->service->createScript($schema, 'postgresql'));
    }


    // --- createJson ---

    public function testCreateJson(): void
    {
        $schema = json_encode([
            ['id' => 't1', 'type' => 'table', 'label' => 'users'],
            ['id' => 't2', 'type' => 'table', 'label' => 'posts'],
            ['id' => 'r1', 'type' => 'row', 'label' => 'id', 'parentNode' => 't1', 'data' => ['keyMod' => 'PRIMARY KEY', 'sqlType' => 'INT', 'nullable' => false, 'unsigned' => true, 'defaultValue' => '5', 'comment' => 'pk']],
            ['id' => 'r2', 'type' => 'row', 'label' => 'user_id', 'parentNode' => 't2', 'data' => ['keyMod' => null, 'sqlType' => 'INT', 'nullable' => false, 'unsigned' => false, 'defaultValue' => '', 'comment' => '']],
            ['sourceNode' => ['id' => 'r2'], 'targetNode' => ['id' => 'r1']],
        ]);
        $result = json_decode($this->service->createJson($schema), true);
        $this->assertCount(2, $result['tables']);
        $this->assertCount(1, $result['foreignKeys']);
        $col = $result['tables'][0]['columns'][0];
        $this->assertTrue($col['unsigned']);
        $this->assertEquals('PRIMARY KEY', $col['key']);
        $this->assertEquals('5', $col['default_value']);
        $this->assertEquals('pk', $col['comment']);
        $this->assertEquals('posts', $result['foreignKeys'][0]['table']);
    }

    public function testCreateJsonSkipsInvalidConnection(): void
    {
        $schema = json_encode([
            ['id' => 't1', 'type' => 'table', 'label' => 'users'],
            ['id' => 'r1', 'type' => 'row', 'label' => 'id', 'parentNode' => 't1', 'data' => ['keyMod' => 'PRIMARY KEY', 'sqlType' => 'INT', 'nullable' => false, 'unsigned' => false]],
            ['sourceNode' => ['id' => 'bad'], 'targetNode' => ['id' => 'r1']],
        ]);
        $result = json_decode($this->service->createJson($schema), true);
        $this->assertEmpty($result['foreignKeys']);
    }

    // --- createSchema MySQL ---

    public function testCreateSchemaMySQLBasic(): void
    {
        $sql = "CREATE TABLE users (id INT UNSIGNED NOT NULL PRIMARY KEY, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL UNIQUE);
                CREATE TABLE posts (id INT UNSIGNED NOT NULL PRIMARY KEY, user_id INT UNSIGNED NOT NULL);
                ALTER TABLE posts ADD FOREIGN KEY (user_id) REFERENCES users(id);";
        $arr = json_decode($this->service->createSchema($sql), true);
        $this->assertCount(2, array_filter($arr, fn($i) => ($i['type'] ?? null) === 'table'));
        $this->assertCount(5, array_filter($arr, fn($i) => ($i['type'] ?? null) === 'row'));
        $this->assertCount(1, array_filter($arr, fn($i) => isset($i['source'], $i['target'])));
    }

    public function testCreateSchemaMySQLSeparateConstraints(): void
    {
        $sql = "CREATE TABLE products (id INT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY (id), UNIQUE (name));";
        $arr = json_decode($this->service->createSchema($sql), true);
        $rows = array_column(array_filter($arr, fn($i) => ($i['type'] ?? null) === 'row'), null, 'label');
        $this->assertEquals('PRIMARY KEY', $rows['id']['data']['keyMod']);
        $this->assertEquals('UNIQUE', $rows['name']['data']['keyMod']);
    }

    public function testCreateSchemaMySQLUniqueTogether(): void
    {
        $sql = "CREATE TABLE t (id INT NOT NULL, a INT NOT NULL, b INT NOT NULL, PRIMARY KEY (id), UNIQUE (a, b));";
        $arr = json_decode($this->service->createSchema($sql), true);
        $tables = array_filter($arr, fn($i) => ($i['type'] ?? null) === 'table');
        $table = reset($tables);
        $this->assertCount(2, $table['data']['uniqueTogether'][0]);
    }

    public function testCreateSchemaMySQLNullable(): void
    {
        $sql = "CREATE TABLE items (id INT PRIMARY KEY, name VARCHAR(100) NOT NULL, note TEXT NULL);";
        $arr = json_decode($this->service->createSchema($sql), true);
        $rows = array_column(array_filter($arr, fn($i) => ($i['type'] ?? null) === 'row'), null, 'label');
        $this->assertFalse($rows['name']['data']['nullable']);
        $this->assertTrue($rows['note']['data']['nullable']);
    }

    public function testCreateSchemaMySQLComplexTypes(): void
    {
        $sql = "CREATE TABLE t (id BIGINT UNSIGNED NOT NULL PRIMARY KEY, amt DECIMAL(12,4) NOT NULL, status ENUM('a','b') NOT NULL, meta JSON NULL, ts DATETIME(6) NOT NULL);";
        $arr = json_decode($this->service->createSchema($sql), true);
        $rows = array_column(array_filter($arr, fn($i) => ($i['type'] ?? null) === 'row'), null, 'label');
        $this->assertEquals('DECIMAL(12,4)', $rows['amt']['data']['sqlType']);
        $this->assertEquals("ENUM('a','b')", $rows['status']['data']['sqlType']);
        $this->assertEquals('DATETIME(6)', $rows['ts']['data']['sqlType']);
    }

    public function testCreateSchemaMySQLIfNotExists(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS users (id INT PRIMARY KEY, name VARCHAR(100) NOT NULL);";
        $arr = json_decode($this->service->createSchema($sql), true);
        $tables = array_filter($arr, fn($i) => ($i['type'] ?? null) === 'table');
        $this->assertCount(1, $tables);
        $this->assertEquals('users', reset($tables)['label']);
    }

    public function testCreateSchemaMySQLBackticks(): void
    {
        $sql = "CREATE TABLE `users` (`id` INT UNSIGNED NOT NULL PRIMARY KEY, `full_name` VARCHAR(255) NOT NULL);";
        $arr = json_decode($this->service->createSchema($sql), true);
        $rows = array_column(array_filter($arr, fn($i) => ($i['type'] ?? null) === 'row'), null, 'label');
        $this->assertEquals('INT', $rows['id']['data']['sqlType']);
        $this->assertEquals('VARCHAR(255)', $rows['full_name']['data']['sqlType']);
    }

    public function testCreateSchemaMySQLInlineForeignKey(): void
    {
        $sql = "CREATE TABLE users (id INT PRIMARY KEY);
                CREATE TABLE posts (id INT PRIMARY KEY, user_id INT, FOREIGN KEY (user_id) REFERENCES users(id));";
        $arr = json_decode($this->service->createSchema($sql), true);
        $this->assertCount(1, array_filter($arr, fn($i) => isset($i['source'], $i['target'])));
    }

    public function testCreateSchemaMySQLEmpty(): void
    {
        $this->assertEquals('[]', $this->service->createSchema(''));
    }

    public function testCreateSchemaMySQLInvalid(): void
    {
        $this->assertEquals('[]', $this->service->createSchema('INVALID SQL'));
    }

    // --- createSchema PostgreSQL ---

    public function testCreateSchemaPostgresqlBasic(): void
    {
        $sql = 'CREATE TABLE IF NOT EXISTS "users" ("id" SERIAL NOT NULL PRIMARY KEY, "name" VARCHAR(255) NOT NULL);
                CREATE TABLE IF NOT EXISTS "posts" ("id" SERIAL NOT NULL PRIMARY KEY, "user_id" INTEGER NOT NULL);
                ALTER TABLE "posts" ADD FOREIGN KEY ("user_id") REFERENCES "users"("id");';
        $arr = json_decode($this->service->createSchema($sql), true);
        $this->assertCount(2, array_filter($arr, fn($i) => ($i['type'] ?? null) === 'table'));
        $this->assertCount(1, array_filter($arr, fn($i) => isset($i['source'], $i['target'])));
    }

    public function testCreateSchemaPostgresqlTypes(): void
    {
        $sql = 'CREATE TABLE "items" ("id" BIGSERIAL NOT NULL PRIMARY KEY, "score" NUMERIC(10,2) NOT NULL, "active" BOOLEAN NOT NULL, "meta" JSONB NULL);';
        $arr = json_decode($this->service->createSchema($sql), true);
        $rows = array_column(array_filter($arr, fn($i) => ($i['type'] ?? null) === 'row'), null, 'label');
        $this->assertEquals('BIGSERIAL', $rows['id']['data']['sqlType']);
        $this->assertEquals('NUMERIC(10,2)', $rows['score']['data']['sqlType']);
        $this->assertTrue($rows['meta']['data']['nullable']);
        $this->assertFalse($rows['active']['data']['nullable']);
    }

    public function testCreateSchemaPostgresqlTableConstraints(): void
    {
        $sql = 'CREATE TABLE "products" ("id" INTEGER NOT NULL, "sku" VARCHAR(64) NOT NULL, PRIMARY KEY ("id"), UNIQUE ("sku"));';
        $arr = json_decode($this->service->createSchema($sql), true);
        $rows = array_column(array_filter($arr, fn($i) => ($i['type'] ?? null) === 'row'), null, 'label');
        $this->assertEquals('PRIMARY KEY', $rows['id']['data']['keyMod']);
        $this->assertEquals('UNIQUE', $rows['sku']['data']['keyMod']);
    }

    // --- createMigration ---

    public function testCreateMigrationAllColumnTypes(): void
    {
        $row = fn(string $id, string $label, string $type, bool $nullable = false, bool $unsigned = false, string $key = null) => [
            'id' => $id, 'type' => 'row', 'label' => $label, 'parentNode' => 't1',
            'data' => ['keyMod' => $key, 'sqlType' => $type, 'nullable' => $nullable, 'unsigned' => $unsigned, 'defaultValue' => null, 'comment' => null],
        ];

        $schema = json_encode([
            ['id' => 't1', 'type' => 'table', 'label' => 'all_types'],
            $row('r1', 'c_bool_tiny', 'TINYINT(1)', false, false, 'PRIMARY KEY'),
            $row('r2', 'c_utinyint', 'TINYINT', false, true),
            $row('r3', 'c_tinyint', 'TINYINT'),
            $row('r4', 'c_usmallint', 'SMALLINT', false, true),
            $row('r5', 'c_smallint', 'SMALLINT'),
            $row('r6', 'c_umediumint', 'MEDIUMINT', false, true),
            $row('r7', 'c_mediumint', 'MEDIUMINT'),
            $row('r8', 'c_ubigint', 'BIGINT', false, true),
            $row('r9', 'c_bigint', 'BIGINT'),
            $row('r10', 'c_uint', 'INT', false, true),
            $row('r11', 'c_int', 'INT'),
            $row('r12', 'c_varchar100', 'VARCHAR(100)'),
            $row('r13', 'c_varchar255', 'VARCHAR(255)'),
            $row('r14', 'c_char', 'CHAR(10)'),
            $row('r15', 'c_char_bare', 'CHAR'),
            $row('r16', 'c_longtext', 'LONGTEXT'),
            $row('r17', 'c_medtext', 'MEDIUMTEXT'),
            $row('r18', 'c_tinytext', 'TINYTEXT'),
            $row('r19', 'c_text', 'TEXT', true),
            $row('r20', 'c_dec_scale', 'DECIMAL(10,2)'),
            $row('r21', 'c_dec_prec', 'DECIMAL(10)'),
            $row('r22', 'c_dec_bare', 'DECIMAL'),
            $row('r23', 'c_double', 'DOUBLE'),
            $row('r24', 'c_float', 'FLOAT'),
            $row('r25', 'c_datetime', 'DATETIME'),
            $row('r26', 'c_timestamp', 'TIMESTAMP'),
            $row('r27', 'c_date', 'DATE'),
            $row('r28', 'c_time', 'TIME'),
            $row('r29', 'c_year', 'YEAR'),
            $row('r30', 'c_bool', 'BOOL'),
            $row('r31', 'c_json', 'JSON'),
            $row('r32', 'c_blob', 'BLOB'),
            $row('r33', 'c_enum', "ENUM('a','b')"),
            $row('r34', 'c_serial', 'SERIAL'),
            $row('r35', 'c_idx', 'INDEX'),
        ]);

        $files = $this->service->createMigration($schema);
        $this->assertCount(1, $files);
        $content = $files[0]['content'];

        $this->assertStringContainsString("boolean('c_bool_tiny')", $content);
        $this->assertStringContainsString("unsignedTinyInteger('c_utinyint')", $content);
        $this->assertStringContainsString("tinyInteger('c_tinyint')", $content);
        $this->assertStringContainsString("unsignedSmallInteger('c_usmallint')", $content);
        $this->assertStringContainsString("smallInteger('c_smallint')", $content);
        $this->assertStringContainsString("unsignedMediumInteger('c_umediumint')", $content);
        $this->assertStringContainsString("mediumInteger('c_mediumint')", $content);
        $this->assertStringContainsString("unsignedBigInteger('c_ubigint')", $content);
        $this->assertStringContainsString("bigInteger('c_bigint')", $content);
        $this->assertStringContainsString("unsignedInteger('c_uint')", $content);
        $this->assertStringContainsString("integer('c_int')", $content);
        $this->assertStringContainsString("string('c_varchar100', 100)", $content);
        $this->assertStringContainsString("string('c_varchar255')", $content);
        $this->assertStringContainsString("char('c_char', 10)", $content);
        $this->assertStringContainsString("char('c_char_bare')", $content);
        $this->assertStringContainsString("longText('c_longtext')", $content);
        $this->assertStringContainsString("mediumText('c_medtext')", $content);
        $this->assertStringContainsString("tinyText('c_tinytext')", $content);
        $this->assertStringContainsString("text('c_text')", $content);
        $this->assertStringContainsString("decimal('c_dec_scale', 10, 2)", $content);
        $this->assertStringContainsString("decimal('c_dec_prec', 10)", $content);
        $this->assertStringContainsString("decimal('c_dec_bare')", $content);
        $this->assertStringContainsString("double('c_double')", $content);
        $this->assertStringContainsString("float('c_float')", $content);
        $this->assertStringContainsString("dateTime('c_datetime')", $content);
        $this->assertStringContainsString("timestamp('c_timestamp')", $content);
        $this->assertStringContainsString("date('c_date')", $content);
        $this->assertStringContainsString("time('c_time')", $content);
        $this->assertStringContainsString("year('c_year')", $content);
        $this->assertStringContainsString("boolean('c_bool')", $content);
        $this->assertStringContainsString("json('c_json')", $content);
        $this->assertStringContainsString("binary('c_blob')", $content);
        $this->assertStringContainsString("enum('c_enum',", $content);
        $this->assertStringContainsString("string('c_serial')", $content);
        $this->assertStringContainsString("->nullable()", $content);
        $this->assertStringContainsString("->primary()", $content);
        $this->assertStringNotContainsString("c_idx", $content);
    }

    public function testCreateMigrationColumnModifiers(): void
    {
        $schema = json_encode([
            ['id' => 't1', 'type' => 'table', 'label' => 'orders'],
            ['id' => 'r1', 'type' => 'row', 'label' => 'status', 'parentNode' => 't1', 'data' => ['keyMod' => null, 'sqlType' => 'VARCHAR(50)', 'nullable' => true, 'unsigned' => false, 'defaultValue' => 'pending', 'comment' => 'order status']],
            ['id' => 'r2', 'type' => 'row', 'label' => 'qty', 'parentNode' => 't1', 'data' => ['keyMod' => null, 'sqlType' => 'INT', 'nullable' => false, 'unsigned' => false, 'defaultValue' => '1', 'comment' => null]],
            ['id' => 'r3', 'type' => 'row', 'label' => 'deleted_at', 'parentNode' => 't1', 'data' => ['keyMod' => null, 'sqlType' => 'TIMESTAMP', 'nullable' => true, 'unsigned' => false, 'defaultValue' => 'NULL', 'comment' => null]],
        ]);
        $content = $this->service->createMigration($schema)[0]['content'];
        $this->assertStringContainsString("->default('pending')", $content);
        $this->assertStringContainsString("->comment('order status')", $content);
        $this->assertStringContainsString("->default(1)", $content);
        $this->assertStringContainsString("->default(null)", $content);
    }

    public function testCreateMigrationWithForeignKeys(): void
    {
        $schema = json_encode([
            ['id' => 't1', 'type' => 'table', 'label' => 'users'],
            ['id' => 't2', 'type' => 'table', 'label' => 'posts'],
            ['id' => 'r1', 'type' => 'row', 'label' => 'id', 'parentNode' => 't1', 'data' => ['keyMod' => 'PRIMARY KEY', 'sqlType' => 'INT', 'nullable' => false, 'unsigned' => false, 'defaultValue' => null, 'comment' => null]],
            ['id' => 'r2', 'type' => 'row', 'label' => 'id', 'parentNode' => 't2', 'data' => ['keyMod' => 'PRIMARY KEY', 'sqlType' => 'INT', 'nullable' => false, 'unsigned' => false, 'defaultValue' => null, 'comment' => null]],
            ['id' => 'r3', 'type' => 'row', 'label' => 'user_id', 'parentNode' => 't2', 'data' => ['keyMod' => null, 'sqlType' => 'INT', 'nullable' => false, 'unsigned' => false, 'defaultValue' => null, 'comment' => null]],
            ['sourceNode' => ['id' => 'r1'], 'targetNode' => ['id' => 'r3']],
        ]);
        $files = $this->service->createMigration($schema);
        $this->assertCount(2, $files);
        $postsFile = collect($files)->first(fn($f) => str_contains($f['filename'], 'posts'));
        $this->assertStringContainsString("foreign('user_id')", $postsFile['content']);
        $this->assertStringContainsString("references('id')->on('users')", $postsFile['content']);
    }
}
