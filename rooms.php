<?php
session_start();
date_default_timezone_set('America/Chicago');

// Initialize cleaning checklist in session if not exists
if (!isset($_SESSION['rooms_cleaning'])) {
    $_SESSION['rooms_cleaning'] = [
        'date' => date('m/d/Y'),
        'S1' => ['am' => false, 'pm' => false, 'am_initials' => '', 'pm_initials' => ''],
        'S2' => ['am' => false, 'pm' => false, 'am_initials' => '', 'pm_initials' => ''],
        'S3' => ['am' => false, 'pm' => false, 'am_initials' => '', 'pm_initials' => ''],
        'S4' => ['am' => false, 'pm' => false, 'am_initials' => '', 'pm_initials' => ''],
        'S5' => ['am' => false, 'pm' => false, 'am_initials' => '', 'pm_initials' => ''],
        'S6' => ['am' => false, 'pm' => false, 'am_initials' => '', 'pm_initials' => ''],
        'L1' => ['am' => false, 'pm' => false, 'am_initials' => '', 'pm_initials' => ''],
        'L2' => ['am' => false, 'pm' => false, 'am_initials' => '', 'pm_initials' => ''],
        'L3 - Staff Cleaning' => ['am' => false, 'pm' => false, 'am_initials' => '', 'pm_initials' => ''],
        'L4' => ['am' => false, 'pm' => false, 'am_initials' => '', 'pm_initials' => ''],
        'L5A/L5B' => ['am' => false, 'pm' => false, 'am_initials' => '', 'pm_initials' => ''],
        'L6' => ['am' => false, 'pm' => false, 'am_initials' => '', 'pm_initials' => ''],
        'Overflow' => ['am' => false, 'pm' => false, 'am_initials' => '', 'pm_initials' => '']
    ];
}

// Initialize history if not exists
if (!isset($_SESSION['rooms_cleaning_history'])) {
    $_SESSION['rooms_cleaning_history'] = [];
}

// Migrate old boolean format to new array format
foreach ($_SESSION['rooms_cleaning'] as &$data) {
    if (is_bool($data)) {
        $data = ['am' => false, 'pm' => false, 'am_initials' => '', 'pm_initials' => ''];
    }
    // Migrate old initials field to new separate fields
    if (isset($data['initials']) && !isset($data['am_initials'])) {
        $data['am_initials'] = $data['initials'];
        $data['pm_initials'] = '';
        unset($data['initials']);
    }
}
unset($data);

$message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'update_room') {
        $room = $_POST['room'] ?? '';
        if (array_key_exists($room, $_SESSION['rooms_cleaning']) && $room !== 'date') {
            $_SESSION['rooms_cleaning'][$room]['am'] = isset($_POST['am']) ? true : false;
            $_SESSION['rooms_cleaning'][$room]['pm'] = isset($_POST['pm']) ? true : false;
            $_SESSION['rooms_cleaning'][$room]['am_initials'] = trim($_POST['am_initials'] ?? '');
            $_SESSION['rooms_cleaning'][$room]['pm_initials'] = trim($_POST['pm_initials'] ?? '');
            $message = "Room $room updated successfully.";
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'clear_all') {
        // Save current checklist to history before clearing
        $current_date = $_SESSION['rooms_cleaning']['date'];
        $_SESSION['rooms_cleaning_history'][$current_date] = $_SESSION['rooms_cleaning'];
        
        // Keep only the last 7 entries
        if (count($_SESSION['rooms_cleaning_history']) > 7) {
            $_SESSION['rooms_cleaning_history'] = array_slice($_SESSION['rooms_cleaning_history'], -7, 7, true);
        }
        
        // Reset current checklist with new date
        $new_checklist = [
            'date' => date('m/d/Y'),
            'S1' => ['am' => false, 'pm' => false, 'am_initials' => '', 'pm_initials' => ''],
            'S2' => ['am' => false, 'pm' => false, 'am_initials' => '', 'pm_initials' => ''],
            'S3' => ['am' => false, 'pm' => false, 'am_initials' => '', 'pm_initials' => ''],
            'S4' => ['am' => false, 'pm' => false, 'am_initials' => '', 'pm_initials' => ''],
            'S5' => ['am' => false, 'pm' => false, 'am_initials' => '', 'pm_initials' => ''],
            'S6' => ['am' => false, 'pm' => false, 'am_initials' => '', 'pm_initials' => ''],
            'L1' => ['am' => false, 'pm' => false, 'am_initials' => '', 'pm_initials' => ''],
            'L2' => ['am' => false, 'pm' => false, 'am_initials' => '', 'pm_initials' => ''],
            'L3 - Staff Cleaning' => ['am' => false, 'pm' => false, 'am_initials' => '', 'pm_initials' => ''],
            'L4' => ['am' => false, 'pm' => false, 'am_initials' => '', 'pm_initials' => ''],
            'L5A/L5B' => ['am' => false, 'pm' => false, 'am_initials' => '', 'pm_initials' => ''],
            'L6' => ['am' => false, 'pm' => false, 'am_initials' => '', 'pm_initials' => ''],
            'Overflow' => ['am' => false, 'pm' => false, 'am_initials' => '', 'pm_initials' => '']
        ];
        $_SESSION['rooms_cleaning'] = $new_checklist;
        $message = 'All rooms reset and previous checklist saved.';
    } elseif (isset($_POST['action']) && $_POST['action'] === 'clear_history') {
        $_SESSION['rooms_cleaning_history'] = [];
        $message = 'History cleared.';
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
                <th>Initials AM</th>
                <th>Initials PM</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($_SESSION['rooms_cleaning'] as $room => $data): ?>
                <?php if ($room === 'date') continue; // Skip date field ?>
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
                            <input type="text" name="am_initials" maxlength="3" placeholder="ex: ABC" value="<?php echo htmlspecialchars($data['am_initials'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="initials-input">
                        </td>
                        <td>
                            <input type="text" name="pm_initials" maxlength="3" placeholder="ex: ABC" value="<?php echo htmlspecialchars($data['pm_initials'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="initials-input">
                        </td>
                    </form>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="button-group">
        <form method="post" action="" style="display: inline;">
            <input type="hidden" name="action" value="clear_all">
            <button type="submit" class="clear-button" onclick="return confirm('Reset all rooms? Current checklist will be saved to history.');">Reset All & Save</button>
        </form>
    </div>

    <?php if (!empty($_SESSION['rooms_cleaning_history'])): ?>
        <h2>Checklist History</h2>
        <div class="history-container">
            <?php foreach (array_reverse($_SESSION['rooms_cleaning_history'], true) as $date => $checklist): ?>
                <div class="history-entry">
                    <h3><?php echo htmlspecialchars($date, ENT_QUOTES, 'UTF-8'); ?></h3>
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>Room</th>
                                <th>AM</th>
                                <th>PM</th>
                                <th>AM Initials</th>
                                <th>PM Initials</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($checklist as $room => $data): ?>
                                <?php if ($room === 'date') continue; ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($room, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo $data['am'] ? '✓' : ''; ?></td>
                                    <td><?php echo $data['pm'] ? '✓' : ''; ?></td>
                                    <td><?php echo htmlspecialchars($data['am_initials'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($data['pm_initials'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="button-group">
            <form method="post" action="" style="display: inline;">
                <input type="hidden" name="action" value="clear_history">
                <button type="submit" class="delete-button" onclick="return confirm('Delete all history? This cannot be undone.');">Clear History</button>
            </form>
        </div>
    <?php endif; ?>
</div>
</body>
</html>








