import ApiService from './ApiService';

// Используем переменную окружения для API URL, с fallback на localhost
const API_BASE_URL = process.env.REACT_APP_API_URL || 'http://localhost:8080';

const api = new ApiService(API_BASE_URL);

export default api;
