<?php
require_once 'connect.php';

// single query with left join to get all customers and their orders (solves n+1)
$stmt = $conn->query("
    select
        c.customer_id, c.name, c.surname, c.birthdate, c.points,
        o.order_id, o.date, o.comments, o.delivery_date, o.status
    from customers c
    left join orders o on c.customer_id = o.customer_id
    order by c.customer_id, o.order_id
");
$rows = $stmt->fetchAll();

echo "<h1>Customers and their orders</h1>\n";

// group rows by customer
$customers = [];
foreach ($rows as $row) {
    $id = $row['customer_id'];

    if (!isset($customers[$id])) {
        $customers[$id] = [
            'customer_id' => $row['customer_id'],
            'name' => $row['name'],
            'surname' => $row['surname'],
            'birthdate' => $row['birthdate'],
            'points' => $row['points'],
            'orders' => [],
        ];
    }

    if ($row['order_id'] !== null) {
        $customers[$id]['orders'][] = [
            'order_id' => $row['order_id'],
            'date' => $row['date'],
            'comments' => $row['comments'],
            'delivery_date' => $row['delivery_date'],
            'status' => $row['status'],
        ];
    }
}

foreach ($customers as $customer) {
    echo "<h2>customer: " . htmlspecialchars($customer['name']) . " " . htmlspecialchars($customer['surname']) . "</h2>\n";
    echo "<p>birthdate: " . htmlspecialchars($customer['birthdate']) . "</p>\n";
    echo "<p>points: " . htmlspecialchars($customer['points']) . "</p>\n";

    if (count($customer['orders']) > 0) {
        echo "<table border='1' cellpadding='5' cellspacing='0'>\n";
        echo "<tr>\n";
        echo "<th>order id</th>\n";
        echo "<th>date</th>\n";
        echo "<th>comments</th>\n";
        echo "<th>delivery date</th>\n";
        echo "<th>status</th>\n";
        echo "</tr>\n";

        foreach ($customer['orders'] as $order) {
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