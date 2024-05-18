<?php
$host = 'localhost';
$db = 'shopping';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create the database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $db");
    echo "Database created successfully.<br>";

    // Connect to the new database
    $pdo->exec("USE $db");

    // Create the products table
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        price DECIMAL(10, 2) NOT NULL
    )");
    echo "Table 'products' created successfully.<br>";

    // Insert sample data
    $pdo->exec("INSERT INTO products (name, description, price) VALUES
        ('Product 1', 'Description for product 1', 10.00),
        ('Product 2', 'Description for product 2', 20.00),
        ('Product 3', 'Description for product 3', 30.00)");
    echo "Sample data inserted successfully.<br>";

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<?php
$host = 'localhost';
$db   = 'shopping';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}
?>
<?php
include 'config/database.php';

$query = $pdo->query("SELECT * FROM products");
$products = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Online Shopping</title>
</head>
<body>
    <h1>Products</h1>
    <div class="products">
        <?php foreach ($products as $product): ?>
            <div class="product">
                <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                <p><?php echo htmlspecialchars($product['description']); ?></p>
                <p>$<?php echo number_format($product['price'], 2); ?></p>
                <a href="/cart/add.php?id=<?php echo $product['id']; ?>">Add to Cart</a>
            </div>
        <?php endforeach; ?>
    </div>
    <a href="/cart/index.php">View Cart</a>
</body>
</html>
<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart = $_SESSION['cart'];
$products = [];

if (!empty($cart)) {
    $ids = implode(',', array_fill(0, count($cart), '?'));
    $query = $pdo->prepare("SELECT * FROM products WHERE id IN ($ids)");
    $query->execute(array_keys($cart));
    $products = $query->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cart</title>
</head>
<body>
    <h1>Your Cart</h1>
    <?php if (empty($products)): ?>
        <p>Your cart is empty.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($products as $product): ?>
                <li>
                    <?php echo htmlspecialchars($product['name']); ?> - 
                    $<?php echo number_format($product['price'], 2); ?> x 
                    <?php echo $cart[$product['id']]; ?> = 
                    $<?php echo number_format($product['price'] * $cart[$product['id']], 2); ?>
                    <a href="/cart/remove.php?id=<?php echo $product['id']; ?>">Remove</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <a href="/">Continue Shopping</a>
</body>
</html>
<?php
session_start();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_SESSION['cart'][$id])) {
    $_SESSION['cart'][$id]++;
} else {
    $_SESSION['cart'][$id] = 1;
}

header('Location: /cart/index.php');
exit;
?>
<?php
session_start();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (isset($_SESSION['cart'][$id])) {
    unset($_SESSION['cart'][$id]);
}

header('Location: /cart/index.php');
exit;
?>
<?php
include '../config/database.php';

$query = $pdo->query("SELECT * FROM products");
$products = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Products</title>
</head>
<body>
    <h1>Products</h1>
    <div class="products">
        <?php foreach ($products as $product): ?>
            <div class="product">
                <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                <p><?php echo htmlspecialchars($product['description']); ?></p>
                <p>$<?php echo number_format($product['price'], 2); ?></p>
                <a href="/cart/add.php?id=<?php echo $product['id']; ?>">Add to Cart</a>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>

