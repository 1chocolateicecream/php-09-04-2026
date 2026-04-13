<?php
require_once 'connect.php';

/**
 * Fetch customers with orders from database
 */
function fetchCustomersWithOrders(PDO $conn): array {
    $stmt = $conn->query("
        select
            c.customer_id, c.name, c.surname, c.birthdate, c.points,
            o.order_id, o.date, o.comments, o.delivery_date, o.status
        from customers c
        left join orders o on c.customer_id = o.customer_id
        order by c.customer_id, o.order_id
    ");
    return $stmt->fetchAll();
}

/**
 * Transform flat query results into hierarchical customer->orders structure
 */
function transformToHierarchical(array $rows): array {
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
    
    return $customers;
}

/**
 * Render a single customer's orders as HTML table
 */
function renderOrdersTable(array $orders): string {
    $html = "<table border='1' cellpadding='5' cellspacing='0'>\n";
    $html .= "<tr>\n";
    $html .= "<th>order id</th>\n";
    $html .= "<th>date</th>\n";
    $html .= "<th>comments</th>\n";
    $html .= "<th>delivery date</th>\n";
    $html .= "<th>status</th>\n";
    $html .= "</tr>\n";

    foreach ($orders as $order) {
        $html .= "<tr>\n";
        $html .= "<td>" . htmlspecialchars($order['order_id']) . "</td>\n";
        $html .= "<td>" . htmlspecialchars($order['date']) . "</td>\n";
        $html .= "<td>" . htmlspecialchars($order['comments'] ?? '') . "</td>\n";
        $html .= "<td>" . htmlspecialchars($order['delivery_date']) . "</td>\n";
        $html .= "<td>" . htmlspecialchars($order['status']) . "</td>\n";
        $html .= "</tr>\n";
    }

    $html .= "</table>\n";
    return $html;
}

/**
 * Render a single customer as HTML
 */
function renderCustomer(array $customer): string {
    $html = "<h2>customer: " . htmlspecialchars($customer['name']) . " " . htmlspecialchars($customer['surname']) . "</h2>\n";
    $html .= "<p>birthdate: " . htmlspecialchars($customer['birthdate']) . "</p>\n";
    $html .= "<p>points: " . htmlspecialchars($customer['points']) . "</p>\n";

    if (count($customer['orders']) > 0) {
        $html .= renderOrdersTable($customer['orders']);
    } else {
        $html .= "<p>this customer has no orders!</p>\n";
    }

    $html .= "<hr>\n";
    return $html;
}

/**
 * Render all customers as HTML
 */
function renderHTML(array $customers): string {
    $html = "<h1>Customers and their orders</h1>\n";
    
    foreach ($customers as $customer) {
        $html .= renderCustomer($customer);
    }

    $html .= "<p>total customers: " . count($customers) . "</p>\n";
    return $html;
}

// Main execution
$rows = fetchCustomersWithOrders($conn);
$customers = transformToHierarchical($rows);
echo renderHTML($customers);
?>