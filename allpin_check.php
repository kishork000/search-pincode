<?php
$hostname = "localhost";
$username = "codpin";
$password = "passw0rd@098";
$dbname = "indianpincode";

// Connect to database
$con = mysqli_connect($hostname, $username, $password, $dbname);
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['name1']) && !empty(trim($_POST['name1']))) {
    $name1 = mysqli_real_escape_string($con, trim($_POST['name1']));

    // Primary search in allindia_pincode
    $sql = "SELECT * FROM allindia_pincode 
            WHERE allpincode LIKE '%$name1%' 
               OR poname LIKE '%$name1%' 
               OR distname LIKE '%$name1%' 
            ORDER BY allpincode ASC";

    $result = mysqli_query($con, $sql);

    if (mysqli_num_rows($result) == 0) {
        // Search in missing_pincodes
        $sql_missing = "SELECT * FROM missing_pincodes 
                        WHERE pincode LIKE '%$name1%' 
                           OR poname LIKE '%$name1%' 
                           OR distname LIKE '%$name1%' 
                        ORDER BY pincode ASC";
        $result_missing = mysqli_query($con, $sql_missing);

        if (mysqli_num_rows($result_missing) == 0) {
            // No result found in both tables
            echo "
            <div style='color:white; background:#222; padding:10px; border-radius:10px;'>
                <h2>❌ ये पिनकोड INDIA / SPEED POST में नहीं है: <span style='color:red;'>$name1</span></h2>
                <h4>यदि किसी PINCODE को CRM में अपडेट कराना है तो STATE, DISTRICT, PINCODE और यदि PINCODE में कोई कोड जैसे:-  (_A,_B,_C, या _E) अपडेट कराना है तो WhatsApp ग्रुप में मैसेज करें । ये जरूर ध्यान रखें कि पोस्ट ऑफिस कभी भी CRM में अपडेट नहीं होता है। POST OFFICE और PINCODE हमेशा ग्राहक के आधार कार्ड से कन्फर्म करें या GOOGLE से पिनकोड और पोस्ट ऑफिस सर्च कर लें, अगर कोई DOUBT हो तो अपने QUALITY MENTOR से कन्फर्म करें और यदि ALL INDIA पिनकोड सर्च पैनल में पोस्टऑफिस को अपडेट कराना है तो व्हाट्सप्प करे, Verify के बाद अपडेट हो जायेगा आगे के लिए ।</h4>
            </div>";
        } else {
            // Result found in missing_pincodes
            echo "
            <table style='width:100%; border-collapse: collapse; background-color:#111; color:white; border: 1px solid #444;'>
                <thead>
                    <tr style='background-color:#222; color:#f9f9f9;'>
                        <th style='padding: 10px; border: 1px solid #444;'>PINCODE</th>
                        <th style='padding: 10px; border: 1px solid #444;'>POST OFFICE NAME</th>
                        <th style='padding: 10px; border: 1px solid #444;'>TALUKA</th>
                        <th style='padding: 10px; border: 1px solid #444;'>DISTRICT</th>
                        <th style='padding: 10px; border: 1px solid #444;'>STATE</th>
                    </tr>
                </thead>
                <tbody>";
            while ($row = mysqli_fetch_assoc($result_missing)) {
                echo "<tr>
                    <td style='padding: 8px; border: 1px solid #444;'>{$row['pincode']}</td>
                    <td style='padding: 8px; border: 1px solid #444;'>{$row['poname']}</td>
                    <td style='padding: 8px; border: 1px solid #444;'>{$row['taluka']}</td>
                    <td style='padding: 8px; border: 1px solid #444;'>{$row['distname']}</td>
                    <td style='padding: 8px; border: 1px solid #444;'>{$row['state']}</td>
                </tr>";
            }
            echo "</tbody></table>";
        }

    } else {
        // Result found in allindia_pincode
        echo "
        <table style='width:100%; border-collapse: collapse; background-color:#111; color:white; border: 1px solid #444;'>
            <thead>
                <tr style='background-color:#222; color:#f9f9f9;'>
                    <th style='padding: 10px; border: 1px solid #444;'>PINCODE</th>
                    <th style='padding: 10px; border: 1px solid #444;'>POST OFFICE NAME</th>
                    <th style='padding: 10px; border: 1px solid #444;'>TALUKA</th>
                    <th style='padding: 10px; border: 1px solid #444;'>DISTRICT</th>
                    <th style='padding: 10px; border: 1px solid #444;'>STATE</th>
                </tr>
            </thead>
            <tbody>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                <td style='padding: 8px; border: 1px solid #444;'>{$row['allpincode']}</td>
                <td style='padding: 8px; border: 1px solid #444;'>{$row['poname']}</td>
                <td style='padding: 8px; border: 1px solid #444;'>{$row['taluka']}</td>
                <td style='padding: 8px; border: 1px solid #444;'>{$row['distname']}</td>
                <td style='padding: 8px; border: 1px solid #444;'>{$row['state']}</td>
            </tr>";
        }
        echo "</tbody></table>";
    }
} else {
    echo "<p style='color:red;'>Please enter a valid PIN code or name.</p>";
}

mysqli_close($con);
?>