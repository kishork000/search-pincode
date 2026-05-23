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

if (isset($_POST['name']) && !empty(trim($_POST['name']))) {
    $name = mysqli_real_escape_string($con, trim($_POST['name']));

    $sql = "SELECT * FROM pincode_list WHERE pincode LIKE '%$name%'";
    $result = mysqli_query($con, $sql);

    if (mysqli_num_rows($result) == 0) {
        echo "<h2 style='color:white;'>Courier companies are not providing delivery services at this PIN code: <span style='color:red;'>$name</span></h2>";
    } else {
        echo "
        <table style='width:100%; border-collapse: collapse; background-color:#111; color:white; border: 1px solid #444;'>
            <thead>
                <tr style='background-color:#222; color:#f9f9f9;'>
                    <th style='padding: 10px; border: 1px solid #444;'>PINCODE</th>
                    <th style='padding: 10px; border: 1px solid #444;'>COURIER COMPANY</th>
                    <th style='padding: 10px; border: 1px solid #444;'>DELIVERY %</th>
                    <th style='padding: 10px; border: 1px solid #444;'>DISPATCH CENTER</th>
                    <th style='padding: 10px; border: 1px solid #444;'>CITY/STATE</th>
                    <th style='padding: 10px; border: 1px solid #444;'>BOOKING DISTANCE</th>
                    <th style='padding: 10px; border: 1px solid #444;'>DELIVERY TIME</th>
                    <th style='padding: 10px; border: 1px solid #444;'>SERVICEABILITY</th>
                </tr>
            </thead>
            <tbody>";
        while ($row = mysqli_fetch_assoc($result)) {
            $pcode = htmlspecialchars($row['pincode'], ENT_QUOTES);
            $cc_raw = $row['courier_company'];
            $cc = htmlspecialchars($cc_raw, ENT_QUOTES);

            // Determine if this row is Home Delivery / HD. If so show a disabled 'HD' indicator.
            $isHD = false;
            if (!empty($cc_raw)) {
                $low = strtolower($cc_raw);
                if (strpos($low, 'home') !== false && strpos($low, 'deliv') !== false) {
                    $isHD = true;
                }
                // match standalone 'hd' token (case-insensitive)
                if (!$isHD && preg_match('/\bhd\b/i', $cc_raw)) {
                    $isHD = true;
                }
            }

            // Prepare serviceability cell: disabled HD button or active check button
            if ($isHD) {
                $serviceCell = "<button class='service-check btn-small' disabled style='padding:4px 6px;border-radius:6px;min-width:54px;background-color:#666;color:#ccc;'>HD</button>";
            } else {
                $serviceCell = "<button class='service-check btn-small' data-pincode='{$pcode}' data-courier='{$cc}' style='padding:4px 6px;border-radius:6px;min-width:54px;cursor:pointer;background-color:#1da1f2;'>Check</button>";
            }

            echo "<tr>
                    <td style='padding: 8px; border: 1px solid #444;'>{$row['pincode']}</td>
                    <td style='padding: 8px; border: 1px solid #444;'>{$row['courier_company']}</td>
                    <td style='padding: 8px; border: 1px solid #444;'>{$row['deliveryPercent']}</td>
                    <td style='padding: 8px; border: 1px solid #444;'>{$row['city_name']}</td>
                    <td style='padding: 8px; border: 1px solid #444;'>{$row['state_name']}</td>
                    <td style='padding: 8px; border: 1px solid #444;'>{$row['cod']}</td>
                    <td style='padding: 8px; border: 1px solid #444;'>{$row['delivery_time']}</td>
                    <td style='padding: 8px; border: 1px solid #444;'>{$serviceCell}</td>
                </tr>";
        }
        echo "</tbody></table>";
        echo "
        <style>
        .service-result {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.9rem;
        }
        .service-result.available {
            background-color: #28a745;
            color: white;
        }
        .service-result.unavailable {
            background-color: #dc3545;
            color: white;
        }
        .service-result.error {
            background-color: #fd7e14;
            color: white;
        }
        .recheck-link {
            color: #bcd;
            font-size: 0.85rem;
            margin-left: 6px;
            cursor: pointer;
            text-decoration: none;
        }
        .recheck-link:hover {
            text-decoration: underline;
        }
        .spinner {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 0.6s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        </style>
        <script>
        // Same JavaScript logic as index.php for handling embargo and remarks
        (function() {
            var cache = {};
            var recheckCount = {};
            var CACHE_EXPIRY_MS = 7 * 60 * 1000;

            function isHomeDeliveryName(name) {
                if (!name) return false;
                var s = String(name).toLowerCase();
                return s.indexOf('home') !== -1 && s.indexOf('deliv') !== -1;
            }

            function renderResultElement(key, result) {
                var cls = 'service-result';
                var text = 'Unknown';
                var isAvailable = result && result.is_available;
                var remarks = result && result.remarks;
                var embargoed = remarks && remarks.toLowerCase && remarks.toLowerCase().includes('embargo');
                
                // Embargo takes priority: if remarks contain 'Embargo', mark as unavailable
                if (embargoed) {
                    cls += ' unavailable';
                    text = 'Not available (Embargo)';
                } else if (result && result.is_available !== undefined) {
                    cls += (result.is_available == '1') ? ' available' : ' unavailable';
                    text = (result.is_available == '1') ? 'Available' : 'Not available';
                    if (remarks && remarks !== '-') {
                        text += ' - ' + remarks;
                    }
                } else if (result && result.error) {
                    cls += ' error';
                    text = 'Error: ' + result.error;
                }

                var count = recheckCount[key] || 0;
                var linkHtml = '';
                if (count < 2) {
                    linkHtml = ' <a href=\"#\" class=\"recheck-link\" data-key=\"' + key.replace(/\"/g, '&quot;') + '\" style=\"color:#bcd; font-size:0.85rem; margin-left:6px;\">Re-check</a>';
                } else {
                    linkHtml = ' <span style=\"color:#888; font-size:0.85rem; margin-left:6px;\">Re-check limit reached</span>';
                }

                var html = '<span class=\"' + cls + '\">' + text + '</span>' + linkHtml;
                return html;
            }

            function isCachedValid(key) {
                if (!cache[key]) return false;
                var entry = cache[key];
                if (!entry.ts) return false;
                return (Date.now() - entry.ts) < CACHE_EXPIRY_MS;
            }

            document.addEventListener('click', function(e) {
                if (!e.target.classList.contains('service-check')) return;
                e.preventDefault();
                
                var btn = e.target;
                var pincode = String(btn.dataset.pincode || '').trim();
                var courierRaw = String(btn.dataset.courier || '').trim();
                var courierRawLower = courierRaw.toLowerCase();

                if (isHomeDeliveryName(courierRaw)) {
                    btn.replaceWith(createSpan('service-result', 'HD'));
                    return;
                }

                var courier = 'shiprocket';
                if (courierRawLower.indexOf('delhiv') !== -1 || courierRawLower.indexOf('delhivery') !== -1) courier = 'delhivery';
                else if (courierRawLower.indexOf('shiprocket') !== -1) courier = 'shiprocket';
                else {
                    btn.replaceWith(createSpan('service-result error', 'No Live API'));
                    return;
                }

                var key = courier + '|' + pincode;

                if (isCachedValid(key)) {
                    btn.replaceWith(renderResultElement(key, cache[key].data));
                    return;
                }

                btn.classList.add('btn-small');
                btn.innerHTML = '<span class=\"spinner\"></span>';
                btn.disabled = true;

                var timeoutId = setTimeout(function() {
                    cache[key] = { data: { error: 'Request timeout, try later' }, ts: Date.now() };
                    btn.replaceWith(renderResultElement(key, cache[key].data));
                }, 4000);

                fetch('pincode-serviciblity.php?pincode=' + encodeURIComponent(pincode) + '&courier=' + encodeURIComponent(courier), {
                    method: 'GET'
                })
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    clearTimeout(timeoutId);
                    cache[key] = { data: data, ts: Date.now() };
                    btn.replaceWith(renderResultElement(key, data));
                })
                .catch(function(error) {
                    clearTimeout(timeoutId);
                    var msg = 'Error';
                    cache[key] = { data: { error: msg }, ts: Date.now() };
                    btn.replaceWith(renderResultElement(key, cache[key].data));
                });
            });

            document.addEventListener('click', function(e) {
                if (!e.target.classList.contains('recheck-link')) return;
                e.preventDefault();
                
                var key = e.target.dataset.key;
                if (!key) return;

                recheckCount[key] = (recheckCount[key] || 0) + 1;
                delete cache[key];

                var btn = e.target.closest('td').querySelector('button');
                if (!btn) {
                    var newBtn = document.createElement('button');
                    newBtn.className = 'service-check btn-small';
                    newBtn.dataset.pincode = key.split('|')[1];
                    var parts = key.split('|');
                    newBtn.dataset.courier = parts[0] === 'delhivery' ? 'delhivery' : 'shiprocket';
                    newBtn.innerHTML = 'Check';
                    e.target.replaceWith(newBtn);
                } else {
                    btn.click();
                }
            });

            function createSpan(className, text) {
                var span = document.createElement('span');
                span.className = className;
                span.textContent = text;
                return span;
            }
        })();
        </script>";
    }
} else {
    echo "<p style='color:red;'>Please enter a valid PIN code.</p>";
}

mysqli_close($con);
