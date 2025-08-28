package main

import (
	"database/sql"
	"fmt"
	"log"
	"os"

	_ "github.com/go-sql-driver/mysql"
)

// Read MySQL connection details from environment variables
var (
	MYSQL_HOST     = getEnv("MYSQL_HOST", "127.0.0.1") // use "mysql" if Go is inside Docker
	MYSQL_PORT     = getEnv("MYSQL_PORT", "3307")      // mapped port
	MYSQL_USER     = getEnv("MYSQL_USER", "root")
	MYSQL_PASSWORD = getEnv("MYSQL_PASSWORD", "rootpass123")
	MYSQL_DB       = getEnv("MYSQL_DB", "testdb")
)

func getEnv(key, fallback string) string {
	if value := os.Getenv(key); value != "" {
		return value
	}
	return fallback
}

func getDSN(dbName string) string {
	// Data Source Name format: user:password@tcp(host:port)/dbname
	return fmt.Sprintf("%s:%s@tcp(%s:%s)/%s",
		MYSQL_USER, MYSQL_PASSWORD, MYSQL_HOST, MYSQL_PORT, dbName)
}

func runQuery(db *sql.DB, query string) {
	_, err := db.Exec(query)
	if err != nil {
		log.Println("❌ Error:", err)
	}
}

func main() {
	// 1. Connect to MySQL (without specifying DB for creating it if needed)
	dbRoot, err := sql.Open("mysql", getDSN(""))
	if err != nil {
		log.Fatal("❌ Connection error:", err)
	}
	defer dbRoot.Close()

	_, err = dbRoot.Exec(fmt.Sprintf("CREATE DATABASE IF NOT EXISTS %s;", MYSQL_DB))
	if err != nil {
		log.Println("❌ Error creating database:", err)
	} else {
		fmt.Printf("✅ Database '%s' ensured.\n", MYSQL_DB)
	}

	// 2. Connect to MySQL with the database
	db, err := sql.Open("mysql", getDSN(MYSQL_DB))
	if err != nil {
		log.Fatal("❌ Connection error:", err)
	}
	defer db.Close()
	fmt.Println("✅ Connected to MySQL from Go")

	// 3. Create table
	runQuery(db, `
		CREATE TABLE IF NOT EXISTS users (
			id INT AUTO_INCREMENT PRIMARY KEY,
			name VARCHAR(100),
			email VARCHAR(100)
		);
	`)
	fmt.Println("📦 Table 'users' ensured.")

	// 4. Insert record
	runQuery(db, `INSERT INTO users (name, email) VALUES ('Alice', 'alice@example.com');`)
	fmt.Println("📥 Inserted a record into 'users'.")

	// 5. Fetch records
	rows, err := db.Query("SELECT id, name, email FROM users;")
	if err != nil {
		log.Fatal("❌ Error fetching users:", err)
	}
	defer rows.Close()

	fmt.Println("📊 Users in DB:")
	for rows.Next() {
		var id int
		var name, email string
		err := rows.Scan(&id, &name, &email)
		if err != nil {
			log.Println("❌ Error scanning row:", err)
			continue
		}
		fmt.Printf("(%d, '%s', '%s')\n", id, name, email)
	}
}
