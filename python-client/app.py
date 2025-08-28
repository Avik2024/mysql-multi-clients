# Python MySQL Client
import mysql.connector

def connect():
    try:
        conn = mysql.connector.connect(
            host="localhost",
            user="root",
            password="password",
            database="testdb"
        )
        print("Connected to MySQL from Python")
        conn.close()
    except Exception as e:
        print("Error:", e)

if __name__ == "__main__":
    connect()
