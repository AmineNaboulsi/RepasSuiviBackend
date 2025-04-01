const axios = require('axios');
const redis = require('redis');

const redisClient = redis.createClient({
  url: process.env.REDIS_URL || 'redis://localhost:6379'
});

redisClient.on('error', (err) => {
  console.log('Redis Client Error', err);
});

(async () => {
  await redisClient.connect();
  console.log('Connected to Redis');
})();

const getUserFromRedis = async (token) => {
  try {
    console.log(`Searching for token: auth:${token}`);
    const exists = await redisClient.exists(`auth:${token}`);
    console.log(`Token exists in Redis: ${exists}`);
    
    const user = await redisClient.get(`auth:${token}`);
    console.log(`Retrieved data: ${user}`);
    
    if (user) {
      return JSON.parse(user);
    } else {
      throw new Error('Token not found or expired');
    }
  } catch (error) {
    console.error('Error retrieving from Redis:', error.message);
    throw error;
  }
};
const getUser = async (req, res) => {
  const token = req.headers['authorization']?.split(' ')[1]; 

  if (!token) {
    return res.status(400).json({ message: 'Token is required' });
  }

  try {
    const user = await getUserFromRedis(token);

    return res.status(200).json({
      message: 'User data retrieved successfully',
      user: user
    });
  } catch (error) {
    return res.status(404).json({ message: error.message }); 
  }
};

const Login = async (req, res) => {
  try {
    const bodyData = req.body;
    const url = process.env.AUTH_SERVICE_URL;
    const response = await fetch(`${url}/login`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(bodyData)
    });
    const data = await response.json();

    if (response.ok && data.token) {
      console.log(data);
        // await redisClient.set(`auth:${data.token}`, JSON.stringify(data.user), { EX: 3600 })
      await redisClient.set(`auth:${data.token}`, JSON.stringify(data.user));
      
      const verifyStorage = await redisClient.get(`auth:${data.token}`);
      console.log('Token stored successfully:', !!verifyStorage);

    }
    await redisClient.set('testKey', 'Hello from Node.js');
    const value = await redisClient.get('testKey');
    console.log(value)
    return res.status(response.status).json(data);
  } catch (error) {
    console.error('Auth service error:', error.message);
    
    if (error.response) {
      return res.status(error.response.status).json(error.response.data);
    }
    
    return res.status(500).json({
      message: 'Could not connect to authentication service: ' + error.message
    });
  }
};

const Register = async (req, res) => {
  try {
    console.log('Request Content-Type:', req.headers['content-type']);
    const bodyData = req.body;
    console.log('Sending data:', bodyData);
    const url = process.env.AUTH_SERVICE_URL;
    const response = await fetch(`${url}/register`, {
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
      message: 'Could not connect to authentication service: ' + error.message
    });
  }
};

const VerifyEmail = async (req, res) => {
  try {
    const { email } = req.query;
    
    if (!email) {
      return res.status(400).json({ message: 'Email parameter is required' });
    }
    const url = process.env.AUTH_SERVICE_URL;
    
    const response = await fetch(`${url}/sent-verify-link?email=${email}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      }
    });
    const data = await response.json();
    return res.status(response.status).json(data);
  } catch (error) {
    console.error('Verify email error:', error.message);
    
    if (error.response) {
      return res.status(error.response.status).json(error.response.data);
    }
    
    return res.status(500).json({
      message: 'Error connecting to authentication service: ' + error.message
    });
  }
};

const Logout = async (req, res) => {
  const token = req.headers['authorization']?.split(' ')[1];
  
  if (!token) {
    return res.status(400).json({ message: 'Token is required' });
  }
  
  try {
    await redisClient.del(`auth:${token}`);
    return res.status(200).json({ message: 'Logged out successfully' });
  } catch (error) {
    console.error('Logout error:', error.message);
    return res.status(500).json({ message: 'Error during logout: ' + error.message });
  }
};

const getAllTokens = async (req, res) => {
  try {
    const keys = await redisClient.keys('auth:*');
    const tokens = {};
    
    for (const key of keys) {
      const userData = await redisClient.get(key);
      tokens[key] = JSON.parse(userData);
    }
    
    return res.status(200).json({ tokens });
  } catch (error) {
    console.error('Get all tokens error:', error.message);
    return res.status(500).json({ message: 'Error retrieving tokens: ' + error.message });
  }
};

module.exports = {
  Login,
  Register,
  VerifyEmail,
  getUser,
  getUserFromRedis,
  Logout,
  getAllTokens
};