<?php
include "config/database.php";

$id = $_GET['id'];

$query = mysqli_query($conn, "SELECT * FROM products WHERE id='$id'");
$product = mysqli_fetch_assoc($query);
?>

<link rel="stylesheet" href="css/style.css">

<div class="product-detail">
    <div class="product-left">
        <img src="assets/<?php echo $product['image']; ?>">
    </div>

    <div class="product-right">
        <h2><?php echo $product['name']; ?></h2>
        <p class="price">Rp <?php echo number_format($product['price']); ?></p>

        <form method="POST" action="process_order.php">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            <input type="hidden" name="product_name" value="<?php echo $product['name']; ?>">
            <input type="hidden" name="price" value="<?php echo $product['price']; ?>">

            <label>Size:</label>
            <select name="size" required>
                <option value="S">S</option>
                <option value="M">M</option>
                <option value="L">L</option>
                <option value="XL">XL</option>
            </select>

            <label>Quantity:</label>
            <input type="number" name="quantity" value="1" min="1">

            <button type="submit" class="btn-buy">Buy Now</button>
        </form>
    </div>
</div>