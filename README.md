
# UltraMSG WhatsApp Integration Service

Welcome to the UltraMSG WhatsApp Integration Service. This project is designed to help you integrate WhatsApp messaging services into your application using the UltraMSG API. It handles incoming messages, saves them to a database, checks for specific message formats, and responds appropriately.

## Table of Contents 

- [Service Description](#service-description)
- [Features](#features)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Support](#support)
- [License](#license)

## Service Description

This service provides a comprehensive solution for integrating UltraMSG API with your application. It allows for automated responses to specific message patterns, message storage in a database, and handling customer card requests based on VIP keywords.

## Features

- **Integration with UltraMSG API**: Send and receive WhatsApp messages.
- **Automated Message Processing**: Respond to messages starting with specific keywords.
- **Database Storage**: Save incoming message data for further analysis and processing.
- **Customer Card Delivery**: Send digital customer cards based on VIP keyword detection.

## Installation

1. Clone the repository to your local environment:
    \`\`\`sh
    git clone https://github.com/raxlor/fiver-botwsp
    cd fiver-botwsp
    \`\`\`

2. Install the required dependencies using Composer:
    \`\`\`sh
    composer install
    \`\`\`

3. Set up your database and update the \`config.php\` file with your database credentials.

## Configuration

Create a \`config.php\` file in the root directory with the following structure:

```php
<?php
return [
    // UltraMSG configuration
    'ultramsg_instance_id' => 'ultramsg_instance_id', // Your UltraMSG instance ID
    'ultramsg_token' => 'ultramsg_token', // Your UltraMSG token

    // Database configuration
    'db_host' => 'localhost', // Database host
    'db_name' => 'name_db', // Database name
    'db_user' => 'db_user', // Database user
    'db_pass' => 'db_pass', // Database password

    // Predefined messages for various scenarios
    'messages' => [
        'no_email' => "Om je CHABROL Wines klantenkaart op je telefoon te ontvangen stuur je een bericht beginnend met VIP en gevolgd door jouw bij ons bekende e-mail adres. Probeer het nog eens of vraag een van onze medewerkers naar meer informatie.

To receive your CHABROL Wines customer card on your phone, send a message starting with VIP and followed by your known email address. Please try again or ask one of our associates for more information.",
        'no_card' => "Helaas is jouw e-mail adres (nog) niet bekend in ons systeem. Controleer je e-mail adres en probeer opnieuw of vraag een van onze medewerkers naar meer informatie.

Unfortunately, your e-mail address is not (yet) known in our system. Please check your email address and try again or ask one of our associates for more information.",
        'your_card' => "Hierbij de opgevraagde CHABROL Wines klantenkaart. Op iPhone kun je deze aan je wallet toevoegen door in de kaart rechtsboven op ‘toevoegen’ te klikken. Op Android/Google kán je een wallet app nodig hebben, dit zal je telefoon aangeven. Kom je er niet uit: vraag een van onze medewerkers naar meer informatie.

Here is the requested CHABROL Wines customer card. On iPhone you can add this to your wallet by clicking 'add' in the card at the top right. On Android/Google you may need a wallet app, your phone will indicate this. Please ask one of our associates for more information.",
        'no_serial' => "Helaas is er een fout opgetreden. Het serienummer van jouw klantenkaart kon niet worden gevonden. Probeer het opnieuw of vraag een van onze medewerkers om hulp.

Unfortunately, there was an error. The serial number for your customer card could not be found. Please try again or ask one of our associates for assistance."
    ]
];
?>
```

## Usage

1. Ensure your server is configured to receive and process incoming webhooks from UltraMSG.

2. The main functionality of this project is within the \`index.php\` file. It listens for incoming messages, processes them, and responds based on the message content.

3. To process messages, the script:
    - Loads the configuration.
    - Sends messages using the UltraMSG API.
    - Saves incoming messages to the database.
    - Checks if messages start with "VIP" and extracts the email address.
    - Verifies the email and updates user information if found.
    - Sends the customer card document to the user if the email is verified.

## Support

For any issues or customization requests, please contact me through Fiverr. I am here to help you with any aspect of this integration.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for more details.
#
