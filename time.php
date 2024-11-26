<?php
session_start();

// Database connection details
$host = 'localhost'; // Replace with your database host
$dbname = 'finalproj'; // Replace with your database name
$username = 'root'; // Replace with your database username
$password = ''; // Replace with your database password

// Create a connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Allow-Headers: Content-Type");

// Check if the date is passed in the request
if (isset($_POST['date'])) {
    $selectedDate = $_POST['date']; // Date passed from the frontend

    // Define time slots for the restaurant
    $timeSlots = [
        '10:00:00',
        '11:00:00',
        '12:00:00',
        '13:00:00',
        '14:00:00',
        '15:00:00',
        '16:00:00',
        '17:00:00',
        '18:00:00',
        '19:00:00',
        '20:00:00'
    ];

    // Assume $pdo is your database connection
    $stmt = $pdo->prepare("SELECT time FROM customers WHERE date = :date AND status = 'Booked'");
    $stmt->bindParam(':date', $selectedDate);
    $stmt->execute();

    $bookedTimes = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Count how many times each time slot has been booked
    $timeSlotCounts = array_count_values($bookedTimes);

    // Define the number of available tables
    $maxTables = 3;

    // Filter out the fully booked time slots
    $availableTimes = array_filter($timeSlots, function ($time) use ($timeSlotCounts, $maxTables) {
        return (!isset($timeSlotCounts[$time]) || $timeSlotCounts[$time] < $maxTables);
    });

    // Return available times as JSON
    header('Content-Type: application/json');
    echo json_encode(array_values($availableTimes));
}
