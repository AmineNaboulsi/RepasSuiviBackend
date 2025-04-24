const express = require("express");
const router = express.Router();
const controllerAuth = require("../controller/AuthController")

router.post("/login" ,controllerAuth.Login)  ;
router.post("/register" , controllerAuth.Register)  
router.post("/verify-email" , controllerAuth.VerifyEmail)  
router.get("/user" , controllerAuth.getUser)  
router.post("/verify" , controllerAuth.Verify)  

module.exports = router;