const express = require('express');
const cors = require('cors');
const axios = require('axios');
const app = express();

app.use(cors());
app.use(express.json());

const AUTH_SERVICE_URL = process.env.AUTH_SERVICE_URL ;


app.post('/api/auth/login', async (req, res) => {
    return res.json({ message: 'Login success' });
});

app.post('/api/auth/register', async (req, res) => {
    return res.json({ message: 'register success' });
});


const PORT = process.env.PORT || 4000;
app.listen(PORT, () => {
  console.log(`API Gateway running on port ${PORT}`);
});