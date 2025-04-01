const axios = require('axios');
const FormData = require("form-data");

const getFoods = async (req, res) => {
    try {
        const mealServiceUrl = process.env.MEAL_SERVICE_URL;
      
        const headers = {};
        if (req.headers.authorization) {
            headers.Authorization = req.headers.authorization;
        }
        
        const response = await axios.get(`${mealServiceUrl}/api/foods`, { headers });
        res.status(200).json(response.data);
    } catch (error) {
        console.error('Error fetching meals:', error);
        res.status(error.response?.status || 500).json({
            message: error.response?.data?.message || 'Error fetching food'
        });
    }
};

const getFoodbyId = async (req, res) => {
    try {
            const mealId = req.params.id;
            const mealServiceUrl = process.env.MEAL_SERVICE_URL ;

              
            const headers = {};
            if (req.headers.authorization) {
                headers.Authorization = req.headers.authorization;
            }
            
            const response = await axios.get(`${mealServiceUrl}/api/foods/${mealId}` , {headers});
            res.status(200).json(response.data);
        } catch (error) {
            console.error('Error fetching meal by ID:', error);
            res.status(error.response?.status || 500).json({
                message: error.response?.data?.message || 'Error fetching food by ID'
            });
    }
};


const AddFood = async (req, res) => {
    try {
        const formData = new FormData();

        for (const key in req.body) {
            formData.append(key, req.body[key]);
        }

        if (req.file) {
            formData.append("image", req.file.buffer, {
                filename: req.file.originalname,
                contentType: req.file.mimetype
            });
        }

        const mealServiceUrl = process.env.MEAL_SERVICE_URL;
        
        const response = await axios.post(`${mealServiceUrl}/api/foods`, formData, {
            headers: {
                ...formData.getHeaders()
            }
        });

        res.status(200).json(response.data);
    } catch (error) {
        console.error("Error adding food:", error.response?.data || error);
        res.status(error.response?.status || 500).json({
            message: error.response?.data?.message || "Error adding food"
        });
    }
};

const UploadFoodImage = async (req, res) => {
    try {
        const foodId = req.params.id;
        const mealServiceUrl = process.env.MEAL_SERVICE_URL;

        if (!req.file) {
            return res.status(400).json({ message: "No image provided" });
        }
             
        const headers = {
            ...formData.getHeaders()
        };
        if (req.headers.authorization) {
            headers.Authorization = req.headers.authorization;
        }
        


        const formData = new FormData();
        formData.append("image", req.file.buffer, req.file.originalname);

        const response = await axios.post(
            `${mealServiceUrl}/api/food/${foodId}/upload`, 
            formData, 
            {
                headers
            }
        );

        res.status(200).json(response.data);
    } catch (error) {
        console.error("Error uploading image:", error);
        res.status(error.response?.status || 500).json({
            message: error.response?.data?.message || "Error uploading image",
        });
    }
};


const UpdatedFood = async (req, res) => {
    try {
        const foodId = req.params.id;
        const food = req.body;

        const mealServiceUrl = process.env.MEAL_SERVICE_URL;
             
        const headers = {};
        if (req.headers.authorization) {
            headers.Authorization = req.headers.authorization;
        }
        
        const response = await axios.put(`${mealServiceUrl}/api/foods/${foodId}`, food , {headers});
        res.status(200).json(response.data);
    } catch (error) {
        console.error('Error updating food:', error);
        res.status(error.response?.status || 500).json({
            message: error.response?.data?.message || 'Error updating food'
        });
    }
};

const DeleteFood = async (req, res) => {
    try{
        const foodId = req.params.id;
        const mealServiceUrl = process.env.MEAL_SERVICE_URL;
        const response = await axios.delete(`${mealServiceUrl}/api/foods/${foodId}`);
        res.status(200).json(response.data);
    }catch(error){
        console.error('Error deleting food:', error);
        res.status(error.response?.status || 500).json({
            message: error.response?.data?.message || 'Error deleting food'
        });
    }
};

module.exports = {
    getFoods,
    getFoodbyId,
    UploadFoodImage,
    AddFood,
    UpdatedFood,
    DeleteFood
}   