const express = require("express");
const multer = require("multer");
const router = express.Router();
const controllerFood= require("../controller/FoodController")
// const upload = multer();
const storage = multer.memoryStorage();
const upload = multer({ storage: storage });

router.get("/" ,controllerFood.getFoods)  ;
router.get("/:id" ,controllerFood.getFoodbyId)  ;
router.post("/addfood", upload.single("image"), controllerFood.AddFood);
router.put("/updatefood/:id" , controllerFood.UpdatedFood);
router.post("/:id/upload", upload.single("image"), controllerFood.UploadFoodImage); 
router.delete("/delfood/:id" , controllerFood.DeleteFood);

module.exports = router;