const mysql = require('mysql2');

const connection = mysql.createConnection({
  host: 'localhost',
  user: 'root',
  password: 'password',
  database: 'testdb'
});

connection.connect(err => {
  if (err) throw err;
  console.log("Connected to MySQL from Node.js");
  connection.end();
});
