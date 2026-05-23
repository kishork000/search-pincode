<?php

include 'db.php';

// Get sanitized inputs
$state = isset($_POST['state']) ? mysqli_real_escape_string($con, trim($_POST['state'])) : '';
$district = isset($_POST['district']) ? mysqli_real_escape_string($con, trim($_POST['district'])) : '';
$searchType = isset($_POST['searchType']) ? mysqli_real_escape_string($con, trim($_POST['searchType'])) : '';
$name1 = isset($_POST['name1']) ? mysqli_real_escape_string($con, trim($_POST['name1'])) : '';

// Build SQL query based on filters
function build_query($con, $table, $state, $district, $searchType, $name1)
{
    $where = [];
    $pincode_field = ($table === 'village') ? 'allpincode' : 'pincode';

    if (!empty($state)) $where[] = "state = '$state'";
    if (!empty($district)) $where[] = "distname = '$district'";

    if (!empty($name1)) {
        switch ($searchType) {
            case 'PIN':
                $where[] = "$pincode_field LIKE '%$name1%'";
                break;
            case 'VL':
                $where[] = "villName LIKE '%$name1%'";
                break;
            case 'DIST':
                $where[] = "distname LIKE '%$name1%'";
                break;
            default:
                $where[] = "($pincode_field LIKE '%$name1%' 
                           OR villName LIKE '%$name1%' 
                           OR distname LIKE '%$name1%')";
        }
    }

    $sql = "SELECT * FROM $table";
    if (!empty($where)) $sql .= " WHERE " . implode(" AND ", $where);
    $sql .= " ORDER BY $pincode_field ASC"; // Limit to 100 results LIMIT 100

    return mysqli_query($con, $sql);
}

// Main search logic
try {
    if (!empty($state) || !empty($district) || !empty($name1)) {
        // Search main table first
        $result = build_query($con, 'village', $state, $district, $searchType, $name1);

        if (mysqli_num_rows($result) > 0) {
            echo "<table style='width:100%; border-collapse: collapse; background-color:#111; color:white; border: 1px solid #444;'>
                    <thead>
                        <tr style='background-color:#222; color:#f9f9f9;'>
                            <th style='padding: 10px; border: 1px solid #444;'>PINCODE</th>
                            <th style='padding: 10px; border: 1px solid #444;'>VILLAGE NAME</th>
                            <th style='padding: 10px; border: 1px solid #444;'>SUB-DISTRICT</th>
                            <th style='padding: 10px; border: 1px solid #444;'>DISTRICT</th>
                            <th style='padding: 10px; border: 1px solid #444;'>STATE</th>
                        </tr>
                    </thead>
                    <tbody>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td style='padding: 8px; border: 1px solid #444;'>" . htmlspecialchars($row['allpincode']) . "</td>
                        <td style='padding: 8px; border: 1px solid #444;'>" . htmlspecialchars($row['villName']) . "</td>
                        <td style='padding: 8px; border: 1px solid #444;'>" . htmlspecialchars($row['taluka']) . "</td>
                        <td style='padding: 8px; border: 1px solid #444;'>" . htmlspecialchars($row['distname']) . "</td>
                        <td style='padding: 8px; border: 1px solid #444;'>" . htmlspecialchars($row['state']) . "</td>
                    </tr>";
            }
            echo "</tbody></table>";
        } else {
            // Search missing_pincodes
            $result_missing = build_query($con, 'missing_pincodes', $state, $district, $searchType, $name1);

            if (mysqli_num_rows($result_missing) > 0) {
                echo "<table style='width:100%; border-collapse: collapse; background-color:#111; color:white; border: 1px solid #444;'>
                        <thead>
                            <tr style='background-color:#222; color:#f9f9f9;'>
                                <th style='padding: 10px; border: 1px solid #444;'>PINCODE</th>
                                <th style='padding: 10px; border: 1px solid #444;'>VILLAGE NAME</th>
                                <th style='padding: 10px; border: 1px solid #444;'>SUB-DISTRICT</th>
                                <th style='padding: 10px; border: 1px solid #444;'>DISTRICT</th>
                                <th style='padding: 10px; border: 1px solid #444;'>STATE</th>
                            </tr>
                        </thead>
                        <tbody>";
                while ($row = mysqli_fetch_assoc($result_missing)) {
                    echo "<tr>
                            <td style='padding: 8px; border: 1px solid #444;'>" . htmlspecialchars($row['pincode']) . "</td>
                            <td style='padding: 8px; border: 1px solid #444;'>" . htmlspecialchars($row['villName']) . "</td>
                            <td style='padding: 8px; border: 1px solid #444;'>" . htmlspecialchars($row['taluka']) . "</td>
                            <td style='padding: 8px; border: 1px solid #444;'>" . htmlspecialchars($row['distname']) . "</td>
                            <td style='padding: 8px; border: 1px solid #444;'>" . htmlspecialchars($row['state']) . "</td>
                        </tr>";
                }
                echo "</tbody></table>";
            } else {
                // Preserve original error message
                echo "<div style='color:white; background:#222; padding:10px; border-radius:10px;'>
                        <h2>❌ ये पिनकोड INDIA / SPEED POST में नहीं है: <span style='color:red;'>$name1</span></h2>
                        <h4>यदि किसी PINCODE को CRM में अपडेट कराना है तो STATE, DISTRICT, PINCODE और यदि PINCODE में कोई कोड जैसे:-  (_A,_B,_C, या _E) अपडेट कराना है तो WhatsApp ग्रुप में मैसेज करें । ये जरूर ध्यान रखें कि पोस्ट ऑफिस कभी भी CRM में अपडेट नहीं होता है। POST OFFICE और PINCODE हमेशा ग्राहक के आधार कार्ड से कन्फर्म करें या GOOGLE से पिनकोड और पोस्ट ऑफिस सर्च कर लें, अगर कोई DOUBT हो तो अपने QUALITY MENTOR से कन्फर्म करें और यदि ALL INDIA पिनकोड सर्च पैनल में पोस्टऑफिस को अपडेट कराना है तो व्हाट्सप्प करे, Verify के बाद अपडेट हो जायेगा आगे के लिए ।</h4>
                    </div>";
            }
        }
    } else {
        echo "<p style='color:red;'>Please enter a valid search criteria.</p>";
    }
} catch (Exception $e) {
    error_log($e->getMessage()); // Log error for admin
    echo "<div style='color:red;'>An unexpected error occurred. Please try again later.</div>";
}

//Validate PIN code is numeric if provided
if (!empty($name1) && $searchType === 'PIN' && !ctype_digit($name1)) {
    echo "<div style='color:red;'>Invalid PIN code format.</div>";
    exit;
}

mysqli_close($con);
?>
