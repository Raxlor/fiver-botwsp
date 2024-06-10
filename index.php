<?php
// Load configuration
$config = include('config.php');

// Function to send messages using UltraMSG
function sendWhatsAppMessage($to, $body, $isFile = false, $fileUrl = null) {
    global $config;
    if ($isFile && $fileUrl) {
        $url = "https://api.ultramsg.com/" . $config['ultramsg_instance_id'] . "/messages/document?token=" . $config['ultramsg_token'];
        $data = [
            'to' => $to,
            'document' => $fileUrl,
            'filename' => basename($fileUrl),
            'caption' => $body
        ];
        $json = json_encode($data);

        $options = [
            'http' => [
                'header' => "Content-type: application/json\r\n",
                'method' => 'POST',
                'content' => $json
            ]
        ];
    } else {
        $url = "https://api.ultramsg.com/" . $config['ultramsg_instance_id'] . "/messages/chat?token=" . $config['ultramsg_token'] . "&to=" . urlencode($to) . "&body=" . urlencode($body);
        
        $options = [
            'http' => [
                'header' => "Content-type: application/json\r\n",
                'method' => 'GET'
            ]
        ];
    }

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    if ($result === FALSE) {
        error_log("Error sending message: " . print_r(error_get_last(), true));
    } else {
        error_log("Message sent successfully: " . $result);
    }
    
    return $result;
}

// Get the content of the message
$input = file_get_contents('php://input');
$message = json_decode($input, true);

// Save the message in the database
try {
    // Crear una nueva conexión PDO
    $pdo = new PDO('mysql:host=' . $config['db_host'] . ';dbname=' . $config['db_name'], $config['db_user'], $config['db_pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Preparar la sentencia SQL
    $stmt = $pdo->prepare("INSERT INTO tbl_messages (event_type, instanceId, id, referenceId, `data.id`, `data.from`, `data.to`, `data.author`, `data.pushname`, `data.ack`, `data.type`, `data.body`, `data.media`, `data.fromMe`, `data.self`, `data.isForwarded`, `data.isMentioned`, `data.quotedMsg`, `data.mentionedIds`, `data.time`) VALUES (:event_type, :instanceId, :id, :referenceId, :data_id, :data_from, :data_to, :data_author, :data_pushname, :data_ack, :data_type, :data_body, :data_media, :data_fromMe, :data_self, :data_isForwarded, :data_isMentioned, :data_quotedMsg, :data_mentionedIds, :data_time)");

    // Ejecutar la sentencia con los parámetros correspondientes
    $stmt->execute([
        ':event_type' => $message['event_type'],
        ':instanceId' => $message['instanceId'],
        ':id' => $message['id'],
        ':referenceId' => $message['referenceId'],
        ':data_id' => $message['data']['id'],
        ':data_from' => $message['data']['from'],
        ':data_to' => $message['data']['to'],
        ':data_author' => $message['data']['author'],
        ':data_pushname' => $message['data']['pushname'],
        ':data_ack' => $message['data']['ack'],
        ':data_type' => $message['data']['type'],
        ':data_body' => $message['data']['body'],
        ':data_media' => $message['data']['media'],
        ':data_fromMe' => $message['data']['fromMe'],
        ':data_self' => $message['data']['self'],
        ':data_isForwarded' => $message['data']['isForwarded'],
        ':data_isMentioned' => $message['data']['isMentioned'],
        ':data_quotedMsg' => json_encode($message['data']['quotedMsg']),
        ':data_mentionedIds' => json_encode($message['data']['mentionedIds']),
        ':data_time' => $message['data']['time']
    ]);

} catch (PDOException $e) {
    // Registrar el error en el log de errores
    error_log("Error saving message: " . $e->getMessage());
    
    // Mostrar un mensaje de error específico para el usuario (si es necesario)
    echo "There was an error saving your message. Please try again later.";
    
    // Salir del script
    exit;
}


// Check if the message starts with "VIP"
if (strpos($message['data']['body'], 'VIP') !== 0) {
    exit; // Terminate if the message does not start with "VIP"
}

// Extract the email address from the message body
preg_match('/VIP\s+(\S+@\S+)/', $message['data']['body'], $matches);
$email = $matches[1] ?? null;

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendWhatsAppMessage($message['data']['from'], $config['messages']['no_email']);
    exit;
}

// Search for the email in the user database
$stmt = $pdo->prepare("SELECT * FROM tbl_users WHERE email = :email");
$stmt->execute([':email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    sendWhatsAppMessage($message['data']['from'], $config['messages']['no_card']);
    exit;
}

// Check if the serialNumber exists
if (empty($user['serialNumber'])) {
    sendWhatsAppMessage($message['data']['from'], $config['messages']['no_serial']);
    exit;
}

// Extract the number part from the 'from' data
$mobilenr = preg_replace('/@.*$/', '', $message['data']['from']);

// Prepare and execute the SQL statement to update the user's mobile number
$stmt = $pdo->prepare("UPDATE tbl_users SET mobilenr = :mobilenr WHERE email = :email");
$stmt->execute([':mobilenr' => $mobilenr, ':email' => $email]);

// Send the document to the sender
$documentUrl = "https://vip.chabrol.wine/beheer/passes/{$user['serialNumber']}.pkpass";
sendWhatsAppMessage($message['data']['from'], $config['messages']['your_card'], true, $documentUrl);

// Update the messages table
$stmt = $pdo->prepare("UPDATE tbl_messages SET sent_via_WA = NOW() WHERE id = :id");
$stmt->execute([':id' => $message['id']]);

