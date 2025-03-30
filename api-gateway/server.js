const express = require('express');
const cors = require('cors');
const dotenv = require('dotenv');

const RouterAuth = require('./routes/AuthRoute');
const RouterMeal = require('./routes/MealRoute');
const RouterFood = require('./routes/FoodRoute');

dotenv.config();

const app = express();

app.use(cors());
app.use(express.json());
app.use(express.raw({ type : 'application/octet-stream' }));
app.use(express.urlencoded({ extended: true }));

//
app.use('/api/auth',RouterAuth);

//
app.use('/api/meals',RouterMeal);

//
app.use('/api/foods',RouterFood);

app.get("/", (req, res) => {
  res.send("API Gateway is running");
});

const PORT = process.env.PORT;
app.listen(PORT, () => {
  console.log(`API Gateway running on port ${PORT}`);
});