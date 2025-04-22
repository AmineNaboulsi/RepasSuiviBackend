const express = require('express');
const axios = require('axios');
const Consul = require('consul');
const consul = new Consul({
  host: 'service-registry',
  port: '8500'
});
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

const PORT = process.env.PORT || 80 ;
const authMiddleware = require('./middleware/authMiddleware');
dotenv.config();
app.use(express.json());
app.use(bodyParser.json());


const redisClient = redis.createClient({
  url: process.env.REDIS_URL || 'redis://redis:6379'
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
    servicename: 'auth-service',
    serviceUrl: `${process.env.AUTH_SERVICE_URL}/verifyemail`,
  },
  {
    path: '/sent-verify-link',
    method: 'post',
    servicename: 'auth-service',
    serviceUrl: `${process.env.AUTH_SERVICE_URL}/sent-verify-link`,
  },
  {
    path: '/api/food/:id/upload',
    method: 'post',
    servicename: 'meal-service',
    serviceUrl: `${process.env.MEAL_SERVICE_URL}/api/food/:id/upload`,
  },
  ,
  {
    path: '/api/foods/:name',
    method: 'get',
    servicename: 'meal-service',
    serviceUrl: `${process.env.MEAL_SERVICE_URL}/api/foods/:name`,
    middleware: authMiddleware
  },
  {
    path: '/api/foods',
    method: 'get',
    servicename: 'meal-service',
    serviceUrl: `${process.env.MEAL_SERVICE_URL}/api/foods`,
    middleware: authMiddleware
  },
  {
    path: '/api/foods',
    method: 'post',
    servicename: 'meal-service',
    serviceUrl: `${process.env.MEAL_SERVICE_URL}/api/foods`,
    middleware: authMiddleware
  },
  {
    path: '/api/foods/:id',
    method: 'put',
    servicename: 'meal-service',
    serviceUrl: `${process.env.MEAL_SERVICE_URL}/api/foods/:id`,
    middleware: authMiddleware
  },
  {
    path: '/api/foods/:id',
    method: 'delete',
    servicename: 'meal-service',
    serviceUrl: `${process.env.MEAL_SERVICE_URL}/api/foods/:id`,
    middleware: authMiddleware
  },
  {
    path: '/api/meals',
    method: 'get',
    servicename: 'meal-service',
    serviceUrl: `${process.env.MEAL_SERVICE_URL}/api/meals`,
    middleware: authMiddleware
  },
  {
    path: '/api/meals',
    method: 'post',
    servicename: 'meal-service',
    serviceUrl: `${process.env.MEAL_SERVICE_URL}/api/meals`,
    middleware: authMiddleware
  },
  {
    path: '/api/meals/:id',
    method: 'delete',
    servicename: 'meal-service',
    serviceUrl: `${process.env.MEAL_SERVICE_URL}/api/meals/:id`,
    middleware: authMiddleware
  },
  {
    path: '/api/getcaloroystrend',
    method: 'get',
    servicename: 'meal-service',
    serviceUrl: `${process.env.MEAL_SERVICE_URL}/api/getcaloroystrend`,
    middleware: authMiddleware
  },
  {
    path: '/api/meals/:id',
    method: 'put',
    servicename: 'meal-service',
    serviceUrl: `${process.env.MEAL_SERVICE_URL}/api/meals/:id`,
    middleware: authMiddleware
  },
  {
    path: '/api/weight-records',
    method: 'get',
    servicename: 'nutrition-service',
    serviceUrl: `${process.env.NUTRITION_SERVICE_URL}/api/weight-records`,
    middleware: authMiddleware
  }
  ,
  {
    path: '/api/weight-records',
    method: 'post',
    servicename: 'nutrition-service',
    serviceUrl: `${process.env.NUTRITION_SERVICE_URL}/api/weight-records`,
    middleware: authMiddleware
  }
  ,
  {
    path: '/api/nutritiongoeals/:id',
    method: 'delete',
    servicename: 'nutrition-service',
    serviceUrl: `${process.env.NUTRITION_SERVICE_URL}/api/nutritiongoeals/:id`,
    middleware: authMiddleware
  }
  ,
  {
    path: '/api/nutritiongoeals',
    method: 'get',
    servicename: 'nutrition-service',
    serviceUrl: `${process.env.NUTRITION_SERVICE_URL}/api/nutritiongoeals`,
    middleware: authMiddleware
  }
  ,
  {
    path: '/api/nutritiongoeals',
    method: 'post',
    servicename: 'nutrition-service',
    serviceUrl: `${process.env.NUTRITION_SERVICE_URL}/api/nutritiongoeals`,
    middleware: authMiddleware
  }
  ,
  {
    path: '/api/exercises',
    method: 'get',
    servicename: 'nutrition-service',
    serviceUrl: `${process.env.NUTRITION_SERVICE_URL}/api/exercises`,
    middleware: authMiddleware
  }
  ,
  {
    path: '/api/exercises',
    method: 'post',
    servicename: 'nutrition-service',
    serviceUrl: `${process.env.NUTRITION_SERVICE_URL}/api/exercises`,
    middleware: authMiddleware
  }
  ,
  {
    path: '/api/statistics',
    method: 'get',
    serviceUrl: [
      {name : "exercises-week" ,url :`${process.env.NUTRITION_SERVICE_URL}/api/exercises?f=week`} , 
      {name : "exercises" ,url :`${process.env.NUTRITION_SERVICE_URL}/api/exercises`} , 
      {name : "weight-records" ,url :`${process.env.NUTRITION_SERVICE_URL}/api/weight-records`} , 
      {name : "meals" ,url :`${process.env.MEAL_SERVICE_URL}/api/meals`} , 
      {name : "caloroystrend" ,url :`${process.env.MEAL_SERVICE_URL}/api/getcaloroystrend`} , 
      {name : "nutritiongoeals" ,url :`${process.env.NUTRITION_SERVICE_URL}/api/nutritiongoeals`} , 
    ],
    middleware: authMiddleware
  }
  
];


const getServiceUrlFromConsul = async (serviceName) => {
  return new Promise((resolve, reject) => {
    consul.agent.service.list((err, services) => {
      if (err) {
        return reject(err);
      }

      const service = Object.values(services).find(s => s.Service === serviceName);
      if (service) {
        resolve(`http://${service.Address}:${service.Port}`);
      } else {
        reject(new Error(`Service ${serviceName} not found in Consul`));
      }
    });
  });
};

const FormData = require('form-data');
const genericHandler = async (req, res, route) => {
  try {
    const { servicename } = route;
    let url = await getServiceUrlFromConsul(servicename);
    
    Object.keys(req.params).forEach((key) => {
      url = url.replace(`:${key}`, req.params[key]);
    });
    const queryParams = new URLSearchParams({
      ...req.query,
      ...(req.user?.id ? { userId: req.user.id } : {}) 
    }).toString();
    
    url += `?${queryParams}`;

    const isMultipart = req.headers['content-type']?.includes('multipart/form-data');

    let axiosConfig = {
      method: req.method,
      data: req.body,
      url,
      headers: {
        'Authorization': req.headers.authorization || '',
      },
    };

    const buildUrl = (baseUrl) => {
      const url = new URL(baseUrl);
      const queryParams = {
        ...req.query,
        ...(req.user?.id ? { userId: req.user.id } : {}) 
      };
      for (const [key, value] of Object.entries(queryParams)) {
        url.searchParams.append(key, value);
      }
      return url.toString();
    };
    
    if (Array.isArray(serviceUrl)) {
      const results = await Promise.all(
        serviceUrl.map(async (service) => {
          const name = service.name || 'unknown';
          try {
            const finalUrl = buildUrl(service.url);
            console.log(`finalUrl : ${finalUrl}`)
            const response = await axios.get(finalUrl, {
              headers: {
                'Authorization': req.headers.authorization || '',
              },
            });
            return { name, data: response.data };
          } catch (err) {
            return { name, data: null };
          }
        })
      );
      return res.status(200).json(results);
    }

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


app.get('/' , (req, res) => {
  res.status(200).json({ message: 'API getway Running' });
});

app.get('/health', (req, res) => res.send('OK'));

// app.use((req, res) => {
//   res.status(404).json({ error: 'Route not found' });
// });

app.listen(PORT, () => {
  console.log(`API Gateway running on port ${PORT}`);

  consul.agent.service.register({
    id: "api-gateway-001",
    name: "api-gateway",
    address: 'api-gateway',
    port: PORT,
    check: {
      http: `http://api-gateway/health`,
      interval: '10s'
    }
  }, err => {
    if (err) {
      console.error('❌ Failed to register service with Consul:', err);
    } else {
      console.log('✅ Service registered with Consul');
    }
  });

});