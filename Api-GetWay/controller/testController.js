const AfficherTest = async (req , res)=>{
    const {email , password} =  req.body;
    if(!email || !password){
        return res.status(400).json({message:"Email and password are required"});
    }
    mysql.
    res.json({message:"Test route"})
}
module.exports = {
    AfficherTest
}