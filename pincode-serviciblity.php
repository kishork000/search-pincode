<?php
// pincode-serviciblity.php
// Supports two modes:
// 1) Single pincode JSON check via GET?pincode=XXXXX&courier=shiprocket|delhivery
// 2) Existing batch/CSV/delete logic when run without pincode parameter (keeps original behaviour)

// Decide response type
$isDownload = isset($_GET['download']) && $_GET['download'] === '1';
header('Content-Type: ' . ($isDownload ? 'text/csv' : 'application/json') . '; charset=utf-8');

// Simple JSON helper
function json_exit($data, $http = 200)
{
    http_response_code($http);
    echo json_encode($data);
    if (is_resource($GLOBALS['connection'] ?? null)) {
        @$GLOBALS['connection']->close();
    }
    exit;
}

// --- Database config (edit these credentials in a secure manner in production) ---
$localConfig = array(
    "host" => "localhost",
    "dbusername" => "codpin",
    "dbpassword" => "passw0rd@098",
    "dbname" => "indianpincode",
    // Optional: provide Delhivery configuration here if available
    "delhivery" => [
        // Public pin-codes endpoint (provided)
        'serviceability_url' => 'https://track.delhivery.com/c/api/pin-codes/json/',
        // Token provided by user
        'token' => '9762182dbe3ed8b1cffe4a178eb1539b5037cf43',
        // Use default auth type 'token' which maps to header 'Authorization: Token <token>'
        'auth_type' => 'token'
    ]
);

// Connect to DB
$connection = mysqli_connect($localConfig["host"], $localConfig["dbusername"], $localConfig["dbpassword"], $localConfig["dbname"]);
if (!$connection) {
    if ($isDownload) die("Connection failed: " . mysqli_connect_error());
    json_exit(['error' => 'DB connection failed', 'details' => mysqli_connect_error()], 500);
}

// Read courier param (default: shiprocket)
$courier = isset($_GET['courier']) ? strtolower(trim($_GET['courier'])) : 'shiprocket';
$allowedCouriers = ['shiprocket', 'delhivery'];
if (!in_array($courier, $allowedCouriers)) {
    $courier = 'shiprocket';
}

// If a single pincode is requested, handle and return JSON (courier-aware)
if (isset($_GET['pincode'])) {
    $rawPin = $_GET['pincode'];
    $pincode = preg_replace('/[^0-9]/', '', $rawPin);
    if (!preg_match('/^\d{6}$/', $pincode)) {
        json_exit(['error' => 'Invalid pincode format'], 400);
    }

    // Find the pincode in DB for the selected courier
    // Match exact pincode or DB entries that have suffixes like _C/_B (e.g. 140119_C)
    $stmt = $connection->prepare("SELECT * FROM pincode_list WHERE courier_company = ? AND (pincode = ? OR pincode LIKE CONCAT(?, '_%')) LIMIT 1");
    if (!$stmt) {
        json_exit(['error' => 'Prepare failed', 'details' => $connection->error], 500);
    }
    $stmt->bind_param('sss', $courier, $pincode, $pincode);
    if (!$stmt->execute()) {
        $stmt->close();
        json_exit(['error' => 'Query execute failed', 'details' => $stmt->error], 500);
    }
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // If DB row not found for the selected courier, try a fallback search by pincode only
    // (handles cases where courier value in DB differs in casing or naming)
    $usedCourierFromDb = null;
    if (!$row) {
        // Fallback: search by pincode only, allowing DB entries with suffixes (140119_C)
        $stmt2 = $connection->prepare("SELECT * FROM pincode_list WHERE (pincode = ? OR pincode LIKE CONCAT(?, '_%')) LIMIT 1");
        if ($stmt2) {
            $stmt2->bind_param('ss', $pincode, $pincode);
            if ($stmt2->execute()) {
                $tmp = $stmt2->get_result()->fetch_assoc();
                if ($tmp) {
                    $row = $tmp;
                    $usedCourierFromDb = $row['courier_company'] ?? null;
                }
            }
            $stmt2->close();
        }
    }

    // If DB row still not found, fall back to a live check (useful for ad-hoc single-pin queries)
    // Do NOT alter Shiprocket or Delhivery adapter functions; we just allow calling them
    if (!$row) {
        $response = [
            'pincode' => $pincode,
            'courier_company' => $courier,
            'city_name' => null,
            'state_name' => null,
            'cod' => null,
            'delivery_time' => null,
            'deliveryPercent' => null,
        ];
    } else {
        // Prepare base response using DB fields. Use the exact DB pincode value (may include _C/_B suffix)
        $response = [
            'pincode' => $row['pincode'] ?? $pincode,
            'courier_company' => $usedCourierFromDb ?? $courier,
            'city_name' => $row['city_name'] ?? null,
            'state_name' => $row['state_name'] ?? null,
            'cod' => $row['cod'] ?? null,
            'delivery_time' => $row['delivery_time'] ?? null,
            'deliveryPercent' => $row['deliveryPercent'] ?? null,
        ];
    }

    // Call courier-specific check
    $check = null;
    if ($courier === 'shiprocket') {
        $check = check_shiprocket($pincode);
    } else {
        // Delhivery adapter: requires configuration in $localConfig['delhivery']
        if (empty($localConfig['delhivery']['serviceability_url']) || empty($localConfig['delhivery']['token'])) {
            json_exit(['error' => 'Delhivery not configured on server. Add settings to $localConfig[\'delhivery\'] in the PHP file.'], 500);
        }
        $check = check_delhivery($pincode, $localConfig['delhivery']);
    }

    if (isset($check['error'])) {
        // Return error but include DB info for debugging
        $response['is_available'] = null;
        $response['service_error'] = $check['error'];
        json_exit($response, 502);
    }
    // Merge useful fields from API raw response into the response only when DB row was missing
    $raw = $check['raw'] ?? null;
    if (!$row) {
        // helper: try to extract first matching key from raw (array/object)
        $extract = function ($raw, $candidates) {
            if ($raw === null) return null;
            if (is_string($raw)) {
                $maybe = json_decode($raw, true);
                if (json_last_error() === JSON_ERROR_NONE) $raw = $maybe;
            }
            if (!is_array($raw)) return null;
            foreach ($candidates as $k) {
                if (array_key_exists($k, $raw) && $raw[$k] !== null && $raw[$k] !== '') return $raw[$k];
            }
            // search nested arrays (shallow)
            foreach ($raw as $entry) {
                if (is_array($entry)) {
                    foreach ($candidates as $k) {
                        if (array_key_exists($k, $entry) && $entry[$k] !== null && $entry[$k] !== '') return $entry[$k];
                    }
                }
            }
            return null;
        };

        // If DB row was missing, try to populate from raw
        if (empty($response['city_name'])) {
            $city = $extract($raw, ['city_name', 'city', 'cityName', 'place']);
            if ($city) $response['city_name'] = $city;
        }
        if (empty($response['state_name'])) {
            $state = $extract($raw, ['state_name', 'state', 'stateName', 'region']);
            if ($state) $response['state_name'] = $state;
        }
        if (empty($response['cod'])) {
            $codVal = $extract($raw, ['cod', 'cod_available', 'cash_on_delivery', 'codAvailable']);
            if ($codVal !== null) {
                // normalize common boolean/strings to '1' or '0'
                if (is_bool($codVal)) $response['cod'] = $codVal ? '1' : '0';
                elseif (is_numeric($codVal)) $response['cod'] = ((int)$codVal) ? '1' : '0';
                else $response['cod'] = (stripos((string)$codVal, 'y') !== false || stripos((string)$codVal, 't') !== false) ? '1' : '0';
            }
        }
        if (empty($response['delivery_time'])) {
            $dt = $extract($raw, ['delivery_time', 'transit', 'transit_time', 'delivery_estimate', 'eta']);
            if ($dt) $response['delivery_time'] = $dt;
        }
    }

    $response['is_available'] = $check['is_available'];
    $response['service_raw'] = $raw;
    // also include 'raw' for consistency with batch responses
    $response['raw'] = $raw;

    // Extract remarks from raw response if available
    if ($raw !== null) {
        $remarks = null;
        if (is_string($raw)) {
            $rawParsed = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($rawParsed)) {
                $raw = $rawParsed;
            }
        }
        if (is_array($raw)) {
            // For Delhivery: delivery_codes[0].postal_code.remarks
            if (isset($raw['delivery_codes']) && is_array($raw['delivery_codes']) && count($raw['delivery_codes']) > 0) {
                $dc = $raw['delivery_codes'][0];
                if (is_array($dc) && isset($dc['postal_code']) && is_array($dc['postal_code'])) {
                    $remarks = $dc['postal_code']['remarks'] ?? null;
                }
            }
            // For Shiprocket or other formats: direct remarks field
            if ($remarks === null && isset($raw['remarks'])) {
                $remarks = $raw['remarks'];
            }
        }
        if ($remarks !== null) {
            $response['remarks'] = $remarks;
        }
    }

    json_exit($response, 200);
}

// --- New behavior: provide scan/export/delete endpoints and do NOT delete automatically ---

// Helper: run batch check for given courier + page, returns array of results keyed by pincode
function run_batch_check($connection, $courier, $offset, $rowcount)
{
    global $localConfig;
    $stmt = $connection->prepare("SELECT * FROM pincode_list WHERE courier_company = ? LIMIT ?, ?");
    if (!$stmt) {
        return ['error' => 'Prepare failed: ' . $connection->error];
    }
    $stmt->bind_param("sii", $courier, $offset, $rowcount);
    if (!$stmt->execute()) {
        $stmt->close();
        return ['error' => 'Execute failed: ' . $stmt->error];
    }
    $result = $stmt->get_result();
    $pinCodes = [];
    $tResult = [];
    while ($row = $result->fetch_assoc()) {
        $tResult[$row["pincode"]] = array(
            "pincode" => $row["pincode"],
            "courier_company" => $row["courier_company"],
            "deliveryPercent" => $row["deliveryPercent"],
            "city_name" => $row["city_name"],
            "state_name" => $row["state_name"],
            "cod" => $row["cod"],
            "delivery_time" => $row["delivery_time"]
        );
        $pinCodes[] = $row["pincode"];
    }
    $stmt->close();

    // If no pins, return empty
    if (count($pinCodes) === 0) return ['tResult' => $tResult, 'fResult' => []];

    // If courier is Delhivery, call the Delhivery adapter per-pin so auth errors surface correctly
    if ($courier === 'delhivery') {
        $batchSize = 20;
        $batches = array_chunk($pinCodes, $batchSize);
        foreach ($batches as $batch) {
            foreach ($batch as $pin) {
                if (!preg_match('/^\d{6}$/', $pin)) {
                    $tpin = preg_replace('/[^0-9]/', '', $pin);
                    if (!preg_match('/^\d{6}$/', $tpin)) {
                        $tResult[$pin]["is_available"] = "0";
                        $tResult[$pin]["service_error"] = 'Invalid numeric pincode after cleaning';
                        continue;
                    }
                } else {
                    $tpin = $pin;
                }

                $check = check_delhivery($tpin, $localConfig['delhivery']);
                if (isset($check['error'])) {
                    // Surface auth or other errors from Delhivery
                    $tResult[$pin]["is_available"] = "0";
                    $tResult[$pin]["service_error"] = $check['error'];
                    if (isset($check['raw'])) $tResult[$pin]["raw"] = $check['raw'];
                } else {
                    $tResult[$pin]["is_available"] = $check['is_available'];
                    if (isset($check['raw'])) $tResult[$pin]["raw"] = $check['raw'];
                }
            }
            usleep(100000);
        }

        $fResult = [];
        foreach ($tResult as $pin => $row) {
            if (isset($row['is_available']) && $row['is_available'] == '0') {
                $fResult[$pin] = $row;
            }
        }

        return ['tResult' => $tResult, 'fResult' => $fResult];
    }

    // For Shiprocket: perform per-pin checks using the existing check_shiprocket() adapter
    // Use the DB pincode as display value but send only numeric 6-digit code to the API
    foreach ($pinCodes as $pin) {
        // clean numeric part for API check but keep original for display
        $tpin = preg_replace('/[^0-9]/', '', $pin);
        if (!preg_match('/^\d{6}$/', $tpin)) {
            $tResult[$pin]["is_available"] = "0";
            $tResult[$pin]["service_error"] = 'Invalid numeric pincode after cleaning';
            continue;
        }

        $check = check_shiprocket($tpin);
        if (isset($check['error'])) {
            $tResult[$pin]["is_available"] = "0";
            $tResult[$pin]["service_error"] = $check['error'];
            if (isset($check['raw'])) $tResult[$pin]['raw'] = $check['raw'];
        } else {
            $tResult[$pin]["is_available"] = $check['is_available'];
            if (isset($check['raw'])) $tResult[$pin]['raw'] = $check['raw'];
        }

        // small delay to avoid throttling
        usleep(50000);
    }

    $fResult = [];
    foreach ($tResult as $pin => $row) {
        if (isset($row['is_available']) && $row['is_available'] == '0') {
            $fResult[$pin] = $row;
        }
    }

    return ['tResult' => $tResult, 'fResult' => $fResult];
}

// Handle scan (returns JSON list of checked pins and statuses)
if (isset($_GET['scan']) && $_GET['scan'] == '1') {
    $courier = isset($_GET['courier']) ? $_GET['courier'] : 'shiprocket';

    // Validate courier has API configured for couriers that require config
    if ($courier === 'delhivery' && (empty($localConfig['delhivery']['serviceability_url']) || empty($localConfig['delhivery']['token']))) {
        json_exit(['error' => 'No API configured for courier: ' . $courier], 400);
    }

    // Simple count endpoint to help frontend progress calculations
    if (isset($_GET['count']) && $_GET['count'] == '1') {
        $stmt = $connection->prepare("SELECT COUNT(*) as cnt FROM pincode_list WHERE courier_company = ?");
        if (!$stmt) json_exit(['error' => 'DB prepare failed: ' . $connection->error], 500);
        $stmt->bind_param('s', $courier);
        if (!$stmt->execute()) json_exit(['error' => 'DB execute failed: ' . $stmt->error], 500);
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        json_exit(['count' => intval($res['cnt'])], 200);
    }

    // If a specific list of pins is provided, check only those (preserve original DB pincode values)
    if (isset($_GET['pins']) && trim($_GET['pins']) !== '') {
        $rawPins = array_map('trim', explode(',', $_GET['pins']));
        if (count($rawPins) === 0) json_exit(['error' => 'No pins provided'], 400);

        // Query DB for exact matches (we do not modify DB values here)
        $placeholders = implode(',', array_fill(0, count($rawPins), '?'));
        $types = str_repeat('s', count($rawPins));
        $sql = "SELECT * FROM pincode_list WHERE pincode IN ($placeholders)";
        $stmt = $connection->prepare($sql);
        if (!$stmt) json_exit(['error' => 'DB prepare failed: ' . $connection->error], 500);
        $stmt->bind_param($types, ...$rawPins);
        if (!$stmt->execute()) json_exit(['error' => 'DB execute failed: ' . $stmt->error], 500);
        $result = $stmt->get_result();
        $pins = [];
        while ($row = $result->fetch_assoc()) {
            $pins[$row['pincode']] = $row;
        }
        $stmt->close();

        if (empty($pins)) {
            json_exit(['error' => 'No matching pincodes found for provided list'], 404);
        }

        $results = [];
        $unserviceable = [];
        foreach ($pins as $origPin => $row) {
            // Clean numeric part for API check but keep original for display and export
            $cleanPin = preg_replace('/[^0-9]/', '', $origPin);
            if (!preg_match('/^\d{6}$/', $cleanPin)) {
                // Mark as unavailable if cleaned pin is invalid, but don't alter DB
                $row['is_available'] = '0';
                $row['service_error'] = 'Invalid numeric pincode after cleaning';
                $results[$origPin] = $row;
                $unserviceable[$origPin] = $row;
                continue;
            }

            if ($courier === 'shiprocket') {
                $check = check_shiprocket($cleanPin);
            } else {
                $check = check_delhivery($cleanPin, $localConfig['delhivery']);
            }

            if (isset($check['error'])) {
                $row['is_available'] = '0';
                $row['service_error'] = $check['error'];
            } else {
                $row['is_available'] = $check['is_available'];
            }
            $row['checked_with'] = $cleanPin;
            $results[$origPin] = $row;
            if ($row['is_available'] === '0') $unserviceable[$origPin] = $row;
            // small delay to avoid throttling
            usleep(50000);
        }

        json_exit([
            'tResult' => $results,
            'fResult' => $unserviceable,
            'summary' => [
                'total' => count($results),
                'serviceable' => count($results) - count($unserviceable),
                'unserviceable' => count($unserviceable)
            ]
        ], 200);
    }

    // If page parameters are provided, use the existing paged batch checker so frontend can iterate pages
    if (isset($_GET['pg']) || isset($_GET['rowcount'])) {
        $pg = isset($_GET['pg']) ? filter_var($_GET['pg'], FILTER_VALIDATE_INT) : 0;
        $pg = ($pg !== false && $pg >= 0) ? $pg : 0;
        $rowcount = isset($_GET['rowcount']) ? intval($_GET['rowcount']) : 100;
        $offset = $pg * $rowcount;
        $res = run_batch_check($connection, $courier, $offset, $rowcount);
        if (isset($res['error'])) json_exit(['error' => $res['error']], 500);
        json_exit(['tResult' => $res['tResult'], 'fResult' => $res['fResult'], 'meta' => ['pg' => $pg, 'rowcount' => $rowcount]], 200);
    }

    // Default: fallback to checking all pincodes (kept for compatibility) but recommend using paged mode
    $stmt = $connection->prepare("SELECT * FROM pincode_list WHERE courier_company = ?");
    if (!$stmt) json_exit(['error' => 'DB prepare failed: ' . $connection->error], 500);
    $stmt->bind_param('s', $courier);
    if (!$stmt->execute()) json_exit(['error' => 'DB execute failed: ' . $stmt->error], 500);
    $result = $stmt->get_result();
    $pins = [];
    while ($row = $result->fetch_assoc()) {
        $pins[$row['pincode']] = $row;
    }
    $stmt->close();

    if (empty($pins)) {
        json_exit(['error' => 'No pincodes found for courier: ' . $courier], 404);
    }

    $results = [];
    $unserviceable = [];
    $batch_size = 10;
    $chunks = array_chunk(array_keys($pins), $batch_size);
    foreach ($chunks as $chunk) {
        foreach ($chunk as $pin) {
            // clean and keep original
            $cleanPin = preg_replace('/[^0-9]/', '', $pin);
            if (!preg_match('/^\d{6}$/', $cleanPin)) {
                $row = $pins[$pin];
                $row['is_available'] = '0';
                $row['service_error'] = 'Invalid numeric pincode after cleaning';
                $results[$pin] = $row;
                $unserviceable[$pin] = $row;
                continue;
            }

            if ($courier === 'shiprocket') {
                $check = check_shiprocket($cleanPin);
            } else {
                $check = check_delhivery($cleanPin, $localConfig['delhivery']);
            }

            $row = $pins[$pin];
            if (isset($check['error'])) {
                $row['is_available'] = '0';
                $row['service_error'] = $check['error'];
            } else {
                $row['is_available'] = $check['is_available'];
            }
            $row['checked_with'] = $cleanPin;
            $results[$pin] = $row;
            if ($row['is_available'] === '0') $unserviceable[$pin] = $row;
        }
        usleep(100000);
    }

    json_exit([
        'tResult' => $results,
        'fResult' => $unserviceable,
        'summary' => [
            'total' => count($results),
            'serviceable' => count($results) - count($unserviceable),
            'unserviceable' => count($unserviceable)
        ]
    ], 200);
}

// Handle export: create temporary CSV for given pins and return download token & url
if (isset($_GET['export']) && $_GET['export'] == '1' && isset($_GET['pins'])) {
    // Accept raw DB pincode values (may contain suffixes like _C) and use exact match
    $pins = array_map('trim', explode(',', $_GET['pins']));
    // sanitize allowed characters (digits, letters, underscore, hyphen)
    $pins = array_map(function ($p) {
        return preg_replace('/[^0-9A-Za-z_\-]/', '', $p);
    }, $pins);
    $pins = array_filter($pins, function ($p) {
        return $p !== '';
    });
    if (count($pins) == 0) json_exit(['error' => 'No valid pins provided'], 400);

    // Fetch details for these pins from DB using exact match
    $in = implode(',', array_fill(0, count($pins), '?'));
    $types = str_repeat('s', count($pins));
    $sql = "SELECT * FROM pincode_list WHERE pincode IN ($in)";
    $stmt = $connection->prepare($sql);
    if (!$stmt) json_exit(['error' => 'Prepare failed: ' . $connection->error], 500);
    $stmt->bind_param($types, ...$pins);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = [];
    while ($r = $result->fetch_assoc()) $rows[] = $r;
    $stmt->close();

    // Create temporary CSV file
    $token = bin2hex(random_bytes(8));
    $tmpDir = sys_get_temp_dir();
    $filename = "$tmpDir/pincode_export_{$token}.csv";
    $fh = fopen($filename, 'w');
    if ($fh === false) json_exit(['error' => 'Failed to create temp file'], 500);
    $head = ["pincode", "courier_company", "deliveryPercent", "city_name", "state_name", "cod", "delivery_time"];
    fputcsv($fh, $head);
    foreach ($rows as $r) fputcsv($fh, [$r['pincode'], $r['courier_company'], $r['deliveryPercent'], $r['city_name'], $r['state_name'], $r['cod'], $r['delivery_time']]);
    fclose($fh);

    // Return token and download url
    $downloadUrl = basename(__FILE__) . "?download_file=" . $token;
    json_exit(['token' => $token, 'download_url' => $downloadUrl], 200);
}

// Serve temporary CSV when requested
if (isset($_GET['download_file'])) {
    $token = preg_replace('/[^a-f0-9]/', '', $_GET['download_file']);
    $tmpDir = sys_get_temp_dir();
    $filename = "$tmpDir/pincode_export_{$token}.csv";
    if (!file_exists($filename)) {
        header('HTTP/1.1 404 Not Found');
        echo 'Not found';
        exit;
    }
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="pincode_export_' . $token . '.csv"');
    readfile($filename);
    exit;
}

// Handle delete via POST: requires token and pins; token must have corresponding temp CSV file
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'delete') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input) || empty($input['token']) || empty($input['pins'])) json_exit(['error' => 'Invalid request'], 400);
    $token = preg_replace('/[^a-f0-9]/', '', $input['token']);
    $pins = $input['pins'];
    if (!is_array($pins) || count($pins) === 0) json_exit(['error' => 'No pins provided'], 400);
    $tmpDir = sys_get_temp_dir();
    $filename = "$tmpDir/pincode_export_{$token}.csv";
    if (!file_exists($filename)) json_exit(['error' => 'Export not found or not generated'], 400);

    // Proceed to delete pins (prepared statement)
    $stmt = $connection->prepare("DELETE FROM pincode_list WHERE pincode = ?");
    if (!$stmt) json_exit(['error' => 'Prepare failed: ' . $connection->error], 500);
    $deleted = [];
    foreach ($pins as $p) {
        // sanitize but preserve suffixes (allow letters, digits, underscore, hyphen)
        $p2 = preg_replace('/[^0-9A-Za-z_\-]/', '', $p);
        if ($p2 === '') continue;
        $stmt->bind_param('s', $p2);
        if ($stmt->execute()) $deleted[] = $p2;
    }
    $stmt->close();

    // Optionally remove the temp CSV after successful delete
    @unlink($filename);
    json_exit(['deleted' => $deleted], 200);
}

// If we reach here without handling, return a helpful message
$connection->close();
json_exit(['message' => 'No action requested. Use ?pincode=..., ?scan=1, ?export=1&pins=..., or POST?action=delete'], 400);

// --- Helper functions ---
function check_shiprocket($pincode)
{
    $token_url = "https://apiv2.shiprocket.in/v1/external/auth/login";
    $serviceURL = "https://apiv2.shiprocket.in/v1/external/courier/serviceability/";
    $token_data = array("email" => "kishor@sanyasiayurveda.com", "password" => '8lbm@g6c9Xmz^58Y');

    // Token cache to avoid repeated auth calls which may trigger 403 or rate limits.
    $tmpDir = sys_get_temp_dir();
    $cacheFile = $tmpDir . DIRECTORY_SEPARATOR . 'shiprocket_token.json';
    $token = null;
    $now = time();
    if (file_exists($cacheFile)) {
        $c = @json_decode(@file_get_contents($cacheFile), true);
        if (isset($c['token']) && isset($c['expires_at']) && $c['expires_at'] > $now) {
            $token = $c['token'];
        }
    }

    // Fetch new token if none cached
    if (!$token) {
        $ch = curl_init();
        if ($ch === false) return ['error' => 'Failed to initialize curl (token)'];
        curl_setopt_array($ch, array(
            CURLOPT_URL => $token_url,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => json_encode($token_data),
            CURLOPT_TIMEOUT => 15
        ));
        $res = curl_exec($ch);
        if ($res === false) {
            $err = curl_error($ch);
            curl_close($ch);
            return ['error' => 'Curl token error: ' . $err];
        }
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($http !== 200) {
            // include body when possible
            $body = substr($res ?? '', 0, 2000);
            return ['error' => 'Auth HTTP code ' . $http, 'raw' => $body];
        }
        $j = json_decode($res, true);
        if (json_last_error() !== JSON_ERROR_NONE) return ['error' => 'Token JSON parse error: ' . json_last_error_msg(), 'raw' => substr($res, 0, 2000)];
        if (!isset($j['token'])) return ['error' => 'Token missing in auth response', 'raw' => $res];
        $token = $j['token'];
        // Cache token for 5 minutes by default (reduce auth calls)
        $cacheData = ['token' => $token, 'expires_at' => $now + 300];
        @file_put_contents($cacheFile, json_encode($cacheData));
    }

    // Call serviceability endpoint
    $ch2 = curl_init();
    if ($ch2 === false) return ['error' => 'Failed to init curl (service)'];
    $headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $token, 'User-Agent: PincodeChecker/1.0');
    curl_setopt_array($ch2, array(
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 20
    ));
    $query = http_build_query(["pickup_postcode" => "110020", "delivery_postcode" => $pincode, "cod" => "1", "weight" => "0.10"]);
    curl_setopt($ch2, CURLOPT_URL, $serviceURL . '?' . $query);
    $sres = curl_exec($ch2);
    if ($sres === false) {
        $err = curl_error($ch2);
        curl_close($ch2);
        return ['error' => 'Curl service error: ' . $err];
    }
    $http = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
    curl_close($ch2);
    if ($http === 401 || $http === 403) {
        // Invalidate cache if auth failed so next request tries to refresh token
        @unlink($cacheFile);
        return ['error' => 'Service auth failed (HTTP ' . $http . ')', 'raw' => substr($sres, 0, 2000)];
    }
    if ($http !== 200) return ['error' => 'Service HTTP code ' . $http, 'raw' => substr($sres, 0, 2000)];
    $sj = json_decode($sres, true);
    if (json_last_error() !== JSON_ERROR_NONE) return ['error' => 'Service JSON parse error: ' . json_last_error_msg(), 'raw' => substr($sres, 0, 2000)];
    $is_available = (isset($sj['status']) && $sj['status'] == '404') ? '0' : '1';
    return ['is_available' => $is_available, 'raw' => $sj];
}

function check_delhivery($pincodeOrArray, $cfg)
{
    if (empty($cfg['serviceability_url']) || empty($cfg['token'])) return ['error' => 'Delhivery configuration missing'];

    $serviceUrl = $cfg['serviceability_url'];
    $token = $cfg['token'];
    $authType = isset($cfg['auth_type']) ? strtolower($cfg['auth_type']) : 'token';
    $pickup = isset($cfg['pickup_postcode']) ? $cfg['pickup_postcode'] : '110020';

    $ch = curl_init();
    if ($ch === false) return ['error' => 'Failed to init curl (delhivery)'];

    // Build auth header (support common variants)
    if ($authType === 'bearer') {
        $authHeader = 'Authorization: Bearer ' . $token;
    } else {
        // default: use Token scheme (some Delhivery endpoints expect Token <token>)
        $authHeader = 'Authorization: Token ' . $token;
    }

    $headers = ['Accept: application/json', 'Content-Type: application/json', 'User-Agent: PincodeChecker/1.0', $authHeader];
    curl_setopt_array($ch, [
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 30,
        // Follow redirects (301/302) which Delhivery may return for some endpoints
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 5
    ]);

    // If input is an array and endpoint supports pin-codes, call once for multiple pins
    $isPinCodesEndpoint = stripos($serviceUrl, 'pin-codes') !== false;
    if (is_array($pincodeOrArray) && $isPinCodesEndpoint) {
        // prepare comma-separated list of pincodes
        $codes = array_map(function ($p) {
            return preg_replace('/[^0-9]/', '', $p);
        }, $pincodeOrArray);
        $codes = array_filter($codes, function ($c) {
            return preg_match('/^\d{6}$/', $c);
        });
        if (count($codes) === 0) return array();
        $url = rtrim($serviceUrl, '/') . '?filter_codes=' . urlencode(implode(',', $codes));
        curl_setopt($ch, CURLOPT_URL, $url);
        $res = curl_exec($ch);
        if ($res === false) {
            $err = curl_error($ch);
            curl_close($ch);
            // return error per pin
            $out = [];
            foreach ($codes as $c) $out[$c] = ['error' => 'Curl error: ' . $err];
            return $out;
        }
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($http === 401 || $http === 403) {
            $body = substr($res, 0, 2000);
            curl_close($ch);
            $out = [];
            foreach ($codes as $c) $out[$c] = ['error' => 'Authentication failed with Delhivery (HTTP ' . $http . '). Response: ' . $body];
            return $out;
        }
        if ($http < 200 || $http >= 300) {
            $body = substr($res, 0, 2000);
            curl_close($ch);
            $out = [];
            foreach ($codes as $c) $out[$c] = ['error' => 'Delhivery HTTP code ' . $http . '. Response: ' . $body];
            return $out;
        }
        curl_close($ch);
        $j = json_decode($res, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $err = json_last_error_msg();
            $out = [];
            foreach ($codes as $c) $out[$c] = ['error' => 'Delhivery JSON parse error: ' . $err . ' — Raw: ' . substr($res, 0, 2000)];
            return $out;
        }

        // Response is expected to be an array of entries; map them by pin
        $out = [];
        if (is_array($j)) {
            foreach ($j as $entry) {
                if (!is_array($entry)) continue;
                $pinKey = null;
                if (isset($entry['pin_code'])) $pinKey = (string)$entry['pin_code'];
                elseif (isset($entry['pincode'])) $pinKey = (string)$entry['pincode'];
                elseif (isset($entry['pin'])) $pinKey = (string)$entry['pin'];
                if ($pinKey === null) continue;
                // determine availability
                $is_available = '0';
                if (isset($entry['serviceability'])) $is_available = ($entry['serviceability']) ? '1' : '0';
                elseif (isset($entry['serviceable'])) $is_available = ($entry['serviceable']) ? '1' : '0';
                elseif (isset($entry['status']) && ($entry['status'] == 200 || $entry['status'] == '200')) $is_available = '1';
                else $is_available = '1';
                $out[$pinKey] = ['is_available' => $is_available, 'raw' => $entry];
            }
        }
        // For any requested code not returned, mark unavailable with note
        foreach ($codes as $c) if (!isset($out[$c])) $out[$c] = ['is_available' => '0', 'error' => 'No data returned for pin'];
        return $out;
    }

    // Fallback single-pin behavior
    $pincode = is_array($pincodeOrArray) ? (string)array_values($pincodeOrArray)[0] : $pincodeOrArray;
    // If the configured URL is the Delhivery pin-codes endpoint use its expected query param
    if ($isPinCodesEndpoint) {
        // e.g. https://track.delhivery.com/c/api/pin-codes/json/?filter_codes=pin_code
        $url = rtrim($serviceUrl, '/') . '?filter_codes=' . urlencode($pincode);
    } else {
        // Build query params similar to Shiprocket so frontend behaviour is consistent
        $query = http_build_query([
            'pickup_postcode' => $pickup,
            'delivery_postcode' => $pincode,
            'cod' => '1',
            'weight' => '0.10'
        ]);
        $url = $serviceUrl . (strpos($serviceUrl, '?') === false ? '?' . $query : '&' . $query);
    }
    curl_setopt($ch, CURLOPT_URL, $url);

    $res = curl_exec($ch);
    if ($res === false) {
        $err = curl_error($ch);
        curl_close($ch);
        return ['error' => 'Curl error: ' . $err];
    }
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    // On auth-related HTTP codes return helpful message
    if ($http === 401 || $http === 403) {
        // include server body if available to help debugging
        $body = substr($res, 0, 2000);
        curl_close($ch);
        return ['error' => 'Authentication failed with Delhivery (HTTP ' . $http . '). Response: ' . $body];
    }
    if ($http < 200 || $http >= 300) {
        $body = substr($res, 0, 2000);
        curl_close($ch);
        return ['error' => 'Delhivery HTTP code ' . $http . '. Response: ' . $body];
    }

    curl_close($ch);
    $j = json_decode($res, true);
    if (json_last_error() !== JSON_ERROR_NONE) return ['error' => 'Delhivery JSON parse error: ' . json_last_error_msg() . ' — Raw: ' . substr($res, 0, 2000)];

    // Try to map various possible response shapes to is_available
    $is_available = '0';
    // 1) If endpoint returned keyed results for pin codes
    if (is_array($j)) {
        // If array of entries, try to find matching pin entry
        $found = null;
        foreach ($j as $entry) {
            if (!is_array($entry)) continue;
            // common keys: pin_code, pin, pincode
            if ((isset($entry['pin_code']) && (string)$entry['pin_code'] === (string)$pincode) || (isset($entry['pincode']) && (string)$entry['pincode'] === (string)$pincode) || (isset($entry['pin']) && (string)$entry['pin'] === (string)$pincode)) {
                $found = $entry;
                break;
            }
        }
        if ($found !== null) {
            // look for common indicators
            if (isset($found['serviceability'])) {
                $is_available = ($found['serviceability']) ? '1' : '0';
            } elseif (isset($found['serviceable'])) {
                $is_available = ($found['serviceable']) ? '1' : '0';
            } elseif (isset($found['status']) && ($found['status'] == 200 || $found['status'] == '200')) {
                $is_available = '1';
            } elseif (isset($found['message']) && stripos($found['message'], 'not') !== false) {
                $is_available = '0';
            } else {
                // default to available when present in response entries
                $is_available = '1';
            }
            return ['is_available' => $is_available, 'raw' => $j];
        }
    }

    // 2) Fallbacks for other shapes
    if (isset($j['serviceable'])) {
        $is_available = ($j['serviceable']) ? '1' : '0';
    } elseif (isset($j['status']) && ($j['status'] === 200 || $j['status'] === '200')) {
        $is_available = '1';
    } elseif (isset($j['data']) && isset($j['data']['serviceable'])) {
        $is_available = ($j['data']['serviceable']) ? '1' : '0';
    } else {
        // Fallback: if any field indicates not found (404) mark unavailable, else optimistic available
        if (isset($j['message']) && stripos($j['message'], 'not') !== false) {
            $is_available = '0';
        } else {
            $is_available = '1';
        }
    }

    return ['is_available' => $is_available, 'raw' => $j];
}
