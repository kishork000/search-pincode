<?php
include 'db.php';

$state = mysqli_real_escape_string($con, $_POST['state']);
$result = mysqli_query($con, "SELECT DISTINCT distname FROM village WHERE state='$state' ORDER BY distname");
echo '<option value="">Select District</option>';
while($row = mysqli_fetch_assoc($result)) {
    echo "<option value='".htmlspecialchars($row['distname'])."'>".htmlspecialchars($row['distname'])."</option>";
}
mysqli_close($con);
?>