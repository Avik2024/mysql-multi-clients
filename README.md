# Multi-language MySQL Clients

This repo shows how to connect to a MySQL database using Python, Go, Node.js, and PHP.

## Setup
1. Start MySQL with Docker:
   ```
   docker-compose up -d
   ```

2. Run each client:
   - **Python**: `pip install -r python-client/requirements.txt && python python-client/app.py`
   - **Go**: `cd go-client && go run main.go`
   - **Node.js**: `cd node-client && npm install && node index.js`
   - **PHP**: `php php-client/index.php`

Default MySQL credentials:
- User: `root`
- Password: `password`
- Database: `testdb`
