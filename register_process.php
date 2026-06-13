<?php
session_start();
require_once "dbcon.php";
if($_SERVER['REQUEST_METHOD']=="POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO users (name, email, phone, age, gender, address, password) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssisss", $name, $email, $phone, $age, $gender, $address, $hash);
    
    if($stmt->execute()) {
        echo "<script>alert('Registration successful! Please login.'); 
        window.location='login.php';</script>";
    } else {
        echo "<script>alert('Registration failed!'); 
        window.location='register.php';</script>";
    }
    exit();
}