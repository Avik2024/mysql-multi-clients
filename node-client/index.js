// Node.js MySQL Client
const mysql = require("mysql2/promise");

// Read connection details from environment (good for Docker or host)
const MYSQL_HOST = process.env.MYSQL_HOST || "127.0.0.1"; // or "mysql" if Node in Docker
const MYSQL_PORT = parseInt(process.env.MYSQL_PORT || "3307"); // your mapped port
const MYSQL_USER = process.env.MYSQL_USER || "root";
const MYSQL_PASSWORD = process.env.MYSQL_PASSWORD || "rootpass123";
const MYSQL_DB = process.env.MYSQL_DB || "testdb";

async function getConnection(db = true) {
    return mysql.createConnection({
        host: MYSQL_HOST,
        port: MYSQL_PORT,
        user: MYSQL_USER,
        password: MYSQL_PASSWORD,
        database: db ? MYSQL_DB : undefined
    });
}

async function runQuery(query, values = []) {
    let conn;
    try {
        conn = await getConnection();
        const [rows] = await conn.execute(query, values);
        return rows;
    } catch (err) {
        console.error("❌ Error:", err.message);
    } finally {
        if (conn) await conn.end();
    }
}

async function initDB() {
    let conn;
    try {
        conn = await getConnection(false);
        await conn.execute(`CREATE DATABASE IF NOT EXISTS ${MYSQL_DB}`);
        console.log(`✅ Database '${MYSQL_DB}' ensured.`);
    } catch (err) {
        console.error("❌ Error creating database:", err.message);
    } finally {
        if (conn) await conn.end();
    }
}

async function main() {
    await initDB();
    console.log("✅ Connected to MySQL from Node.js");

    // 1. Create table if not exists
    await runQuery(`
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100),
            email VARCHAR(100)
        )
    `);
    console.log("📦 Table 'users' ensured.");

    // 2. Insert a record
    await runQuery(
        "INSERT INTO users (name, email) VALUES (?, ?)",
        ["Alice", "alice@example.com"]
    );
    console.log("📥 Inserted a record into 'users'.");

    // 3. Fetch records
    const rows = await runQuery("SELECT * FROM users");
    console.log("📊 Users in DB:");
    if (rows) rows.forEach(row => console.log(row));
}

main();
