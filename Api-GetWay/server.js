const express = require('express');
const cors = require('cors');
const axios = require('axios');
const dotenv = require('dotenv');
const path = require('path');
const multer = require('multer');

dotenv.config();

const app = express();

app.use(cors());
app.use(express.json());
app.use(express.raw({ type: 'application/octet-stream' }));
app.use(express.urlencoded({ extended: true }));

const AUTH_SERVICE_URL = process.env.AUTH_SERVICE_URL;

app.post('/api/auth/login', async (req, res) => {
  return res.json({ message: 'login success' });
});

// Add multer for handling form data
app.post('/api/auth/register', async (req, res) => {
  try {
      console.log('Request Content-Type:', req.headers['content-type']);
      const bodyData = req.body;
      console.log('Sending data:', bodyData);
      const response = await fetch(`${AUTH_SERVICE_URL}/register`,{
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(bodyData)
      });
      const data = await response.json();

      return res.status(response.status).json(data);
  } catch (error) {
    console.error('Auth service error:', error.message);
    
    if (error.response) {
      return res.status(error.response.status).json(error.response.data);
    }
    
    return res.status(500).json({
      message: 'Could not connect to authentication service' + error
    });
  }
});

app.post('/api/auth/verify-email', async (req, res) => {
  try {
    const { email } = req.query;
    
    if (!email) {
      return res.status(400).json({ message: 'Email parameter is required' });
    }
    
    const response = await axios.post(`${AUTH_SERVICE_URL}/sent-verify-link?email=${email}`);
    return res.status(response.status).json(response.data);
  } catch (error) {
    console.error('Verify email error:', error.message);
    
    if (error.response) {
      return res.status(error.response.status).json(error.response.data);
    }
    
    return res.status(500).json({
      message: 'Error connecting to authentication service'
    });
  }
});

const PORT = process.env.PORT;
app.listen(PORT, () => {
  console.log(`API Gateway running on port ${PORT}`);
});