1. Clone the 
   https://github.com/milindbhuvad/GOQii.git

2. Create Database
   CREATE DATABASE user_api;

3. Create Table
   CREATE TABLE users (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(100) NOT NULL,
      email VARCHAR(100) NOT NULL UNIQUE,
      password VARCHAR(255) NOT NULL,
      dob DATE
   );

4. Copy api folder and paste in 
   xampp\htdocs\api

5. Navigate into the Project Directory
   cd GOQii/goqii-frontend
   
6. Install Dependencies
   Make sure you have Node.js and npm installed. Then install the required packages:
   npm install

7. Start the Development Server
   npm start
   This will start the app and open it in your default browser (usually at http://localhost:3000).