<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(204);  
    exit;
}

require '../config/db.php';

header('Content-Type: application/json');

try {
    if (isset($_GET['email']) && !empty($_GET['email'])) {
        $email = $_GET['email'];

        $db->where("email", $email);
        $user = $db->getOne("users");

        if ($user) {
            $thirtyDaysAgo = date('Y-m-d', strtotime('-30 days'));

            $db->join("users u", "o.user_id=u.id", "INNER");
            $db->where("u.email", $email);
            $db->where("o.order_date", $thirtyDaysAgo, ">=");
            $orders = $db->get("orders o", null, "o.id as order_id, o.details as order_details, o.order_date as order_date, u.id as user_id, u.name as user_name, u.email as user_email");

            if ($orders) {

                $response = [
                    'user' => [
                        'id' => $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email']
                    ],
                    'orders' => []
                ];
                foreach ($orders as $order) {
                    $response['orders'][] = [
                        'order_id' => $order['order_id'],
                        'order_details' => $order['order_details'],
                        'order_date' => $order['order_date']

                    ];
                }
                echo json_encode($response);
            } else {
                echo json_encode(['error' => 'Nessun ordine trovato per l\'email fornita']);
            }
        } else {
            echo json_encode(['error' => 'Nessun utente trovato con questa email!']);
        }
    } else {
        echo json_encode(['error' => 'Il parametro email è mancante o vuoto']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Si è verificato un errore: ' . $e->getMessage()]);
}
?>
