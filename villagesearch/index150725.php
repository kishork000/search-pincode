<!DOCTYPE html>
<html lang="en">
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Sanyasi Ayurveda Village Finder</title>
<link rel="shortcut icon" href="favicon.ico" />
<link rel="stylesheet" href="../css/nav.css" />
<!-- <link rel="stylesheet" href="css/style.css?v=1.001.a04" /> -->

<!-- DataTables + jQuery -->
<link rel="stylesheet" href="css/jquery.dataTables.min.css">
<link rel="stylesheet" href="css/buttons.dataTables.min.css">

<head>
    <style>
        /* Global Styles */
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            font-family: Arial, Helvetica, sans-serif;
            background: rgb(39, 38, 38);
        }

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
        }

        ::-webkit-scrollbar-thumb {
            background: #1da1f2;
            border-radius: 4px;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Navbar */
        .navbar {
            width: 100vw;
            background: #0e3742;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            min-height: 56px;
            box-shadow: 0 2px 8px 0 rgba(0, 0, 0, 0.12);
            position: relative;
            z-index: 10;
        }

        .navbar .nav-container {
            width: 100%;
            max-width: 1200px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            box-sizing: border-box;
        }

        .navbar .nav-logo {
            font-size: 1.3rem;
            font-weight: 700;
            letter-spacing: 2px;
            color: #c6eff7;
            text-decoration: none;
        }

        .navbar .nav-links {
            display: flex;
            gap: 18px;
        }

        .navbar .nav-link {
            color: #fff;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 500;
            padding: 8px 18px;
            border-radius: 24px;
            transition: background 0.2s, color 0.2s;
            display: block;
        }

        .navbar .nav-link:hover,
        .navbar .nav-link.active {
            background: #1da1f2;
            color: #fff;
        }

        /* Heading */
        .myheading {
            width: 100%;
            background-color: rgb(37, 55, 70);
            padding: 10px 0;
            box-sizing: border-box;
        }

        .myheading h1 {
            font-size: 30px;
            color: rgba(198, 239, 247, 0.993);
            text-transform: uppercase;
            font-weight: 100;
            text-align: center;
            margin: 20px 0 15px;
        }

        /* Form & Filter Section */
        .form-container {
            width: 100%;
            background-color: rgb(37, 55, 70);
            padding: 5px 5px;
            box-sizing: border-box;
        }

        form {
            max-width: 1190px;
            background: #346161;
            padding: 18px 12px 18px 12px;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .filter-row {
            display: flex;
            gap: 18px;
            align-items: flex-end;
            justify-content: center;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            min-width: 160px;
            flex: 1 1 180px;
        }

        .filter-group label {
            color: #c6eff7;
            font-size: 15px;
            margin-bottom: 6px;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .filter-select,
        #check1 {
            margin: 0;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            box-sizing: border-box;
            background: #fff;
            color: #222;
            box-shadow: 2px 2px 6px rgba(53, 51, 51, 0.12);
            transition: box-shadow 0.2s, background 0.2s;
        }

        .filter-select:focus {
            outline: 2px solid #1da1f2;
            background: #eaf6fb;
        }

        #check1 {
            background: #1da1f2;
            color: #fff;
            font-weight: bold;
            letter-spacing: 1px;
            cursor: pointer;
            width: 100%;
            margin-top: 0;
            transition: background 0.2s, letter-spacing 0.2s;
            min-width: 120px;
        }

        #check1:hover,
        #check1:focus {
            background: #047fcc;
            letter-spacing: 2px;
        }

        /* Search Panel */
        .search-card {
            max-width: 100%;
            margin: 12px auto 0 auto;
            background: #23384d;
            border-radius: 14px;
            box-shadow: 0 2px 16px rgba(0, 0, 0, 0.13);
            display: flex;
            flex-direction: column;
            align-items: stretch;
            z-index: 2;
            position: relative;
        }

        @media (min-width: 900px) {
            .search-card {
                max-width: 1190px;
            }
        }

        .search-row {
            display: flex;
            gap: 12px;
            align-items: flex-end;
            justify-content: center;
            flex-wrap: wrap;
        }

        .search-group {
            display: flex;
            flex-direction: column;
            flex: 1 1 0;
            min-width: 0;
        }

        .search-group label {
            color: #c6eff7;
            font-size: 14px;
            margin-bottom: 4px;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .search-input,
        .search-select {
            width: 100%;
            box-sizing: border-box;
            padding: 10px 10px;
            border: none;
            border-radius: 7px;
            font-size: 15px;
            background: #f7fafc;
            color: #222;
            box-shadow: 1px 1px 4px rgba(53, 51, 51, 0.09);
            margin-bottom: 0;
            transition: box-shadow 0.2s, background 0.2s;
            min-width: 0;
            max-width: 100%;
            overflow: hidden;
        }

        .search-input:focus,
        .search-select:focus {
            outline: 2px solid #1da1f2;
            background: #eaf6fb;
        }

        .search-btn {
            padding: 10px 0;
            border: none;
            border-radius: 7px;
            background: linear-gradient(90deg, #1da1f2 60%, #047fcc 100%);
            color: #fff;
            font-weight: bold;
            font-size: 16px;
            letter-spacing: 1px;
            cursor: pointer;
            width: 100%;
            min-width: 100px;
            box-sizing: border-box;
            transition: background 0.2s, letter-spacing 0.2s;
            margin-top: 0;
            box-shadow: 1px 1px 4px rgba(53, 51, 51, 0.09);
        }

        .search-btn:hover,
        .search-btn:focus {
            background: #047fcc;
            letter-spacing: 2px;
        }

        #filter-container.search-row {
            display: flex;
            justify-content: flex-start;
            margin-bottom: 10px;
        }

        #filter-container .search-group {
            width: 50%;
            min-width: 200px;
            max-width: 400px;
        }

        #filter-container .search-input {
            width: 100%;
        }

        /* Table Styles */
        .tbl-content {
            height: 600px;
            overflow: auto;
            border: 3px solid #346161;
            margin-top: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #111;
            color: white;
        }

        th,
        td {
            padding: 12px;
            text-align: center;
            border: 1px solid #444;
        }

        .tbl-content table thead th {
            position: sticky;
            top: 0;
            background: #222;
            z-index: 3;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.07);
        }

        .tbl-content table tbody tr:nth-child(even) {
            background-color: #1a1a1a;
        }

        .tbl-content table tbody tr:hover {
            background-color: #333;
        }

        .tbl-content table tbody tr td {
            font-size: 14px;
            color: #c6eff7;
        }

        .tbl-content table tbody tr td a {
            color: #1da1f2;
            text-decoration: none;
        }

        /* Errors and Spinner */
        .error {
            color: #ff4444;
            background: #ffecec;
            padding: 15px;
            border-radius: 10px;
            margin: 15px 0;
        }

        .loading {
            color: #fff;
            text-align: center;
            padding: 20px;
        }

        .loading p {
            margin: 0;
            font-size: 18px;
        }

        .loading p span {
            font-weight: bold;
            color: #1da1f2;
        }

        .spinner {
            display: inline-block;
            width: 25px;
            height: 25px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: #1da1f2;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        .spinner:before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin-top: -10px;
            margin-left: -10px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.1);
        }

        /* Footer */
        footer {
            background: #23384d;
            color: #c6eff7;
            text-align: center;
            padding: 15px 0;
            position: relative;
            bottom: 0;
            width: 100%;
            font-size: 14px;
        }

        /* Responsive Design */
        @media (max-width: 900px) {
            .filter-row {
                gap: 10px;
            }

            form {
                padding: 10px 2vw;
            }
        }

        @media (max-width: 768px) {
            .form-group {
                flex: 1 1 100%;
                margin-bottom: 15px;
            }

            .myheading h1 {
                font-size: 24px;
            }
        }

        @media (max-width: 700px) {
            .navbar .nav-container {
                flex-direction: column;
                align-items: flex-start;
                padding: 0 8px;
            }

            .navbar .nav-links {
                width: 100%;
                flex-wrap: wrap;
                gap: 8px;
                margin-top: 4px;
            }

            .navbar .nav-link {
                padding: 8px 8px;
                font-size: 0.98rem;
            }
        }

        @media (max-width: 600px) {
            .filter-row {
                flex-direction: column;
                align-items: stretch;
                gap: 6px;
            }

            .filter-group {
                min-width: 0;
                width: 100%;
            }

            .filter-select,
            #check1 {
                width: 100%;
                font-size: 16px;
            }

            .myheading h1 {
                font-size: 22px;
            }

            .search-card {
                max-width: 98vw;
                padding: 10px 4vw 10px 4vw;
                margin: 10px auto 0 auto;
            }

            .search-row {
                flex-direction: column;
                gap: 7px;
            }

            .search-group {
                width: 100%;
                min-width: 0;
            }

            .search-input,
            .search-select,
            .search-btn {
                width: 100%;
                font-size: 15px;
                min-width: 0;
                max-width: 100%;
            }

            .search-title {
                font-size: 17px;
                margin-bottom: 7px;
            }

            #filter-container .search-group {
                width: 100%;
                max-width: 100%;
                min-width: 0;
            }
        }

        @media (max-width: 480px) {
            .form-container {
                padding: 10px;
            }

            .form-group input,
            .form-group select,
            .form-group button {
                font-size: 13px;
                padding: 10px;
            }

            button {
                font-size: 14px;
            }
        }
    </style>

</head>

<body>

    <!-- Modern Navbar -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="http://192.168.1.49:8081/codbetanew/" class="nav-logo">Sanyasi Ayurveda</a>
            <div class="nav-links">
                <a href="http://192.168.1.49:8081/codbetanew/" class="nav-link active">Home</a>
                <a href="http://192.168.1.49:8081/codbetanew/villagesearch/" class="nav-link active">Village Search</a>
                <a href="http://192.168.1.49:8081/codbetanew/faq-app/" class="nav-link">FAQs</a>
                <a href="http://192.168.1.49:8081/codbetanew/" class="nav-link">Courier Search</a>
                <a href="http://192.168.1.49:8081/codbetanew/" class="nav-link">Contact</a>
            </div>
        </div>
    </nav>

    <div class="myheading">
        <h1>SANYASI AYURVEDA VILLAGE FINDER</h1>
    </div>

    <!-- SpeedPost Search Form -->
    <div class="form-container">
        <div class="search-card">
            <!-- <div class="search-title">Village/PinCode Search</div> -->
            <form id="speedPostForm" method="post" autocomplete="off">
                <div class="search-row">
                    <div class="search-group">
                        <label for="state">State</label>
                        <select id="state" name="state" class="search-select" required>
                            <option value="">Select State</option>
                        </select>
                    </div>
                    <div class="search-group">
                        <label for="district">District</label>
                        <select id="district" name="district" class="search-select" required>
                            <option value="">Select District</option>
                        </select>
                    </div>
                </div>
                <div class="search-row" style="margin-top:10px;">
                    <div class="search-group">
                        <label for="name1">PIN Code (Optional)</label>
                        <input id="name1" type="text" class="search-input" name="name1"
                            placeholder="Type PIN code (Optional)"
                            pattern="\d{6}"
                            maxlength="6"
                            inputmode="numeric"
                            title="Please enter a 6-digit PIN code" />
                    </div>
                    <div class="search-group" style="align-items:flex-end;">
                        <button id="check1" type="submit" class="search-btn">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Display Results -->
    <div class="form-container">
        <div id="filter-container" class="search-row" style="display: none; margin-bottom:10px;">
            <div class="search-group">
                <input type="text" id="tableFilter" placeholder="Type Pin code Or Post Office Name to Filter table data..." class="search-input" />
            </div>
        </div>
        <div class="tbl-content">
            <div id="disp">
                <!-- Table will be loaded here -->
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
    <script src="./js/jquery-3.7.1.min.js"></script>
    <script src="./js/jquery.dataTables.min.js"></script>
    <script src="./js/dataTables.buttons.min.js"></script>
    <script src="./js/buttons.html5.min.js"></script>
    <script src="./js/jszip.min.js"></script>
    <script src="./js/script.js"></script>
    <script>
        $(document).ready(function() {

            // Load states
            $.get('get_states.php', function(data) {
                $('#state').html(data);
            });

            // Load districts on state change
            $('#state').on('change', function() {
                const state = $(this).val();
                if (state) {
                    $.post('get_districts.php', {
                        state: state
                    }, function(data) {
                        $('#district').html(data);
                    });
                } else {
                    $('#district').html('<option value="">Select District</option>');
                }
            });

            // SpeedPost form
            $("#speedPostForm").submit(function(e) {
                e.preventDefault();

                const params = {
                    state: $('#state').val(),
                    district: $('#district').val(),
                    name1: $('#name1').val().trim()
                };

                // PIN code validation: only allow 6 digit numbers if entered
                if (params.name1 && !/^\d{6}$/.test(params.name1)) {
                    $("#disp").html("<p class='error'>Please enter a valid 6-digit PIN code.</p>");
                    return;
                }

                if (!params.state && !params.district && !params.name1) {
                    $("#disp").html("<p class='error'>Please provide at least one search criteria</p>");
                    return;
                }

                $("#disp").html("<div class='loading'>Searching... <div class='spinner'></div></div>");

                $.ajax({
                    url: "allpin_check.php",
                    method: "POST",
                    data: params,
                    success: function(response) {
                        $("#disp").html(response);

                        const table = document.querySelector("#disp table");
                        const rows = table?.tBodies?.[0]?.rows || [];

                        if (rows.length > 0) {
                            document.getElementById("filter-container").style.display = "flex";
                            const filterInput = document.getElementById("tableFilter");
                            filterInput.value = "";

                            filterInput.oninput = function() {
                                const val = this.value.trim().toLowerCase();
                                const isNumber = /^\d/.test(val);
                                Array.from(rows).forEach(row => {
                                    const pincode = row.cells[0]?.textContent.toLowerCase() || "";
                                    const village = row.cells[1]?.textContent.toLowerCase() || "";
                                    let match = false;
                                    if (val === "") {
                                        match = true;
                                    } else if (isNumber) {
                                        match = pincode.startsWith(val);
                                    } else {
                                        match = village.startsWith(val);
                                    }
                                    row.style.display = match ? "" : "none";
                                });
                            };
                            // Initialize DataTable
                            $(table).DataTable({
                                // dom: 'Bfrtip',                                
                                paging: false,
                                searching: false,
                                ordering: true,
                                order: [
                                    [1, 'asc']
                                ],
                                columnDefs: [{
                                        orderable: true,
                                        targets: 0
                                    }, // Pincode
                                    {
                                        orderable: true,
                                        targets: 1
                                    }, // Village Name
                                    {
                                        orderable: true,
                                        targets: 2
                                    }, // Sub-District
                                    {
                                        orderable: true,
                                        targets: 3
                                    }, // District
                                    {
                                        orderable: true,
                                        targets: 4
                                    } // State
                                ]
                            });

                        } else {
                            document.getElementById("filter-container").style.display = "none";
                        }
                    },
                    error: function(xhr) {
                        $("#disp").html("<p class='error'>Error: " + xhr.statusText + "</p>");
                    }
                });
            });
        });
    </script>
    <script>
        // Debounce function
        function debounce(func, delay) {
            let timer;
            return function(...args) {
                clearTimeout(timer);
                timer = setTimeout(() => func.apply(this, args), delay);
            };
        }

        function filterRows(rows, value) {
            const lowerVal = value.toLowerCase();
            const isNumber = /^\d/.test(value);
            rows.forEach(row => {
                const pincode = row.cells[0]?.textContent.toLowerCase() || "";
                const village = row.cells[1]?.textContent.toLowerCase() || "";
                let match = false;

                if (isNumber) {
                    match = pincode.startsWith(lowerVal);
                } else {
                    match = village.startsWith(lowerVal);
                }

                row.style.display = match ? "" : "none";
            });
        }

        document.addEventListener("DOMContentLoaded", () => {
            const filterInput = document.getElementById("tableFilter");
            const tableRows = Array.from(document.querySelectorAll("#disp table tbody tr"));

            filterInput.addEventListener("input", debounce(function() {
                const val = this.value.trim();
                if (val === "") {
                    tableRows.forEach(row => row.style.display = "");
                } else {
                    filterRows(tableRows, val);
                }
            }, 200));
        });
    </script>

</body>

</html>