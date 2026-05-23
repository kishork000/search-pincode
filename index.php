<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sanyasi Ayurveda SpeedPost, HD and Courier Service Pincode Finder</title>
    <link rel="shortcut icon" href="favicon.ico" />
    <link rel="stylesheet" href="./css/nav.css" />
    <!-- <link rel="stylesheet" href="css/style.css?v=1.001.a04" /> -->
</head>
<?php
// One-time Depawali/Diwali banner: only show on 2025-10-20 and 2025-10-21
date_default_timezone_set(@date_default_timezone_get() ?: 'UTC');
$today = date('Y-m-d');
$show_dates = [
    '2026-03-31', // today
    '2026-04-05', // tomorrow
];
$show_banner = in_array($today, $show_dates, true);
?>
<style>
    /* === Global Reset === */
    * {
        box-sizing: border-box;
        scrollbar-width: thin;
        scrollbar-color: rgba(255, 255, 255, 0.3) rgba(0, 0, 0, 0.3);
    }

    html,
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, Helvetica, sans-serif;
        background: rgb(39, 38, 38);
        max-width: 100%;
        overflow-x: hidden;
    }

    /* body {
        margin: 0;
        padding: 0;
        font-family: Arial, Helvetica, sans-serif;
        background: rgb(39, 38, 38);
        box-sizing: border-box;
    } */

    .myheading h2 {
        font-size: 1.5rem;
        color: rgba(198, 239, 247, 0.993);
        text-transform: uppercase;
        font-weight: 300;
        text-align: center;
        margin-top: 24px;
        margin-bottom: 18px;
        letter-spacing: 2px;
    }


    .main-search-panel {
        /* min-height: 100vh; */
        width: 100vw;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background: rgb(39, 38, 38);
        box-sizing: border-box;
        padding: 0;
    }

    .form-container {
        width: 100vw;
        margin: 0;
        background-color: rgb(37, 55, 70);
        background-size: auto;
        box-shadow: 0 2px 16px 0 rgba(0, 0, 0, 0.18);
        padding: 5px 5px 5px 5px;
        display: flex;
        flex-direction: column;
        align-items: stretch;
        justify-content: center;
        box-sizing: border-box;
    }

    .search-forms {
        width: 100%;
        margin: 0;
        display: flex;
        flex-direction: row;
        gap: 18px;
        justify-content: stretch;
        align-items: stretch;
        box-sizing: border-box;
    }

    .search-forms form {
        flex: 1 1 0;
        margin: 0 4px;
        min-width: 0;
        max-width: none;
        box-sizing: border-box;
    }


    center {
        width: 100%;
        display: flex;
        justify-content: center;
        flex-direction: column;
        align-items: center;
    }


    form {
        position: relative;
        margin: 0 auto;
        width: 100%;
        max-width: 100%;
        height: auto;
        padding: 1px 0;
        background: #6b7175;
        /* display: flex;
        flex-direction: row; */
        align-items: center;
        text-align: center;
        border-radius: 12px;
        box-sizing: border-box;
        overflow: hidden;
    }


    input,
    label,
    button {
        margin: 8px 0;
        padding: 10px;
        border: none;
        outline: none;
        box-sizing: border-box;
        border-radius: 50px;
        font-size: 1rem;
        text-align: center;
        width: 100%;
        max-width: 320px;
    }


    label {
        width: 100%;
        max-width: 320px;
        font-size: 1.1rem;
        margin-bottom: 4px;
    }

    input::placeholder {
        color: gray;
    }

    input {
        width: 100%;
        max-width: 320px;
        box-shadow: 3px 3px 3px rgb(53, 51, 51);
        transition: 0.5s;
    }


    input:hover {
        box-shadow: none;
        background: #ecf0f3;
        letter-spacing: 2px;
    }

    button {
        background: #1da1f2;
        width: 150px;
        max-width: 200px;
        cursor: pointer;
        font-weight: 900;
        letter-spacing: 3px;
        box-shadow: 3px 3px 3px rgb(53, 51, 51);
        transition: 0.5s;
        margin-top: 10px;
    }

    button:hover {
        box-shadow: none;
        background: #047fcc;
        letter-spacing: 5px;
        color: #fff;
    }

    table {
        width: 100%;
        table-layout: fixed;
        font-size: 1rem;
    }

    .tbl-header {
        background-color: rgba(255, 255, 255, 0.3);
    }

    .tbl-content {
        height: 750px;
        max-height: 100vh;
        overflow-x: auto;
        margin-top: 0px;
        /* border: 3px solid rgba(255, 255, 255, 0.3); */
        border-radius: 10px;
        background: rgba(37, 55, 70, 0.7);
        width: 100%;
        box-sizing: border-box;
    }

    th {
        text-align: center;
        font-weight: 500;
        font-size: 1rem;
        color: #fff;
        text-transform: uppercase;
        padding: 10px 6px;
    }

    td {
        padding: 10px 6px;
        text-align: center;
        vertical-align: middle;
        font-weight: 300;
        font-size: 1.1rem;
        color: #fff;
        border-bottom: solid 1px rgba(255, 255, 255, 0.1);
        word-break: break-word;
    }

    /* Responsive Design */
    @media (max-width: 900px) {
        .form-container {
            max-width: 100vw;
            padding: 12px 4px;
        }

        .main-search-panel {
            padding: 0;
        }

        .search-forms {
            gap: 10px;
        }
    }

    @media (max-width: 700px) {
        .search-forms {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .search-forms form {
            margin: 0;
        }
    }

    @media (max-width: 600px) {
        .myheading h1 {
            font-size: 1.2rem;
            margin-top: 12px;
            margin-bottom: 10px;
        }

        .form-container {
            max-width: 100vw;
            padding: 8px 2px;
        }

        .main-search-panel {
            padding: 0;
        }

        form {
            max-width: 100vw;
            padding: 4px 0;
        }

        input,
        label,
        button {
            max-width: 100vw;
            font-size: 0.98rem;
        }

        .tbl-content {
            max-height: 40vh;
        }

        td,
        th {
            font-size: 0.95rem;
            padding: 6px 2px;
        }
    }

    footer {
        position: relative;
        width: 100%;
        background-color: rgba(37, 55, 70, 0.7);
        color: #fff;
        padding: 10px 0;
        text-align: center;
        font-size: 0.9rem;
    }


    /* for custom scrollbar for firefox */
    * {
        scrollbar-width: thin;
        scrollbar-color: rgba(255, 255, 255, 0.3) rgba(0, 0, 0, 0.3);
    }

    /* for custom scrollbar for webkit browser */

    ::-webkit-scrollbar {
        width: 6px;
    }

    ::-webkit-scrollbar-track {
        -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
    }

    ::-webkit-scrollbar-thumb {
        -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
    }

    ::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    /* Diwali banner styles */
    .diwali-banner {
        position: fixed;
        right: -380px;
        /* start off-screen */
        top: 20px;
        width: 340px;
        background: linear-gradient(90deg, #ffb347, #ffcc33);
        color: #2b2b2b;
        padding: 14px 48px 14px 16px;
        /* reserve space on the right for small close */
        border-radius: 8px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.25);
        transition: right 0.45s cubic-bezier(.2, .9, .2, 1);
        z-index: 99999;
        font-family: Arial, Helvetica, sans-serif;
    }

    .diwali-banner.show {
        right: 20px;
    }

    /* close button removed - banner auto-closes after timeout */

    .diwali-content .diwali-en {
        font-weight: 800;
        margin-bottom: 6px;
        font-size: 1rem;
    }

    .diwali-content .diwali-hi {
        font-weight: 600;
        font-size: 0.95rem;
    }

    /* Serviceability UI: small buttons and spinner */
    .service-check.btn-small {
        padding: 4px 6px;
        border-radius: 6px;
        font-size: 0.85rem;
        min-width: 54px;
    }

    .service-result {
        font-weight: 700;
    }

    .service-result.available {
        color: #a6f3a6;
    }

    .service-result.unavailable {
        color: #ffb3b3;
    }

    .service-result.error {
        color: #ffcc66;
    }

    .spinner {
        display: inline-block;
        width: 14px;
        height: 14px;
        border: 2px solid rgba(255, 255, 255, 0.16);
        border-top: 2px solid #fff;
        border-radius: 50%;
        animation: spin 0.9s linear infinite;
        vertical-align: middle;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }
</style>

<body>

    <!-- Modern Navbar -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="http://192.168.1.49:8081/" class="nav-logo">Sanyasi Ayurveda</a>
            <div class="nav-links">
                <a href="http://192.168.1.49:8081/" class="nav-link ">Home</a>
                <a href="http://192.168.1.49:8081/villagesearch/" class="nav-link ">Village Search</a>
                <a href="http://192.168.1.49:8081/faq-app/" class="nav-link">FAQs</a>
                <!-- <a href="http://192.168.1.49:8081/" class="nav-link">Courier Search</a>
                <a href="http://192.168.1.49:8081/" class="nav-link">Contact</a> -->
            </div>
        </div>
    </nav>

    <?php if (!empty($show_banner)) : ?>
        <div id="diwaliBanner" class="diwali-banner" role="region" aria-label="Diwali greeting banner">
            <div class="diwali-content">
                <div class="diwali-en">🚨 MEDICINE PRICE UPDATE </div>
                <div class="diwali-hi">01-04-2026 से हम अपनी दवाइयों के नए मूल्य लागू
                    कर रहे हैं: ₹655/- वाली दवाइयाँ → ₹699/-, ₹750/- वाली दवाइयाँ → ₹799/-, ₹955/- वाली दवाइयाँ → ₹999/- अतः
                    01-04-2026 की सुबह से सभी ऑर्डर नए रेट पर ही बुक करें और ग्राहक को दवा का नया मूल्य एकदम स्पष्ट बताएं।
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="main-search-panel">
        <div class="myheading">
            <h2>SANYASI AYURVEDA SPEED POST, HD AND COURIER SERVICE PINCODE FINDER</h2>
        </div>
        <div class="form-container">
            <div class="search-forms">
                <form id="hdCourierForm" method="post">
                    <label for="name">HD & COURIER PINCODE SEARCH</label>
                    <input id="name" type="text" name="name" placeholder="Type PIN Code" />
                    <button id="check" type="submit">Search</button>
                </form>
                <form id="speedPostForm" method="post">
                    <label for="name1">SPEEDPOST PINCODE SEARCH</label>
                    <input id="name1" type="text" name="name1" placeholder="Type PIN / PO / DIST NAME" />
                    <button id="check1" type="submit">Search</button>
                </form>
            </div>
            <div class="tbl-content" style="margin-top:18px; width:100%;">
                <?php
                $blank = isset($_GET['blank']) ? htmlspecialchars($_GET['blank']) : '';
                if (!empty($blank)) {
                    echo "<h2>{$blank}</h2>";
                }
                ?>
                <div id="disp">
                    <p>Please Enter the Pincode in Search Box</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="bg-light">
            <div class="container text-center">
                <p class="text-muted mb-0 py-2">© 2020 Sanyasi Ayurveda Pvt. Ltd. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/script.js"></script>
    <script>
        $(document).ready(function() {
            // Disable right-click on video
            $("video").on("contextmenu", function() {
                return false;
            });

            // HD & Courier search
            $("#hdCourierForm").submit(function(e) {
                e.preventDefault();
                const name = $("#name").val().trim();

                if (name === "") {
                    $("#disp").html("<p style='color:red;'>Please enter a PIN code.</p>");
                } else {
                    $("#disp").html("<p>Loading...</p>");
                    $.ajax({
                        url: "pin_check.php",
                        type: "POST",
                        data: {
                            name: name
                        },
                        timeout: 30000,
                        success: function(response) {
                            $("#disp").html(response);
                        },
                        error: function(xhr, status, error) {
                            if (status === 'timeout') {
                                $("#disp").html(
                                    "<p style='color:red;'>Request timeout. Please try again.</p>"
                                );
                            } else {
                                $("#disp").html("<p style='color:red;'>Error: " + (error ||
                                    'Unknown error') + "</p>");
                            }
                        }
                    });
                }
            });

            // SpeedPost search
            $("#speedPostForm").submit(function(e) {
                e.preventDefault();
                const name1 = $("#name1").val().trim();

                if (name1 === "") {
                    $("#disp").html("<p style='color:red;'>Please enter a PIN / PO / DIST name.</p>");
                } else {
                    $("#disp").html("<p>Loading...</p>");
                    $.ajax({
                        url: "allpin_check.php",
                        type: "POST",
                        data: {
                            name1: name1
                        },
                        timeout: 30000,
                        success: function(response) {
                            $("#disp").html(response);
                        },
                        error: function(xhr, status, error) {
                            if (status === 'timeout') {
                                $("#disp").html(
                                    "<p style='color:red;'>Request timeout. Please try again.</p>"
                                );
                            } else {
                                $("#disp").html("<p style='color:red;'>Error: " + (error ||
                                    'Unknown error') + "</p>");
                            }
                        }
                    });
                }
            });

            // Enhanced delegated handler for .service-check buttons
            // - Small spinner instead of plain text
            // - Client-side in-memory cache for this session to avoid repeat requests
            // - Toggle / Re-check link to allow re-running the check
            // - Only call Shiprocket or Delhivery adapters; otherwise show 'No Live API'
            (function() {
                // cache: key -> {data: <api response object> , ts: <Date.now()> }
                var cache = {};
                // re-check usage counter per key
                var recheckCount = {};
                // expiry in ms (set to 7 minutes — within your requested 5-10 minute window)
                var CACHE_EXPIRY_MS = 7 * 60 * 1000;

                function isHomeDeliveryName(name) {
                    if (!name) return false;
                    var s = String(name).toLowerCase();
                    return s.indexOf('home') !== -1 && s.indexOf('deliv') !== -
                        1; // matches 'home delivery' variants
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

                    // Show Re-check link only if recheckCount < 2
                    var count = recheckCount[key] || 0;
                    var linkHtml = '';
                    if (count < 2) {
                        linkHtml = ' <a href="#" class="recheck-link" data-key="' + $('<div>').text(key)
                            .html() + '" style="color:#bcd; font-size:0.85rem; margin-left:6px;">Re-check</a>';
                    } else {
                        linkHtml =
                            ' <span style="color:#888; font-size:0.85rem; margin-left:6px;">Re-check limit reached</span>';
                    }

                    var html = '<span class="' + cls + '">' + $('<div>').text(text).html() + '</span>' +
                        linkHtml;
                    return html;
                }

                function isCachedValid(key) {
                    if (!cache[key]) return false;
                    var entry = cache[key];
                    if (!entry.ts) return false;
                    return (Date.now() - entry.ts) < CACHE_EXPIRY_MS;
                }

                $(document).on('click', '.service-check', function(e) {
                    e.preventDefault();
                    var btn = $(this);
                    var pincode = String(btn.data('pincode') || '').trim();
                    var courierRaw = String(btn.data('courier') || '').trim();
                    var courierRawLower = courierRaw.toLowerCase();

                    // If DB label suggests HOME DELIVERY then the check should not work
                    if (isHomeDeliveryName(courierRaw)) {
                        btn.replaceWith('<span class="service-result">HD</span>');
                        return;
                    }

                    // Map DB courier names to supported adapters
                    var courier = 'shiprocket';
                    if (courierRawLower.indexOf('delhiv') !== -1 || courierRawLower.indexOf(
                            'delhivery') !== -1) courier = 'delhivery';
                    else if (courierRawLower.indexOf('shiprocket') !== -1) courier = 'shiprocket';
                    else {
                        // Not a configured live courier
                        btn.replaceWith('<span class="service-result error">No Live API</span>');
                        return;
                    }

                    var key = courier + '|' + pincode;

                    // If cached and not expired, show cached result and keep Re-check available
                    if (isCachedValid(key)) {
                        btn.replaceWith(renderResultElement(key, cache[key].data));
                        return;
                    }

                    // show spinner small button while checking
                    btn.addClass('btn-small').html('<span class="spinner"></span>');
                    btn.prop('disabled', true);

                    var timeoutId = setTimeout(function() {
                        cache[key] = {
                            data: {
                                error: 'Request timeout, try later'
                            },
                            ts: Date.now()
                        };
                        btn.replaceWith(renderResultElement(key, cache[key].data));
                    }, 4000);

                    $.ajax({
                        url: 'pincode-serviciblity.php',
                        data: {
                            pincode: pincode,
                            courier: courier
                        },
                        method: 'GET',
                        dataType: 'json',
                        timeout: 4000
                    }).done(function(data) {
                        clearTimeout(timeoutId);
                        // Store in cache with timestamp
                        cache[key] = {
                            data: data,
                            ts: Date.now()
                        };
                        btn.replaceWith(renderResultElement(key, data));
                    }).fail(function(jqxhr) {
                        clearTimeout(timeoutId);
                        var msg = 'Error';
                        if (jqxhr.statusText === 'timeout') {
                            msg = 'Request timeout, try later';
                        } else {
                            try {
                                var resp = jqxhr.responseJSON || JSON.parse(jqxhr
                                    .responseText || '{}');
                                if (resp && resp.error) msg = 'Error: ' + resp.error;
                            } catch (e) {
                                msg = 'Error';
                            }
                        }
                        cache[key] = {
                            data: {
                                error: msg
                            },
                            ts: Date.now()
                        };
                        btn.replaceWith(renderResultElement(key, cache[key].data));
                    });
                });

                // Re-check link handler: re-run the check for given cache key
                $(document).on('click', '.recheck-link', function(e) {
                    e.preventDefault();
                    var a = $(this);
                    var key = a.data('key');
                    if (!key) return;
                    var parts = String(key).split('|');
                    if (parts.length < 2) return;
                    var courier = parts[0];
                    var pincode = parts.slice(1).join('|');

                    // enforce max 2 re-checks
                    recheckCount[key] = (recheckCount[key] || 0) + 1;
                    if (recheckCount[key] > 2) {
                        a.replaceWith(
                            '<span style="color:#888; font-size:0.85rem; margin-left:6px;">Re-check limit reached</span>'
                        );
                        return;
                    }

                    // replace only the link with spinner to minimize layout shift
                    var spinner = $('<span class="spinner"></span>');
                    a.after(spinner);
                    a.hide();

                    var timeoutId = setTimeout(function() {
                        cache[key] = {
                            data: {
                                error: 'Request timeout, try later'
                            },
                            ts: Date.now()
                        };
                        var parent = a.parent();
                        parent.html(renderResultElement(key, cache[key].data));
                        spinner.remove();
                    }, 4000);

                    $.ajax({
                        url: 'pincode-serviciblity.php',
                        data: {
                            pincode: pincode,
                            courier: courier
                        },
                        method: 'GET',
                        dataType: 'json',
                        timeout: 4000
                    }).done(function(data) {
                        clearTimeout(timeoutId);
                        cache[key] = {
                            data: data,
                            ts: Date.now()
                        };
                        // Replace the whole parent content with span+link
                        var parent = a.parent();
                        parent.html(renderResultElement(key, data));
                    }).fail(function(jqxhr) {
                        clearTimeout(timeoutId);
                        var msg = 'Error';
                        if (jqxhr.statusText === 'timeout') {
                            msg = 'Request timeout, try later';
                        } else {
                            try {
                                var resp = jqxhr.responseJSON || JSON.parse(jqxhr
                                    .responseText || '{}');
                                if (resp && resp.error) msg = 'Error: ' + resp.error;
                            } catch (e) {
                                msg = 'Error';
                            }
                        }
                        cache[key] = {
                            data: {
                                error: msg
                            },
                            ts: Date.now()
                        };
                        var parent = a.parent();
                        parent.html(renderResultElement(key, cache[key].data));
                    }).always(function() {
                        // remove spinner if still present
                        spinner.remove();
                    });
                });
            })();
        });
    </script>

    <script>
        // Diwali banner behaviour: slide-in and auto-close after 60s or on click outside
        (function() {
            try {
                var banner = document.getElementById('diwaliBanner');
                if (!banner) return;

                // Show after a tiny delay to allow page layout
                setTimeout(function() {
                    banner.classList.add('show');
                }, 150);

                // Function to hide the banner
                function hideBanner() {
                    banner.classList.remove('show');
                }

                // Auto-close after 60 seconds
                setTimeout(hideBanner, 60000);

                // Hide on click outside the banner
                document.addEventListener('click', function(event) {
                    if (!banner.contains(event.target)) {
                        hideBanner();
                    }
                });
            } catch (e) {
                // fail silently
                console && console.error && console.error(e);
            }
        })();
    </script>

</body>

</html>