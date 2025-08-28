# Python MySQL Client
import os
import mysql.connector

# Read connection details from env (good for Docker or host)
MYSQL_HOST = os.getenv("MYSQL_HOST", "127.0.0.1")  # or "mysql" if Python in Docker
MYSQL_PORT = int(os.getenv("MYSQL_PORT", 3307))    # <- IMPORTANT: use 3307 (your mapped port)
MYSQL_USER = os.getenv("MYSQL_USER", "root")
MYSQL_PASSWORD = os.getenv("MYSQL_PASSWORD", "rootpass123")
MYSQL_DB = os.getenv("MYSQL_DB", "testdb")

def get_connection(db=True):
    """Create and return a new MySQL connection."""
    return mysql.connector.connect(
        host=MYSQL_HOST,
        port=MYSQL_PORT,
        user=MYSQL_USER,
        password=MYSQL_PASSWORD,
        database=MYSQL_DB if db else None
    )

def run_query(query, values=None, fetch=False):
    """Run a SQL query with optional values and return results if needed."""
    conn = None
    try:
        conn = get_connection()
        cursor = conn.cursor()
        cursor.execute(query, values or ())

        if fetch:
            result = cursor.fetchall()
            return result

        conn.commit()
    except Exception as e:
        print("❌ Error:", e)
    finally:
        if conn:
            conn.close()

def init_db():
    """Ensure database exists before using it."""
    conn = None
    try:
        conn = get_connection(db=False)
        cursor = conn.cursor()
        cursor.execute(f"CREATE DATABASE IF NOT EXISTS {MYSQL_DB};")
        print(f"✅ Database '{MYSQL_DB}' ensured.")
    except Exception as e:
        print("❌ Error creating database:", e)
    finally:
        if conn:
            conn.close()

def main():
    init_db()
    print("✅ Connected to MySQL from Python")

    # 1. Create table if not exists
    run_query("""
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100),
            email VARCHAR(100)
        )
    """)
    print("📦 Table 'users' ensured.")

    # 2. Insert a record
    run_query(
        "INSERT INTO users (name, email) VALUES (%s, %s)",
        ("Alice", "alice@example.com")
    )
    print("📥 Inserted a record into 'users'.")

    # 3. Fetch records
    rows = run_query("SELECT * FROM users", fetch=True)
    print("📊 Users in DB:")
    if rows:
        for row in rows:
            print(row)

if __name__ == "__main__":
    main()
