const Login = async (req, res) => {
    return res.json({ message: 'login success' });
};

const Register = async (req, res) => {
    try {
        console.log('Request Content-Type:', req.headers['content-type']);
        const bodyData = req.body;
        console.log('Sending data:', bodyData);
        const url = process.env.AUTH_SERVICE_URL
        const response = await fetch(`${url}/register`,{
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(bodyData)
        });
        const data = await response.json();
  
        return res.status(response.status).json(data);
    } catch (error) {
      console.error('Auth service error:', error.message);
      
      if (error.response) {
        return res.status(error.response.status).json(error.response.data);
      }
      
      return res.status(500).json({
        message: 'Could not connect to authentication service' + error
      });
    }
}

const VerifyEmail = async (req, res) => {
    try {
      const { email } = req.query;
      
      if (!email) {
        return res.status(400).json({ message: 'Email parameter is required' });
      }
      
      const response = await axios.post(`${AUTH_SERVICE_URL}/sent-verify-link?email=${email}`);
      return res.status(response.status).json(response.data);
    } catch (error) {
      console.error('Verify email error:', error.message);
      
      if (error.response) {
        return res.status(error.response.status).json(error.response.data);
      }
      
      return res.status(500).json({
        message: 'Error connecting to authentication service'
      });
    }
}

module.exports = {
    Login,
    Register,
    VerifyEmail
}   