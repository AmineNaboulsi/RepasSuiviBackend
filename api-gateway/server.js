const express = require('express');
const cors = require('cors');
const axios = require('axios');
const dotenv = require('dotenv');
const path = require('path');
const multer = require('multer');
const RouterAuth = require('./routes/AuthRoute');
dotenv.config();

const app = express();

app.use(cors());
app.use(express.json());
app.use(express.raw({ type: 'application/octet-stream' }));
app.use(express.urlencoded({ extended: true }));

app.get('/', async (req, res) => {
  return res.json({ message: 'Auth Service 2' });
});

app.use('/api/auth',RouterAuth);

const PORT = process.env.PORT;
app.listen(PORT, () => {
  console.log(`API Gateway running on port ${PORT}`);
});