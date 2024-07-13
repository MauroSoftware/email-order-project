# Componente di Ricerca Ordini per Email

Questo progetto è un'applicazione web basata su Vue.js che consente agli utenti di cercare ordini tramite email utilizzando API REST. Include un componente backend per gestire il servizio API REST e le interazioni con il database tramite PHP.

## Traspilazione del Codice
Per garantire la compatibilità con i browser che non supportano ES6, il codice è stato traspilato in ES5. La traspilazione è stata realizzata utilizzando Babel in combinazione con Webpack.

![Immagine componente](https://github.com/MauroSoftware/email-order-project/blob/master/email-order-component-img.png)
## Indice

- [Struttura del Progetto](#struttura-del-progetto)
- [Configurazione](#configurazione-del-backend-php)
- [Utilizzo](#utilizzo)
- [Testing](#Testare-il-componente-già-compilato)

## Struttura del Progetto

```
email-order-project/
├── email-order-component/
│   ├── public/
│   │   └── index.html
│   ├── src/
│   │   ├── components/
│   │   │   └── EmailOrderComponent.vue
│   │   └── main.js
│   ├── babel.config.json
│   ├── jsconfig.json
│   ├── package.json
│   └── vue.config.js
│
└── php-backend/
    ├── api/
    │   └── getOrders.php
    ├── class/
    │   └── MysqliDb.php
    └── config/
        └── db.php

```

## Configurazione del backend php

#### 1. Configurazione del Database

Modifica il file [`config/db.php`](https://github.com/MauroSoftware/email-order-project/blob/master/php-backend/config/db.php) inserendo credenziali di accesso al database:

```php
if (!class_exists('MysqliDb')) {
    require_once '../class/MysqliDb.php';
}

$db = new MysqliDb ('host', 'username', 'password', 'databaseName'); // inserire qui le credenziali di accesso al database

```

#### 2. Configurazione dell'API

**Nota bene:** È possibile includere qualsiasi tipo di chiave nell'oggetto 'orders' per visualizzarla come colonna nella tabella del componente.

```php
if (isset($_GET['email']) && !empty($_GET['email'])) {
    $email = $_GET['email'];


    $db->where("email", $email);
    $user = $db->getOne("users");

    if ($user) {
        $thirtyDaysAgo = date('Y-m-d', strtotime('-30 days'));

        $db->join("users u", "o.user_id=u.id", "INNER");
        $db->where("u.email", $email);
        $db->where("o.order_date", $thirtyDaysAgo, ">=");
        $orders = $db->get("orders o", null, "o.*, u.*");

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
                    'order_id' => $order['id'],
                    'order_details' => $order['details'],   //qui puoi aggiungere qualsiasi chiave/valore
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

```

## Utilizzo

### Integrazione in un progetto Vue.js

1. Inserisci il componente [EmailOrderComponent](https://github.com/MauroSoftware/email-order-project/blob/master/email-order-component/src/components/EmailOrderComponent.vue) nel tuo progetto

2. Montare il componente dove desiderato:

Importa il componente nel template e specifica l'endpoint API appropriato:

```vue
<template>
  <div id="app">
    <!-- Sostituire con il proprio endpoint-->
    <EmailOrderComponent
      apiEndpoint="http://localhost:8000/api/getOrders.php"
    />
  </div>
</template>

<script>
import EmailOrderComponent from "./components/EmailOrderComponent.vue";

export default {
  name: "App",
  components: {
    EmailOrderComponent,
  },
};
</script>
```

### Utilizzo come libreria JavaScript autonoma

1. Includere i file del componente:

```html
<script defer="defer" src="/mylibrary.bundle.ccbf70096413b306.js"></script>
<link href="/css/app.d6be98eb.css" rel="stylesheet" />
```

2. Inizializzazare il componente:

```javascript
const EmailOrderComponent = MyLibrary.EmailOrderComponent;

// Assegna l'endpoint API
var apiEndpoint = "http://localhost:8000/api/getOrders.php";

const app = MyLibrary.createApp(EmailOrderComponent, {
  apiEndpoint: apiEndpoint,
}).mount("#MailOrdersAppComponent"); // Inserisci qui il punto di montaggio desiderato
```

## Test del [Componente Precompilato](https://github.com/MauroSoftware/email-order-project/blob/master/testing/dist):

1. [Configurare l'accesso al database](#1-configurazione-del-database)

2. Avvia il server di sviluppo PHP.

```bash
cd php-backend
php -S localhost:8000
```

3. Esegui la query per generare il database, le tabelle e i dati di prova

```sql
CREATE DATABASE test_db;

USE test_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    details TEXT,
    order_date DATE,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

INSERT INTO users (email, name) VALUES
('user1@example.com', 'User One'),
('user2@example.com', 'User Two'),
('user3@example.com', 'User Three'),
('user4@example.com', 'User Four'),
('user5@example.com', 'User Five'),
('user6@example.com', 'User Six'),
('user7@example.com', 'User Seven'),
('user8@example.com', 'User Eight'),
('user9@example.com', 'User Nine'),
('user10@example.com', 'User Ten');


INSERT INTO orders (user_id, order_date, details) VALUES
(1, '2024-07-10', 'Order details for user 1 - recent order'),
(1, '2024-06-25', 'Order details for user 1 - 15 days ago'),
(1, '2024-06-05', 'Order details for user 1 - 35 days ago'),
(2, '2024-07-10', 'Order details for user 2 - recent order'),
(2, '2024-07-05', 'Order details for user 2 - 5 days ago'),
(2, '2024-05-31', 'Order details for user 2 - 40 days ago'),
(3, '2024-07-10', 'Order details for user 3 - recent order'),
(3, '2024-06-15', 'Order details for user 3 - 25 days ago'),
(3, '2024-05-21', 'Order details for user 3 - 50 days ago'),
(4, '2024-07-10', 'Order details for user 4 - recent order'),
(4, '2024-06-30', 'Order details for user 4 - 10 days ago'),
(5, '2024-07-10', 'Order details for user 5 - recent order'),
(5, '2024-06-10', 'Order details for user 5 - 30 days ago'),
(6, '2024-07-10', 'Order details for user 6 - recent order'),
(6, '2024-06-20', 'Order details for user 6 - 20 days ago'),
(7, '2024-07-10', 'Order details for user 7 - recent order'),
(7, '2024-07-02', 'Order details for user 7 - 8 days ago'),
(8, '2024-07-10', 'Order details for user 8 - recent order'),
(8, '2024-06-22', 'Order details for user 8 - 18 days ago'),
(9, '2024-07-10', 'Order details for user 9 - recent order'),
(9, '2024-06-28', 'Order details for user 9 - 12 days ago'),
(10, '2024-07-10', 'Order details for user 10 - recent order'),
(10, '2024-06-12', 'Order details for user 10 - 28 days ago');

```

4. Aprire con il browser il file [testing/dist/index.html](https://github.com/MauroSoftware/email-order-project/blob/master/testing/dist/index.html) ed eseguire la ricerca!
