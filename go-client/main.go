package main

import (
    "database/sql"
    "fmt"
    _ "github.com/go-sql-driver/mysql"
)

func main() {
    dsn := "root:password@tcp(127.0.0.1:3306)/testdb"
    db, err := sql.Open("mysql", dsn)
    if err != nil {
        panic(err)
    }
    defer db.Close()

    if err = db.Ping(); err != nil {
        panic(err)
    }
    fmt.Println("Connected to MySQL from Go")
}
