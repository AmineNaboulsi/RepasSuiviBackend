const express = require("express");
const router = express.Router();
const controllertest = require("../controller/testController")
router.get("/test" ,controllertest.AfficherTest)  ;
router.get("/cava" , async (req , res)=>{
    res.json({message:"Test route"})
})  

module.exports = router;