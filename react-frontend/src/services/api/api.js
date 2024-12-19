import ApiService from './ApiService';

const api = new ApiService(process.env.REACT_APP_BACKEND_URL);

export default api;
