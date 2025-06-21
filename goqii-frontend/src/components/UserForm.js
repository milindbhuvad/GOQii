// src/components/UserForm.js
import React, { useState } from 'react';
import api from '../api';

const UserForm = ({ onUserAdded }) => {
  const [formData, setFormData] = useState({ name: '', email: '', password: '', dob: '' });

  const handleChange = (e) => {
    setFormData({...formData, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      await api.post('/', formData);
      onUserAdded(); // Refresh the user list
      setFormData({ name: '', email: '', password: '', dob: '' });
    } catch (err) {
      console.error('Error adding user', err);
    }
  };

  return (
    <form onSubmit={handleSubmit}>
      <input name="name" placeholder="Name" value={formData.name} onChange={handleChange} required />
      <input name="email" placeholder="Email" type="email" value={formData.email} onChange={handleChange} required />
      <input name="password" placeholder="Password" type="password" value={formData.password} onChange={handleChange} required />
      <input name="dob" placeholder="DOB (YYYY-MM-DD)" value={formData.dob} onChange={handleChange} required />
      <button type="submit">Add User</button>
    </form>
  );
};

export default UserForm;
