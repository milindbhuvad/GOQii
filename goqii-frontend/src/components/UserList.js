// src/components/UserList.js
import React, { useEffect, useState } from 'react';
import api from '../api';

const UserList = ({ refresh }) => {
  const [users, setUsers] = useState([]);

  useEffect(() => {
    const fetchUsers = async () => {
      try {
        const response = await api.get('/');
        setUsers(response.data);
      } catch (err) {
        console.error('Error fetching users', err);
      }
    };

    fetchUsers();
  }, [refresh]);

  return (
    <div>
      <h2>User List</h2>
        {Array.isArray(users) ? users.map((user, i) => (
        <table border="1" cellPadding="10" cellSpacing="0">
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
        )) : <p>No users found.</p>}
    </div>
  );
};

export default UserList;
