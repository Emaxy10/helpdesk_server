<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ticket Created</title>
</head>
<body>
    <h2>🎟 Ticket Created Successfully</h2>

    <p>Your ticket has been created successfully with the following details:</p>

    <p><strong>Title:</strong> {{ $ticket->title }}</p>
    <p><strong>Description:</strong> {{ $ticket->description }}</p>

    </p>

    <p>We’ll get back to you as soon as possible.</p>

    <p>Thank you,<br>
    <strong>Helpdesk Support Team</strong></p>
</body>
</html>
