<?php
session_start();
date_default_timezone_set('America/Chicago');
if (!isset($_SESSION['cats'])) {
    $_SESSION['cats'] = [
        [
            'CatName' => 'Whiskers',
            'Room' => '101',
            'Abnormality' => 'None',
            'Volunteer' => 'Mia',
            'CreatedAt' => '04/07/26 10:00'
        ],
        [
            'CatName' => 'Luna',
            'Room' => '202',
            'Abnormality' => 'Sneezing',
            'Volunteer' => 'Noah',
            'CreatedAt' => '04/07/26 11:15'
        ]
    ];
}
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['index'])) {
        $deleteIndex = filter_var($_POST['index'], FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 0]
        ]);
        if ($deleteIndex !== false && array_key_exists($deleteIndex, $_SESSION['cats'])) {
            array_splice($_SESSION['cats'], $deleteIndex, 1);
            $message = 'Cat record deleted successfully.';
        } else {
            $message = 'Unable to delete the selected record.';
        }
    } else {
        $name = trim($_POST['CatName'] ?? '');
        $room = trim($_POST['Room'] ?? '');
        $abnormality = trim($_POST['Abnormality'] ?? '');
        $volunteer = trim($_POST['Volunteer'] ?? '');
        if ($name === '' || $room === '' || $abnormality === '' || $volunteer === '') {
            $message = 'Please fill in all fields.';
        } else {
            $_SESSION['cats'][] = [
                'CatName' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
                'Room' => htmlspecialchars($room, ENT_QUOTES, 'UTF-8'),
                'Abnormality' => htmlspecialchars($abnormality, ENT_QUOTES, 'UTF-8'),
                'Volunteer' => htmlspecialchars($volunteer, ENT_QUOTES, 'UTF-8'),
                'CreatedAt' => date('m/d/y H:i')
            ];
            $message = 'Cat record added successfully.';
        }
    }
}

$searchTerm = trim($_GET['search'] ?? '');
$filteredCats = $_SESSION['cats'];
if ($searchTerm !== '') {
    $filteredCats = array_filter($_SESSION['cats'], function ($cat) use ($searchTerm) {
        return mb_stripos($cat['CatName'], $searchTerm, 0, 'UTF-8') !== false
            || mb_stripos($cat['Room'], $searchTerm, 0, 'UTF-8') !== false
            || mb_stripos($cat['Abnormality'], $searchTerm, 0, 'UTF-8') !== false
            || mb_stripos($cat['Volunteer'], $searchTerm, 0, 'UTF-8') !== false;
    });
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cat Care Site</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h1>Cat Care Record</h1>
    <img src="red logo.jpg" alt="Cat Care Logo" class="logo">
    
    <?php if ($message): ?>
        <div class="message<?php echo ($message === 'Please fill in all fields.' ? ' error' : ''); ?>"><?php echo $message; ?></div>
    <?php endif; ?>
    <form method="post" action="">
        <div>
            <label for="CatName">Cat Name</label>
            <input type="text" id="CatName" name="CatName" placeholder="Enter cat name" required>
        </div>
        <div>
            <label for="Room">Room</label>
            <input type="text" id="Room" name="Room" placeholder="Enter room" required>
        </div>
        <div>
            <label for="Abnormality">Abnormality noticed</label>
            <textarea id="Abnormality" name="Abnormality" placeholder="Describe any abnormality" required></textarea>
        </div>
        <div>
            <label for="Volunteer">Volunteer Name</label>
            <input type="text" id="Volunteer" name="Volunteer" placeholder="Enter volunteer name" required>
        </div>
        <button type="submit">Add Cat Record</button>
    </form>
    <form method="get" action="" class="search-form">
        <div>
            <label for="search">Search records</label>
            <input type="text" id="search" name="search" placeholder="Search by name, room, abnormality, or volunteer" value="<?php echo htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <button type="submit">Search</button>
    </form>
    <h2>Current Cat List</h2>
    <table>
        <thead>
            <tr>
                <th>Cat Name</th>
                <th>Room</th>
                <th>Abnormality noticed</th>
                <th>Volunteer</th>
                <th>Time Added</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if (count($filteredCats) === 0): ?>
            <tr>
                <td colspan="6">No matching records found.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($filteredCats as $index => $cat): ?>
                <tr>
                    <td><?php echo $cat['CatName']; ?></td>
                    <td><?php echo $cat['Room']; ?></td>
                    <td><?php echo $cat['Abnormality']; ?></td>
                    <td><?php echo $cat['Volunteer']; ?></td>
                    <td><?php echo $cat['CreatedAt']; ?></td>
                    <td>
                        <form method="post" action="" onsubmit="return confirm('Delete this record?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="index" value="<?php echo $index; ?>">
                            <button type="submit" class="delete-button">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
