<?php

$host = "127.0.0.1";     
$user = 'root';          
$password = '';          
$database = 'event_management'; 


$mysqli = new mysqli($host, $user, $password, $database);


if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
?>


<!-- CREATE TABLE events (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(255) NOT NULL,
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,
    venue VARCHAR(255) NOT NULL,
    organizer_name VARCHAR(255) NOT NULL,
    contact_number VARCHAR(20) NOT NULL,
    ticket_price FLOAT NOT NULL,
    available_seats INT NOT NULL,
    event_type VARCHAR(100) NOT NULL
); -->


<!-- CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    mobile_no VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    type ENUM('organiser', 'attendee') NOT NULL
); -->

<!-- CREATE TABLE event_requests (
    id INT AUTO_INCREMENT PRIMARY KEY, -- Optional, if you want it displayed
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    event_type VARCHAR(50) NOT NULL,
    estimeted_price DECIMAL(10, 2) NOT NULL, -- Or estimated_price
    num_persons INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
); -->
