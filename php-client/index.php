<?php
// php-client/index.php

// Load environment variables (if using Docker or .env)
$MYSQL_HOST = getenv('MYSQL_HOST') ?: '127.0.0.1';
$MYSQL_PORT = getenv('MYSQL_PORT') ?: '3307';
$MYSQL_USER = getenv('MYSQL_USER') ?: 'root';
$MYSQL_PASSWORD = getenv('MYSQL_PASSWORD') ?: 'rootpass123';
$MYSQL_DB = getenv('MYSQL_DB') ?: 'testdb';

/**
 * Get a MySQL connection
 * @param bool $db Whether to connect to a specific database
 * @return mysqli
 */
function get_connection($db = true) {
    global $MYSQL_HOST, $MYSQL_PORT, $MYSQL_USER, $MYSQL_PASSWORD, $MYSQL_DB;
    $database = $db ? $MYSQL_DB : null;
    $conn = new mysqli($MYSQL_HOST, $MYSQL_USER, $MYSQL_PASSWORD, $database, $MYSQL_PORT);

    if ($conn->connect_error) {
        die("❌ Connection failed: " . $conn->connect_error . PHP_EOL);
    }

    return $conn;
}

/**
 * Run a query with optional parameters
 */
function run_query($query, $types = null, $params = [], $fetch = false) {
    $conn = get_connection();
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        echo "❌ Prepare failed: " . $conn->error . PHP_EOL;
        $conn->close();
        return;
    }

    if ($types && $params) {
        $stmt->bind_param($types, ...$params);
    }

    if (!$stmt->execute()) {
        echo "❌ Execution failed: " . $stmt->error . PHP_EOL;
        $stmt->close();
        $conn->close();
        return;
    }

    $result = null;
    if ($fetch) {
        $res = $stmt->get_result();
        $result = $res->fetch_all(MYSQLI_ASSOC);
    }

    $stmt->close();
    $conn->close();
    return $result;
}

/**
 * Ensure database exists
 */
function init_db() {
    global $MYSQL_DB;
    $conn = get_connection(false);
    if ($conn->query("CREATE DATABASE IF NOT EXISTS $MYSQL_DB") === TRUE) {
        echo "✅ Database '$MYSQL_DB' ensured." . PHP_EOL;
    } else {
        echo "❌ Error creating database: " . $conn->error . PHP_EOL;
    }
    $conn->close();
}

// Main
init_db();
echo "✅ Connected to MySQL from PHP" . PHP_EOL;

// 1. Create table
run_query("
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100),
        email VARCHAR(100)
    )
");
echo "📦 Table 'users' ensured." . PHP_EOL;

// 2. Insert record
run_query(
    "INSERT INTO users (name, email) VALUES (?, ?)",
    "ss",
    ["Alice", "alice@example.com"]
);
echo "📥 Inserted a record into 'users'." . PHP_EOL;

// 3. Fetch records
$rows = run_query("SELECT * FROM users", null, [], true);
echo "📊 Users in DB:" . PHP_EOL;
foreach ($rows as $row) {
    echo "(" . $row['id'] . ", '" . $row['name'] . "', '" . $row['email'] . "')" . PHP_EOL;
}

