<?php
include 'config.php';

$product_id = $_POST['product_id'];
$size = $_POST['size'];
$quantity = $_POST['quantity'];
$price = $_POST['price'];

$total = $price * $quantity;

mysqli_query($conn, "INSERT INTO orders (product_id, size, quantity, total_price)
VALUES ('$product_id', '$size', '$quantity', '$total')");

header("Location: shop.php");
?>