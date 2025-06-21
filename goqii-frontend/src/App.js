// src/App.js
import React, { useState, useEffect } from 'react';
import api from './api';

function App() {
  const [users, setUsers] = useState([]);
  const [form, setForm] = useState({ name: '', email: '', password: '', dob: '' });
  const [formErrors, setFormErrors] = useState({});

  const fetchUsers = async () => {
    try {
      const res = await api.get('/');
      setUsers(res.data);
    } catch (err) {
      console.error('Failed to fetch users:', err);
    }
  };

  useEffect(() => {
    fetchUsers();
  }, []);

  const handleChange = (e) => {
    setForm({ ...form, [e.target.name]: e.target.value });
    setFormErrors({ ...formErrors, [e.target.name]: '' }); // Clear error on change
  };

  const validateForm = () => {
    const errors = {};

    if (!form.name.trim()) {
      errors.name = 'Name is required';
    } else if (!/^[a-zA-Z\s]{2,50}$/.test(form.name.trim())) {
      errors.name = 'Name must be 2-50 letters only';
    }

    if (!form.email.trim()) {
      errors.email = 'Email is required';
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
      errors.email = 'Invalid email format';
    }

    if (!form.password.trim()) {
      errors.password = 'Password is required';
    } else if (form.password.length < 6) {
      errors.password = 'Password must be at least 6 characters';
    }

    if (!form.dob.trim()) {
      errors.dob = 'Date of Birth is required';
    } else if (!/^\d{4}-\d{2}-\d{2}$/.test(form.dob)) {
      errors.dob = 'DOB must be in YYYY-MM-DD format';
    }

    return errors;
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    const errors = validateForm();
    if (Object.keys(errors).length > 0) {
      setFormErrors(errors);
      return;
    }

    try {
      await api.post('/', form);
      setForm({ name: '', email: '', password: '', dob: '' });
      setFormErrors({});
      fetchUsers(); // Refresh the user list
    } catch (err) {
      console.error('Failed to add user:', err);
    }
  };

  return (
    <div style={{ padding: 20 }}>
      <h2>GOQii User Manager</h2>

      <form onSubmit={handleSubmit} style={{ marginBottom: 20 }}>
        <div>
          <input name="name" placeholder="Name" value={form.name} onChange={handleChange}/>
          {formErrors.name && <div style={{ color: 'red' }}>{formErrors.name}</div>}
        </div>
        <div>
          <input name="email" placeholder="Email" type="email" value={form.email} onChange={handleChange}/>
          {formErrors.email && <div style={{ color: 'red' }}>{formErrors.email}</div>}
        </div>
        <div>
          <input name="password" placeholder="Password" type="password" value={form.password} onChange={handleChange}/>
          {formErrors.password && <div style={{ color: 'red' }}>{formErrors.password}</div>}
        </div>
        <div>
          <input name="dob" placeholder="DOB (YYYY-MM-DD)" value={form.dob} onChange={handleChange}/>
          {formErrors.dob && <div style={{ color: 'red' }}>{formErrors.dob}</div>}
        </div>
        <button type="submit">Add User</button>
      </form>

      <h3>User List</h3>
      {Array.isArray(users) && users.length > 0 ? (
        <table border="1" cellPadding="8" cellSpacing="0">
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Email</th>
              <th>Date of Birth</th>
            </tr>
          </thead>
          <tbody>
            {users.map((user, i) => (
              <tr key={i}>
                <td>{i + 1}</td>
                <td>{user.name}</td>
                <td>{user.email}</td>
                <td>{user.dob}</td>
              </tr>
            ))}
          </tbody>
        </table>
      ) : (
        <p>No users found.</p>
      )}
    </div>
  );
}

export default App;
