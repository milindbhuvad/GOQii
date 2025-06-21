// src/api.js
import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost/GOQii/api/',
  auth: {
    username: 'admin',   // <-- replace with actual API username
    password: 'Admin@123'    // <-- replace with actual API password
  },
  headers: {
    'Content-Type': 'application/json',
  },
});

export default api;
