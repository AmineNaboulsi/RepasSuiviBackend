const express = require("express");
const multer = require('multer');

const router = express.Router();
const controllerMeal = require("../controller/MealController")
const upload = multer();

router.get("/" ,controllerMeal.getMeals)  ;
router.get("/:id" ,controllerMeal.getMealbyId)  ;
router.post("/addmeal" ,upload.none(),controllerMeal.AddMeal)  ;
router.put("/updatemeal/:id" , controllerMeal.UpdatedMeal)  
router.delete("/delmeal/:id" , controllerMeal.DeleteMeal)  

module.exports = router;