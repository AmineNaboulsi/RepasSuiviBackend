const express = require('express');
const axios = require('axios');
const Consul = require('consul');
const consul = new Consul({
  host: 'service-registry',
  port: '8500',
  promisify: true,
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

let PORT = process.env.PORT || 80 ;
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
      {
        name : "exercises-week" ,
        servicename: 'nutrition-service',
        url :`${process.env.NUTRITION_SERVICE_URL}/api/exercises?f=week`} , 
      {
        name : "exercises" ,
        servicename: 'nutrition-service',
        url :`${process.env.NUTRITION_SERVICE_URL}/api/exercises`} , 
      {
        name : "weight-records" ,
        servicename: 'nutrition-service',
        url :`${process.env.NUTRITION_SERVICE_URL}/api/weight-records`
      } , 
      {
        name : "meals" ,
        servicename: 'meal-service',
        url :`${process.env.MEAL_SERVICE_URL}/api/meals`
      } , 
      {
        name : "caloroystrend" ,
        servicename: 'meal-service',
        url :`${process.env.MEAL_SERVICE_URL}/api/getcaloroystrend`
      } , 
      {
        name : "nutritiongoeals" ,
        servicename: 'nutrition-service',
        url :`${process.env.NUTRITION_SERVICE_URL}/api/nutritiongoeals`
      } , 
    ],
    middleware: authMiddleware
  }
  
];


const getServiceUrlFromConsul = async (serviceName) => {
  try {
    const serviceInstances = await consul.health.service({
      service: serviceName,
      passing: true,
    });

    if (serviceInstances.length === 0) {
      return `Service ${serviceName} not found or no healthy instances in Consul` ;
    }

    const service = serviceInstances[0].Service;
    return`http://${service.Address}:${service.Port}`;

  } catch (err) {
    console.error(`Error fetching service ${serviceName} from Consul:`, err);
    res.status(500).json({ error: 'Internal server error' });
  }
};


const FormData = require('form-data');
const genericHandler = async (req, res, route) => {
  const { servicename, serviceUrl } = route;

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

  try {
    let url = serviceUrl;

    Object.keys(req.params).forEach((key) => {
      url = url.replace(`:${key}`, req.params[key]);
    });

    if (Array.isArray(serviceUrl)) {
      const results = await Promise.all(
        serviceUrl.map(async (service) => {
          const name = service.name || 'unknown';
          try {
            const finalUrl = buildUrl(service.url);
            const response = await axios.get(finalUrl, {
              headers: {
                'Authorization': req.headers.authorization || '',
              },
            });
            return { name, data: response.data };
          } catch (err) {
            return { name, data: null, error: err.message };
          }
        })
      );
      return res.status(200).json(results);
    }

    const isMultipart = req.headers['content-type']?.includes('multipart/form-data');

    const buildAxiosConfig = (url) => {
      const headers = {
        'Authorization': req.headers.authorization || '',
      };

      if (isMultipart) {
        const form = new FormData();

        Object.entries(req.body).forEach(([key, value]) => {
          form.append(key, value);
        });

        if (req.files?.length > 0) {
          req.files.forEach(file => {
            form.append(file.fieldname, file.buffer, file.originalname);
          });
        }

        return {
          method: req.method,
          url,
          data: form,
          headers: {
            ...headers,
            ...form.getHeaders(),
          },
        };
      } else {
        return {
          method: req.method,
          url,
          data: req.body,
          headers: {
            ...headers,
            'Content-Type': 'application/json',
          },
        };
      }
    };
    let finalUrl;

    try {
      finalUrl = buildUrl(url);
      console.log(`Attempting request to ${finalUrl} (expecting failure)`);
      const response = await axios(buildAxiosConfig(finalUrl));
      return res.status(response.status).json(response.data);
    } catch (err) {
      console.log(`Expected failure for ${servicename}: ${err.message}`);
    }

    try {
      console.log(`Fetching URL for ${servicename} from Consul`);
      const baseUrl = await getServiceUrlFromConsul(servicename);
      console.log(`Consul returned: ${baseUrl}`);

      const originalUrl = new URL(url);
      const servicePath = originalUrl.pathname + originalUrl.search;
      finalUrl = buildUrl(`${baseUrl}${servicePath}`);
      console.log(`Retrying with Consul URL: ${finalUrl}`);

      const response = await axios(buildAxiosConfig(finalUrl));
      return res.status(response.status).json(response.data);
    } catch (err) {
      console.error(`Consul fetch failed for ${servicename}: ${err.message}`);
      return res.status(502).json({
        error: 'Internal server error',
        details: err.message,
      });
    }

  } catch (error) {
    console.error(`Error forwarding request to ${route.serviceUrl}:`, error.message);
    return res.status(500).json({
      error: 'Internal server error',
      details: error.message,
    });
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

app.use((req, res) => {
  res.status(404).json({ error: 'Route not found' });
});

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

// app.get('/api/health', async (req, res) => {
//   try {
//     const services = await consul.catalog.services();
//     console.log('Consul services:', services);
//     const healthyServices = [];
    
//     const requestedService = req.query.service;
    
//     for (const serviceName of Object.keys(services)) {
//       if (requestedService && serviceName.toLowerCase() !== requestedService.toLowerCase()) {
//         continue;
//       }
      
//       const serviceInstances = await consul.health.service({
//         service: serviceName,
//         passing: true,
//       });
      
//       if (serviceInstances.length > 0) {
//         healthyServices.push({
//           service: serviceName,
//           instances: serviceInstances.map((instance) => ({
//             id: instance.Service.ID,
//             address: instance.Service.Address,
//             port: instance.Service.Port,
//             status: instance.Checks[0].Status,
//           })),
//         });
//       }
//     }
    
//     if (healthyServices.length === 0) {
//       return res.status(503).json({ 
//         error: requestedService 
//           ? `Service '${requestedService}' not found or not healthy` 
//           : 'No healthy services found' 
//       });
//     }

//     // Return healthy services
//     res.status(200).json({
//       message: requestedService 
//         ? `Service '${requestedService}' is healthy` 
//         : 'API Gateway is healthy',
//       services: healthyServices,
//     });
//   } catch (err) {
//     console.error('Error fetching services from Consul:', err);
//     res.status(500).json({ error: 'Internal server error' });
//   }
// });

// Specific endpoint to find nutrition service