const express = require('express');
const mysql = require('mysql');
const cors = require('cors');
const bodyParser = require('body-parser');

const app = express();
const PORT = 3004;

// Middleware
app.use(bodyParser.json());
app.use(cors({
  origin: '*',
  methods: ['GET', 'POST', 'PUT', 'DELETE'],
  allowedHeaders: ['Content-Type', 'Authorization']
}));

// MySQL connection
const connection = mysql.createConnection({
  host: 'localhost',   // Replace with your host (e.g., db host for cloud)
  user: 'root',        // Your MySQL username
  password: 'yourpassword',  // Your MySQL password
  database: 'qr_attendance' // Your database name
});

// Connect to database
connection.connect((err) => {
  if (err) {
    console.error('Error connecting to the database:', err);
    return;
  }
  console.log('Connected to the MySQL database');
});

// Endpoint for fetching student accounts
app.get('/studentaccount', (req, res) => {
  const sql = 'SELECT id, username, password FROM studentaccount';
  connection.query(sql, (err, results) => {
    if (err) {
      console.error('Error fetching student accounts:', err);
      res.status(500).json({ error: 'Internal server error' });
      return;
    }
    res.json(results);
  });
});

// Endpoint for adding new student account
app.post('/studentaccount', (req, res) => {
  const { username, password } = req.body;
  const sql = 'INSERT INTO studentaccount (username, password) VALUES (?, ?)';
  connection.query(sql, [username, password], (err, result) => {
    if (err) {
      console.error('Error adding student account:', err);
      res.status(500).json({ error: 'Error adding student account' });
      return;
    }
    res.json({ id: result.insertId, username, password });
  });
});

// Endpoint for generating QR Code session
app.post('/generateQR', (req, res) => {
  const qrCodeSession = 'qr_session_' + Math.floor(Math.random() * 1000000); // Example session code
  const qrCodeFilePath = `qrcodes/${qrCodeSession}.png`;
  
  // Here, you can generate the QR code and store the file path as necessary
  
  res.json({ message: 'QR Code Generated', qrCodeFilePath });
});

// Server listening
app.listen(PORT, () => {
  console.log(`Server is running on port ${PORT}`);
});
