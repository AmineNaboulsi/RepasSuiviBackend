const axios = require('axios');

const getMealbyId = async (req, res) => {
    try {
        const mealId = req.params.id;
        const mealServiceUrl = process.env.MEAL_SERVICE_URL ;

        const headers = {};
        if(req.headers.authorization){
            headers.Authorization = req.headers.authorization;
        }

        const response = await axios.get(`${mealServiceUrl}/api/meals/${mealId}` , {headers});
        res.status(200).json(response.data);
    } catch (error) {
        console.error('Error fetching meal by ID:', error);
        res.status(error.response?.status || 500).json({
            message: error.response?.data?.message || 'Error fetching meal by ID'
        });
    }
}
const getMeals = async (req, res) => {
    try {
        const mealServiceUrl = process.env.MEAL_SERVICE_URL ;

        const headers = {};
        if(req.headers.authorization){
            headers.Authorization = req.headers.authorization;
        }
        const response = await axios.get(`${mealServiceUrl}/api/meals`,{headers});
        res.status(200).json(response.data);
    } catch (error) {
        console.error('Error fetching meals:', error);
        res.status(error.response?.status || 500).json({
            message: error.response?.data?.message || 'Error fetching meals'
        });
    }
}
const AddMeal = async (req, res) => {
    try {
        const mealData = req.body;
        console.log('✅ Received meal data:', mealData);

        const headers = {};

        if(req.headers.authorization){
            headers.Authorization = req.headers.authorization;
        }
        const mealServiceUrl = process.env.MEAL_SERVICE_URL;

        const response = await fetch(`${mealServiceUrl}/api/meals`, {
            method: 'POST',
            headers ,
            body: JSON.stringify(mealData),
        });

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        // let data;
        // const contentType = response.headers.get('content-type');
        // if (contentType && contentType.includes('application/json')) {
        //     data = await response.json();
        // } else {
        //     data = await response.text();
        // }
        console.log('✅ Meal added successfully:', data);

        // res.status(201).send(data);
        res.status(201).json(data);

    } catch (error) {
        console.error('❌ Error adding meal:', error.message || error);

        res.status(500).json({
            message: 'Error adding meal',
            error: error.message || error
        });
    }
};

const UpdatedMeal = async (req, res) => {
    
};
const DeleteMeal = async (req, res) => {
    const { id } = req.params;
    const mealServiceUrl = process.env.MEAL_SERVICE_URL;
    try {
        const response = await axios.delete(`${mealServiceUrl}/api/meals/${id}`);
        if (response.status === 200) {
            res.status(200).json({ message: 'Meal deleted successfully' });
        } else {
            res.status(response.status).json({ message: 'Failed to delete meal' });
        }
    } catch (error) {
        console.error('Error deleting meal:', error);
        res.status(error.response?.status || 500).json({
            message: error.response?.data?.message || 'Error deleting meal'
        });
    }
};

module.exports = {
    getMealbyId,
    getMeals,
    AddMeal,
    UpdatedMeal,
    DeleteMeal
}   