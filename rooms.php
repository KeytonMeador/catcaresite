<?php
session_start();
date_default_timezone_set('America/Chicago');

// Initialize cleaning checklist in session if not exists
if (!isset($_SESSION['rooms_cleaning'])) {
    $_SESSION['rooms_cleaning'] = [
        'S1' => ['am' => false, 'pm' => false, 'initials' => ''],
        'S2' => ['am' => false, 'pm' => false, 'initials' => ''],
        'S3' => ['am' => false, 'pm' => false, 'initials' => ''],
        'S4' => ['am' => false, 'pm' => false, 'initials' => ''],
        'S5' => ['am' => false, 'pm' => false, 'initials' => ''],
        'S6' => ['am' => false, 'pm' => false, 'initials' => ''],
        'L1' => ['am' => false, 'pm' => false, 'initials' => ''],
        'L2' => ['am' => false, 'pm' => false, 'initials' => ''],
        'L3 - Staff Cleaning' => ['am' => false, 'pm' => false, 'initials' => ''],
        'L4' => ['am' => false, 'pm' => false, 'initials' => ''],
        'L5A/L5B' => ['am' => false, 'pm' => false, 'initials' => ''],
        'L6' => ['am' => false, 'pm' => false, 'initials' => ''],
        'Overflow' => ['am' => false, 'pm' => false, 'initials' => '']
    ];
}

// Migrate old boolean format to new array format
foreach ($_SESSION['rooms_cleaning'] as &$data) {
    if (is_bool($data)) {
        $data = ['am' => false, 'pm' => false, 'initials' => ''];
    }
}
unset($data);

$message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'update_room') {
        $room = $_POST['room'] ?? '';
        if (array_key_exists($room, $_SESSION['rooms_cleaning'])) {
            $_SESSION['rooms_cleaning'][$room]['am'] = isset($_POST['am']) ? true : false;
            $_SESSION['rooms_cleaning'][$room]['pm'] = isset($_POST['pm']) ? true : false;
            $_SESSION['rooms_cleaning'][$room]['initials'] = trim($_POST['initials'] ?? '');
            $message = "Room $room updated successfully.";
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'clear_all') {
        foreach ($_SESSION['rooms_cleaning'] as &$data) {
            $data = ['am' => false, 'pm' => false, 'initials' => ''];
        }
        unset($data);
        $message = 'All rooms reset.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Cleaning Checklist - Cat Care Site</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h1>Room Cleaning Checklist</h1>
    <img src="red logo.jpg" alt="Cat Care Logo" class="logo">
    
    <nav class="page-nav">
        <a href="index.php" class="nav-link">Cat Care Records</a>
        <span class="nav-current">Room Checklist</span>
    </nav>
    
    <?php if ($message): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <table class="cleaning-table">
        <thead>
            <tr>
                <th>Room</th>
                <th style="width: 80px;">AM</th>
                <th style="width: 80px;">PM</th>
                <th>Initials</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($_SESSION['rooms_cleaning'] as $room => $data): ?>
                <tr>
                    <td class="room-name"><?php echo htmlspecialchars($room, ENT_QUOTES, 'UTF-8'); ?></td>
                    <form method="post" action="" style="display: contents;">
                        <input type="hidden" name="action" value="update_room">
                        <input type="hidden" name="room" value="<?php echo htmlspecialchars($room, ENT_QUOTES, 'UTF-8'); ?>">
                        
                        <td>
                            <label class="checkbox-wrapper">
                                <input type="checkbox" name="am" <?php echo $data['am'] ? 'checked' : ''; ?> onchange="this.form.submit();">
                                <span class="checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <label class="checkbox-wrapper">
                                <input type="checkbox" name="pm" <?php echo $data['pm'] ? 'checked' : ''; ?> onchange="this.form.submit();">
                                <span class="checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <input type="text" name="initials" maxlength="3" placeholder="e.g., ABC" value="<?php echo htmlspecialchars($data['initials'], ENT_QUOTES, 'UTF-8'); ?>" class="initials-input">
                        </td>
                    </form>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <form method="post" action="">
        <input type="hidden" name="action" value="clear_all">
        <button type="submit" class="clear-button" onclick="return confirm('Reset all rooms?');">Reset All</button>
    </form>
</div>
</body>
</html>

