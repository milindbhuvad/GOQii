// src/App.js
import React, { useState, useEffect } from 'react';
import api from './api';

function App() {
  const [users, setUsers] = useState([]);
  const [form, setForm] = useState({ name: '', email: '', password: '', dob: '' });

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
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      await api.post('/', form);
      setForm({ name: '', email: '', password: '', dob: '' });
      fetchUsers(); // Refresh the user list
    } catch (err) {
      console.error('Failed to add user:', err);
    }
  };

  return (
    <div style={{ padding: 20 }}>
      <h2>GOQii User Manager</h2>

      <form onSubmit={handleSubmit} style={{ marginBottom: 20 }}>
        <input name="name" placeholder="Name" value={form.name} onChange={handleChange} required />
        <input name="email" placeholder="Email" type="email" value={form.email} onChange={handleChange} required />
        <input name="password" placeholder="Password" type="password" value={form.password} onChange={handleChange} required />
        <input name="dob" placeholder="DOB (YYYY-MM-DD)" value={form.dob} onChange={handleChange} required />
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
