<?php
include 'db.php';

$result = mysqli_query($con, "SELECT DISTINCT state FROM village ORDER BY state");
echo '<option value="">Select State</option>';
while($row = mysqli_fetch_assoc($result)) {
    echo "<option value='".htmlspecialchars($row['state'])."'>".htmlspecialchars($row['state'])."</option>";
}
mysqli_close($con);
?>