const express = require('express');
const axios = require('axios');
const app = express();
const dotenv = require('dotenv');
const bodyParser = require('body-parser');

const PORT = process.env.PORT || 3000;
const authMiddleware = require('./middleware/authMiddleware');
app.use(express.json());
dotenv.config();
app.use(bodyParser.json());

const routes = [
  {
    path: '/api/auth/login',
    method: 'post',
    serviceUrl: `${process.env.AUTH_SERVICE_URL}/api/auth/login`,
  },
  {
    path: '/api/auth/register',
    method: 'post',
    serviceUrl: `${process.env.AUTH_SERVICE_URL}/api/auth/register`,
  },
  {
    path: '/api/auth/verify-email',
    method: 'post',
    serviceUrl: `${process.env.AUTH_SERVICE_URL}/verify-email`,
  },
  {
    path: '/api/food/:id/upload',
    method: 'post',
    serviceUrl: `${process.env.MEAL_SERVICE_URL}/api/food/:id/upload`,
  },
  {
    path: '/api/foods',
    method: 'get',
    serviceUrl: `${process.env.MEAL_SERVICE_URL}/api/foods`,
  },
  {
    path: '/api/foods/:id',
    method: 'put',
    serviceUrl: `${process.env.MEAL_SERVICE_URL}/api/foods/:id`,
  },
  {
    path: '/api/foods/:id',
    method: 'delete',
    serviceUrl: `${process.env.MEAL_SERVICE_URL}/api/foods/:id`,
  },
  {
    path: 'api/meals',
    method: 'get',
    serviceUrl: `${process.env.MEAL_SERVICE_URL}/api/meals`,
  },
  {
    path: 'api/meals',
    method: 'post',
    serviceUrl: `${process.env.MEAL_SERVICE_URL}/api/meals`,
  },
  {
    path: 'api/meals/:id',
    method: 'put',
    serviceUrl: `${process.env.MEAL_SERVICE_URL}/api/meals/:id`,
  },
  {
    path: 'api/meals/:id',
    method: 'delete',
    serviceUrl: `${process.env.MEAL_SERVICE_URL}/api/meals/:id`,
  },
  {
    path: '/meals/:id/custom',
    method: 'get',
    serviceUrl: 'http://meal-service:3001/meals/:id',
    middleware: ()=>{
      next()
    },
    customHandler: (req, res, next) => {
      next();
    },
  },
];

const genericHandler = async (req, res, route) => {
  try {
    const { serviceUrl } = route;
    let url = serviceUrl;
    
    Object.keys(req.params).forEach((key) => {
      url = url.replace(`:${key}`, req.params[key]);
    });
 
    const response = await axios({
      method: req.method,
      url,
      data: req.body,
      headers: {
        ...req.headers,
        host: undefined,
      },
    });

    res.status(response.status).json(response.data);
  } catch (error) {
    console.error(`Error forwarding request to ${route.serviceUrl}:`, error.message);
    if (error.response) {
      res.status(error.response.status).json(error.response.data);
    } else {
      res.status(500).json({
        error: 'Internal server error',
        details: error.message,
      });}
  }
};

routes.forEach((route) => {
  const { path, method, customHandler } = route;

  const handlers = [];
  if (customHandler) {
    handlers.push(customHandler); 
  }
  handlers.push((req, res) => genericHandler(req, res, route));

  app[method](path, handlers);
});

app.get('/', (req, res) => {
  res.status(200).json({ status: 'API Gateway is running' });
});

app.use((req, res) => {
  res.status(404).json({ error: 'Route not found' });
});

app.listen(PORT, () => {
  console.log(`API Gateway running on port ${PORT}`);
});