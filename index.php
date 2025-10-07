<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "event_management";

$conn = new mysqli($host, $user, $pass, $dbname);
if($conn->connect_error){
    die("Database connection failed: " . $conn->connect_error);
}

// ---------- ADD EVENT ----------
if(isset($_POST['add'])){
    $title = $_POST['title'];
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO events (title, description, event_date, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $title, $description, $event_date, $status);
    if($stmt->execute()){
        echo "<script>alert('Event added successfully!');window.location='index.php';</script>";
    } else {
        echo "<script>alert('Failed to add event.');</script>";
    }
}

// ---------- UPDATE EVENT ----------
if(isset($_POST['update'])){
    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE events SET title=?, description=?, event_date=?, status=? WHERE id=?");
    $stmt->bind_param("ssssi", $title, $description, $event_date, $status, $id);
    if($stmt->execute()){
        echo "<script>alert('Event updated successfully!');window.location='index.php';</script>";
    } else {
        echo "<script>alert('Failed to update event.');</script>";
    }
}

// ---------- DELETE EVENT ----------
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $conn->query("DELETE FROM events WHERE id=$id");
    echo "<script>alert('Event deleted successfully!');window.location='index.php';</script>";
}

// ---------- GET EVENT FOR EDIT ----------
$edit_event = null;
if(isset($_GET['edit'])){
    $id = $_GET['edit'];
    $res = $conn->query("SELECT * FROM events WHERE id=$id");
    $edit_event = $res->fetch_assoc();
}

// ---------- GET ALL EVENTS ----------
$events = $conn->query("SELECT * FROM events ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Events Management - Simple Theme</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            color: #333;
            margin: 0;
            padding: 0;
        }
        h2 {
            text-align: center;
            margin: 20px 0;
            color: #444;
        }
        form {
            width: 50%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin: 6px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"], .cancel-btn {
            width: auto;
            padding: 10px 20px;
            margin-top: 10px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .cancel-btn {
            background-color: #888;
            color: white;
            text-decoration: none;
            display: inline-block;
            line-height: 1.5;
        }
        .cancel-btn:hover {
            background-color: #666;
        }
        table {
            border-collapse: collapse;
            width: 90%;
            margin: 30px auto;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        a.edit {
            background-color: #2196F3;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
        }
        a.edit:hover {
            background-color: #0b7dda;
        }
        a.delete {
            background-color: #f44336;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
        }
        a.delete:hover {
            background-color: #da190b;
        }
    </style>
</head>
<body>

<h2>Events Management - Simple Theme</h2>

<!-- ADD / EDIT FORM -->
<form method="POST">
    <h3><?= $edit_event ? "Edit Event" : "Add New Event" ?></h3>
    <input type="hidden" name="id" value="<?= $edit_event['id'] ?? '' ?>">
    <input type="text" name="title" placeholder="Event Title" value="<?= $edit_event['title'] ?? '' ?>" required>
    <textarea name="description" placeholder="Event Description"><?= $edit_event['description'] ?? '' ?></textarea>
    <input type="date" name="event_date" value="<?= $edit_event['event_date'] ?? '' ?>" required>
    <select name="status">
        <option value="open" <?= ($edit_event['status'] ?? '')=='open'?'selected':'' ?>>Open</option>
        <option value="closed" <?= ($edit_event['status'] ?? '')=='closed'?'selected':'' ?>>Closed</option>
    </select>
    <br>
    <input type="submit" name="<?= $edit_event ? 'update' : 'add' ?>" value="<?= $edit_event ? 'Update Event' : 'Add Event' ?>">
    <?php if($edit_event): ?>
        <a href="index.php" class="cancel-btn">Cancel</a>
    <?php endif; ?>
</form>

<!-- EVENTS TABLE -->
<table>
    <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Description</th>
        <th>Date</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    <?php while($row = $events->fetch_assoc()): ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['title'] ?></td>
        <td><?= $row['description'] ?></td>
        <td><?= $row['event_date'] ?></td>
        <td><?= $row['status'] ?></td>
        <td>
            <a class="edit" href="index.php?edit=<?= $row['id'] ?>">Edit</a>
            <a class="delete" href="index.php?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html> 