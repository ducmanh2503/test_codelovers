<?php
// Kết nối CSDL
$host = 'localhost';
$db = 'test';
$user = 'root';
$pass = '';
$conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);

// Hàm hiển thị danh sách sản phẩm
function displayProducts($conn)
{
    $stmt = $conn->query("SELECT * FROM products WHERE stock > 0");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Sản phẩm hiện có:<br>";
    foreach ($products as $product) {
        echo "{$product['id']}. {$product['product_name']} - Giá: {$product['product_price']}đ - Số lượng: {$product['stock']}<br>";
    }
}

// Hàm xử lý mua hàng
function purchaseProduct($conn, $productId, $amountPaid)
{
    // Lấy thông tin sản phẩm
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute(['id' => $productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product || $product['stock'] <= 0) {
        echo "Sản phẩm không hợp lệ hoặc hết hàng.<br>";
        return;
    }

    if ($amountPaid < $product['product_price']) {
        echo "Không đủ tiền. Giá sản phẩm là {$product['product_price']}đ.<br>";
        return;
    }

    // Tính tiền thừa và cập nhật kho
    $change = $amountPaid - $product['product_price'];
    $stmt = $conn->prepare("UPDATE products SET stock = stock - 1 WHERE id = :id");
    $stmt->execute(['id' => $productId]);

    // Lưu giao dịch
    $stmt = $conn->prepare("INSERT INTO transactions (product_id, amount_paid, change_returned, status) VALUES (:product_id, :amount_paid, :change_returned, 'success')");
    $stmt->execute([
        'product_id' => $productId,
        'amount_paid' => $amountPaid,
        'change_returned' => $change
    ]);

    echo "Giao dịch thành công! Nhận sản phẩm: {$product['product_name']}. Tiền thừa: {$change}đ.<br>";
}

// Hàm hủy giao dịch
function cancelTransaction($amountPaid)
{
    echo "Giao dịch bị hủy. Trả lại tiền: {$amountPaid}đ.<br>";
}

// Hiển thị danh sách sản phẩm
displayProducts($conn);

// Form xử lý
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product_id'];
    $amountPaid = $_POST['amount_paid'];
    $choice = $_POST['choice'];

    if ($choice == 1) {
        purchaseProduct($conn, $productId, $amountPaid);
    } else {
        cancelTransaction($amountPaid);
    }
}
?>

<form method="POST" action="">
    Nhập ID sản phẩm: <input type="number" name="product_id" required><br>
    Nhập số tiền của bạn: <input type="number" name="amount_paid" required><br>
    <button type="submit" name="choice" value="1">Tiếp tục</button>
    <button type="submit" name="choice" value="0">Hủy</button>
</form>