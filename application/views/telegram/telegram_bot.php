<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Telegram Bot</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body>
    <h1>Telegram Bot</h1>

    <script>
        const base_url = '<?=base_url();?>';
        const BOT_TOKEN = '6818227164:AAFUNNqXcyqYp4To6pxUlMrdFAZHDY_00gU'; // Replace with your bot token
        const TELEGRAM_API_BASE = `https://api.telegram.org/bot${BOT_TOKEN}`;

        // Function to send a message using Telegram Bot API
        function sendMessage(chatId, text) {
            const apiUrl = `${TELEGRAM_API_BASE}/sendMessage`;

            $.post(apiUrl, {
                chat_id: chatId,
                text: text,
            })
            .done(response => {
                console.log('Message sent:', response);
            })
            .fail(error => {
                console.error('Error sending message:', error);
            });
        }

        // Function to handle incoming messages
        function handleMessage(message) {
            if (message && message.text) {
                const chat_id = message.chat.id;
                const tg_username = message.chat.username;
                const message_text = message.text;
                let text = '';

                // Your bot logic here
                // For example, echoing the received message
               userData(chat_id, tg_username, message_text);
                if( message_text == `/start`){
                    text = "Hello! Welcome to AltcoinsTalks Notifier! \n\nWhat is your AltcoinsTalks username?";
                    insertTelegramData(chat_id, tg_username);
                    sendMessage(chat_id, text);
                }
                
                // else{
                //     text = 'Message /start to start!';
                //     sendMessage(chat_id, text);
                // }
            }
        }

        // Function to fetch updates from Telegram Bot API
        function getUpdates() {
            const apiUrl = `${TELEGRAM_API_BASE}/getUpdates`;

            $.get(apiUrl)
            .done(response => {
                if (response.ok) {
                    const updates = response.result;
                    updates.forEach(update => {
                        handleMessage(update.message);
                        insertTelegramMsg(update.message.message_id, update.message.text, update.message.chat.id);
                    });
                    dropPendingUpdate();
                }
            })
            .fail(error => {
                console.error('Error fetching updates:', error);
            });
        }
        function dropPendingUpdate() {
            const apiUrl = `${TELEGRAM_API_BASE}/setWebhook?drop_pending_updates=true`;
            $.get(apiUrl)
            .done(response => {
            })
            .fail(error => {
                console.error('Error fetching updates:', error);
            });
        }

        function userData(chat_id, tg_username, message_text) {
            const apiUrl = `${base_url}api/_get_telegram_data`;
            $.get(apiUrl, {
                chat_id: chat_id,
                username: tg_username,
            })
            .done(response => {
                if (response.ok) {
                    text = "Hello "+message_text;
                    sendMessage(chat_id, text);
                }
                else{
                    return false;
                }
            })
            .fail(error => {
                console.error('Error fetching updates:', error);
            });
        }
        function insertTelegramData(chat_id, username) {
            const apiUrl = `${base_url}api/_telegram_register`;

            $.post(apiUrl, {
                chat_id: chat_id,
                username: username,
            })
            .done(response => {
                console.log(response.chat_id);
            })
            .fail(error => {
                console.error('Error sending message:', error);
            });
        }
        function insertTelegramMsg(message_id, message_text, chat_id) {
            const apiUrl = `${base_url}api/_insert_telegram_msg`;

            $.post(apiUrl, {
                message_id: message_id,
                chat_id: chat_id,
                message_text: message_text,
            })
            .done(response => {
                console.log(response);
            })
            .fail(error => {
                console.error('Error sending message:', error);
            });
        }


        // Poll for updates every 5 seconds (you can adjust the interval)
        // setInterval(getUpdates, 5000);
        getUpdates();

    </script>
</body>
</html>


