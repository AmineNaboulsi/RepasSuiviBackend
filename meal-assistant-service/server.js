const fastify = require('fastify');
const cors = require('@fastify/cors');
const dotenv = require('dotenv');
const redis = require('redis');

dotenv.config();

const app = fastify({
  logger: true
});

const redisClient = redis.createClient({
  url: process.env.REDIS_URL || 'redis://localhost:6379'
});

redisClient.on('error', (err) => {
  app.log.error('Redis Client Error', err);
});

app.register(cors);

const start = async () => {
  try {
    await redisClient.connect();
    app.log.info('Connected to Redis');
    
    app.decorate('redis', redisClient);

    app.get('/', async (request, reply) => {
      return { message: 'Meal Assistant Service is running' };
    });
    
    await app.listen({ port: process.env.PORT || 3000, host: '0.0.0.0' });
    app.log.info(`Server running at ${app.server.address().port}`);
  } catch (err) {
    app.log.error(err);
    process.exit(1);
  }
};

start();