const express = require('express');
const axios = require('axios');
const app = express();
const dotenv = require('dotenv');
const bodyParser = require('body-parser');
const multer = require('multer');
const upload = multer();
const redis = require('redis');
const cors = require('cors');
const AuthRoute = require('./routes/AuthRoute');
app.use(cors());
app.use(upload.any());

const PORT = process.env.PORT || 4000 ;
const authMiddleware = require('./middleware/authMiddleware');
dotenv.config();
app.use(express.json());
app.use(bodyParser.json());


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

const routes = [
  {
    path: '/verifyemail',
    method: 'get',
    serviceUrl: `${process.env.AUTH_SERVICE_URL}/verifyemail`,
  },
  {
    path: '/sent-verify-link',
    method: 'post',
    serviceUrl: `${process.env.AUTH_SERVICE_URL}/sent-verify-link`,
  },
  {
    path: '/api/food/:id/upload',
    method: 'post',
    serviceUrl: `${process.env.MEAL_SERVICE_URL}/api/food/:id/upload`,
  },
  ,
  {
    path: '/api/foods/:name',
    method: 'get',
    serviceUrl: `${process.env.MEAL_SERVICE_URL}/api/foods/:name`,
    middleware: authMiddleware
  },
  {
    path: '/api/foods',
    method: 'get',
    serviceUrl: `${process.env.MEAL_SERVICE_URL}/api/foods`,
    middleware: authMiddleware
  },
  {
    path: '/api/foods',
    method: 'post',
    serviceUrl: `${process.env.MEAL_SERVICE_URL}/api/foods`,
    middleware: authMiddleware
  },
  {
    path: '/api/foods/:id',
    method: 'put',
    serviceUrl: `${process.env.MEAL_SERVICE_URL}/api/foods/:id`,
    middleware: authMiddleware
  },
  {
    path: '/api/foods/:id',
    method: 'delete',
    serviceUrl: `${process.env.MEAL_SERVICE_URL}/api/foods/:id`,
    middleware: authMiddleware
  },
  {
    path: '/api/meals',
    method: 'get',
    serviceUrl: `${process.env.MEAL_SERVICE_URL}/api/meals`,
    middleware: authMiddleware
  },
  {
    path: '/api/meals',
    method: 'post',
    serviceUrl: `${process.env.MEAL_SERVICE_URL}/api/meals`,
    middleware: authMiddleware
  },
  {
    path: '/api/meals/:id',
    method: 'delete',
    serviceUrl: `${process.env.MEAL_SERVICE_URL}/api/meals/:id`,
    middleware: authMiddleware
  },
  {
    path: '/api/getcaloroystrend',
    method: 'get',
    serviceUrl: `${process.env.MEAL_SERVICE_URL}/api/getcaloroystrend`,
    middleware: authMiddleware
  },
  {
    path: '/api/meals/:id',
    method: 'put',
    serviceUrl: `${process.env.MEAL_SERVICE_URL}/api/meals/:id`,
    middleware: authMiddleware
  },
  {
    path: '/api/weight-records',
    method: 'get',
    serviceUrl: `${process.env.NUTRITION_SERVICE_URL}/api/weight-records`,
    middleware: authMiddleware
  }
  ,
  {
    path: '/api/weight-records',
    method: 'post',
    serviceUrl: `${process.env.NUTRITION_SERVICE_URL}/api/weight-records`,
    middleware: authMiddleware
  }
  ,
  {
    path: '/api/nutritiongoeals/:id',
    method: 'delete',
    serviceUrl: `${process.env.NUTRITION_SERVICE_URL}/api/nutritiongoeals/:id`,
    middleware: authMiddleware
  }
  ,
  {
    path: '/api/nutritiongoeals',
    method: 'get',
    serviceUrl: `${process.env.NUTRITION_SERVICE_URL}/api/nutritiongoeals`,
    middleware: authMiddleware
  }
  ,
  {
    path: '/api/nutritiongoeals',
    method: 'post',
    serviceUrl: `${process.env.NUTRITION_SERVICE_URL}/api/nutritiongoeals`,
    middleware: authMiddleware
  }
  ,
  {
    path: '/api/exercises',
    method: 'get',
    serviceUrl: `${process.env.NUTRITION_SERVICE_URL}/api/exercises`,
    middleware: authMiddleware
  }
  ,
  {
    path: '/api/exercises',
    method: 'post',
    serviceUrl: `${process.env.NUTRITION_SERVICE_URL}/api/exercises`,
    middleware: authMiddleware
  }
];

const FormData = require('form-data');
const genericHandler = async (req, res, route) => {
  try {
    const { serviceUrl } = route;
    let url = serviceUrl;

    Object.keys(req.params).forEach((key) => {
      url = url.replace(`:${key}`, req.params[key]);
    });
    const queryParams = new URLSearchParams({
      ...req.query,
      ...(req.user?.id ? { userId: req.user.id } : {}) 
    }).toString();
    
    url += `?${queryParams}`;

    console.log(`Forwarding request to ${url}`);
    const isMultipart = req.headers['content-type']?.includes('multipart/form-data');

    let axiosConfig = {
      method: req.method,
      data: req.body,
      url,
      headers: {
        'Authorization': req.headers.authorization || '',
      },
    };
    if (isMultipart) {
      const form = new FormData();

      for (const [key, value] of Object.entries(req.body)) {
        form.append(key, value);
      }

      if (req.files && req.files.length > 0) {
        req.files.forEach(file => {
          form.append(file.fieldname, file.buffer, file.originalname);
        });
      }
      axiosConfig.data = form;
      axiosConfig.headers = {
        ...axiosConfig.headers,
        ...form.getHeaders(), 
      };
    } else {
      axiosConfig.data = req.body;
      axiosConfig.headers['Content-Type'] = 'application/json';
    }

    const response = await axios(axiosConfig);
    res.status(response.status).json(response.data);

  } catch (error) {
    console.error(`Error forwarding request to ${route.serviceUrl}:`, error.message);
    if (error.response) {
      res.status(error.response.status).json(error.response.data);
    } else {
      res.status(500).json({
        error: 'Internal server error',
        details: error.message,
      });
    }
  }
};


routes.forEach((route) => {
  const { path, method, middleware } = route;

  const handlers = [];
  if (middleware) {
    handlers.push(middleware); 
  }
  handlers.push((req, res) => genericHandler(req, res, route));

  app[method](path, handlers);
});

app.use('/api/auth',AuthRoute);

app.use((req, res) => {
  res.status(404).json({ error: 'Route not found' });
});

app.listen(PORT, () => {
  console.log(`API Gateway running on port ${PORT}`);
});