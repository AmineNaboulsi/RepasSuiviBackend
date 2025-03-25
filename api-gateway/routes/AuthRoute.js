const express = require("express");
const router = express.Router();
const controllertest = require("../controller/AuthController")

router.post("/login" ,controllertest.Login)  ;
router.post("/register" , controllertest.Register)  
router.post("/verify-email" , controllertest.VerifyEmail)  

module.exports = router;