const Redis = require('ioredis');

const redis = new Redis({ host: 'redis', port: 6379 });


const VerifyToken = async (req, res, next) => {
  const token = req.headers.authorization?.split(" ")[1];

  if (!token) return res.status(401).json({ error: "Unauthorized" });

  const cachedUser = await redis.get(`auth:${token}`);

  if (cachedUser) {
    req.user = JSON.parse(cachedUser); 
    return next(); 
  }

  return res.status(401).json({ error: "Session expired or invalid token" });
};

module.exports = VerifyToken;
