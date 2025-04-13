const redis = require('redis');
const client = redis.createClient({
  url: process.env.REDIS_URL || 'redis://localhost:6379',
});

client.connect().catch((err) => console.error('Redis connection error:', err));

const authMiddleware = async (req, res, next) => {
  try {
    // Extract token from Authorization header (e.g., "Bearer <token>")
    const token = req.headers.authorization?.split(' ')[1];
    if (!token) {
      return res.status(401).json({ error: 'No token provided' });
    }

    // Check token in Redis
    const userData = await client.get(`auth:${token}`);
    if (!userData) {
      return res.status(401).json({ error: 'Invalid or expired token' });
    }

    // Parse user data and attach to request
    req.user = JSON.parse(userData);

    // Optionally, add user info to headers for downstream services
    req.headers['x-user-info'] = JSON.stringify(req.user);

    next();
  } catch (error) {
    console.error('Auth middleware error:', error.message);
    res.status(500).json({ error: 'Authentication failed' });
  }
};

module.exports =  authMiddleware;