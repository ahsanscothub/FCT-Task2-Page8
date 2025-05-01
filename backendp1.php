<?php
$servername = "localhost";
$username = "your_username";
$password = "your_password";
$dbname = "your_database_name";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

function getTourPackageDetails($conn) {
  $sql = "SELECT * FROM tour_packages WHERE id = 1";
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    return $result->fetch_assoc();
  } else {
    return null;
  }
}

function getIncludedItems($conn, $package_id) {
  $sql = "SELECT item FROM inclusions WHERE package_id = $package_id AND included = 1";
  $result = $conn->query($sql);
  $items = array();
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $items[] = $row["item"];
    }
  }
  return $items;
}

function getNotIncludedItems($conn, $package_id) {
  $sql = "SELECT item FROM inclusions WHERE package_id = $package_id AND included = 0";
  $result = $conn->query($sql);
  $items = array();
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $items[] = $row["item"];
    }
  }
  return $items;
}

function getItineraryItems($conn, $package_id) {
  $sql = "SELECT title, description, estimated_time FROM itinerary WHERE package_id = $package_id ORDER BY day_order";
  $result = $conn->query($sql);
  $items = array();
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $items[] = $row;
    }
  }
  return $items;
}

function getReviews($conn, $package_id) {
  $sql = "SELECT r.review_text, u.name AS reviewer_name, u.country AS reviewer_country, u.profile_picture AS reviewer_pic FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.package_id = $package_id ORDER BY r.created_at DESC LIMIT 2";
  $result = $conn->query($sql);
  $reviews = array();
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $reviews[] = $row;
    }
  }
  return $reviews;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book_now'])) {
  $user_id = 1;
  $package_id = 1;
  $booking_date = date("Y-m-d H:i:s");
  $sql = "INSERT INTO bookings (user_id, package_id, booking_date, status) VALUES ($user_id, $package_id, '$booking_date', 'pending')";
  if ($conn->query($sql) === TRUE) {
    echo "<script>alert('Booking successful!');</script>";
  } else {
    echo "<script>alert('Error booking: " . $conn->error . "');</script>";
  }
}

$packageDetails = getTourPackageDetails($conn);
$includedItems = getIncludedItems($conn, 1);
$notIncludedItems = getNotIncludedItems($conn, 1);
$itineraryItems = getItineraryItems($conn, 1);
$reviews = getReviews($conn, 1);

$conn->close();
?>