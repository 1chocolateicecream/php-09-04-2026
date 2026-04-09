<?php
require_once 'connect.php';

// 1. fetch all customers (n+1)
$customersStmt = $conn->query("SELECT customer_id, name, surname, birthdate, points FROM customers");
$customers = $customersStmt->fetchAll();

echo "<h1>Customers and their orders</h1>\n";

// loop through each customer and fetch their orders (n+1 problem - n queries)
foreach ($customers as $customer) {
    echo "<h2>customer: " . htmlspecialchars($customer['name']) . " " . htmlspecialchars($customer['surname']) . "</h2>\n";
    echo "<p>birthdate: " . htmlspecialchars($customer['birthdate']) . "</p>\n";
    echo "<p>points: " . htmlspecialchars($customer['points']) . "</p>\n";

    // fetch orders for this specific customer (new query for each customer)
    $ordersStmt = $conn->prepare("SELECT order_id, date, comments, delivery_date, status FROM orders WHERE customer_id = :customer_id");
    $ordersStmt->execute(['customer_id' => $customer['customer_id']]);
    $orders = $ordersStmt->fetchAll();

    if (count($orders) > 0) {
        echo "<table border='1' cellpadding='5' cellspacing='0'>\n";
        echo "<tr>\n";
        echo "<th>order id</th>\n";
        echo "<th>date</th>\n";
        echo "<th>comments</th>\n";
        echo "<th>delivery date</th>\n";
        echo "<th>status</th>\n";
        echo "</tr>\n";

        foreach ($orders as $order) {
            echo "<tr>\n";
            echo "<td>" . htmlspecialchars($order['order_id']) . "</td>\n";
            echo "<td>" . htmlspecialchars($order['date']) . "</td>\n";
            echo "<td>" . htmlspecialchars($order['comments'] ?? '') . "</td>\n";
            echo "<td>" . htmlspecialchars($order['delivery_date']) . "</td>\n";
            echo "<td>" . htmlspecialchars($order['status']) . "</td>\n";
            echo "</tr>\n";
        }

        echo "</table>\n";
    } else {
        echo "<p>this customer has no orders!</p>\n";
    }

    echo "<hr>\n";
}

echo "<p>total customers: " . count($customers) . "</p>\n";
?>