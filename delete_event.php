<?php
include 'db.php';

if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];
    
    
    $sql = "DELETE FROM events WHERE event_id = ?"; 
    $con = $mysqli->prepare($sql);
    $con->bind_param("i", $event_id); 
    $con->execute();
    
    
    echo "<div class='alert'>Event deleted successfully!</div>";
}


$sql_check = "SELECT COUNT(*) FROM events";
$con_check = $mysqli->query($sql_check); 
$event_count = $con_check->fetch_row()[0]; 


if ($event_count > 0) {
    
    header("Location: view_events.php");
} else {
    
    header("Location: add_event.php");
}
exit; 
?>
