const redis = require('redis');
const client = redis.createClient({
  url: process.env.REDIS_URL || 'redis://localhost:6379',
});

client.connect().catch((err) => console.error('Redis connection error:', err));

const authMiddleware = async (req, res, next) => {
  try {
    const token = req.headers.authorization?.split(' ')[1];
    if (!token) {
      return res.status(401).json({ error: 'No token provided' });
    }

    const userData = await client.get(`auth:${token}`);
    if (!userData) {
      return res.status(401).json({ error: 'Invalid or expired token' });
    }

    req.user = JSON.parse(userData);

    req.query = {
      ...req.query,
      userId: req.user.id
    };
    next();
  } catch (error) {
    console.error('Auth middleware error:', error.message);
    res.status(500).json({ error: 'Authentication failed', details: error.message });
  }
};

module.exports =  authMiddleware;