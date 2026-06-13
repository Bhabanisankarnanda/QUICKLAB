<?php
session_start();
require_once "dbcon.php";

$login_id = trim($_POST['login_id']);
$password = trim($_POST['password']);

$qry = "SELECT * FROM users WHERE email=? OR phone=?";
$stmt = $conn->prepare($qry);
$stmt->bind_param("ss", $login_id, $login_id);
$stmt->execute();
$res = $stmt->get_result();

if($res->num_rows == 0){
    $_SESSION['login_error'] = "Invalid Email/Phone!";
    header("location: login.php"); exit;
}

$user = $res->fetch_assoc();

if(password_verify($password, $user['password'])){
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];

    setcookie("quicklab_login", $user['id'], time()+3600*24*7, "/");

    header("location: homepage.php");
} else {
    $_SESSION['login_error'] = "Incorrect Password!";
    header("location: login.php");
}
?>
