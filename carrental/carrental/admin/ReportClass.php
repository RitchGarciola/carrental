<?php
session_start();
error_reporting(0);
include('includes/config.php');
include_once('generateReport.php');
include_once('Report.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
}
$startDate = null;
$endDate = null;

// Add this block to handle form submission and generate the report links
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
}
?>
<!doctype html>
<html lang="en" class="no-js">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="theme-color" content="#3e454c">
    <title>Car Rental Portal | Financial Report</title>
    <!-- Include jQuery and DataTables if not already included -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js">
    </script>
    <!-- Include jsPDF -->
    <!-- Use jsPDF from CDN -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <!-- Font awesome -->
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <!-- Sandstone Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- Bootstrap Datatables -->
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
    <!-- Bootstrap social button library -->
    <link rel="stylesheet" href="css/bootstrap-social.css">
    <!-- Bootstrap select -->
    <link rel="stylesheet" href="css/bootstrap-select.css">
    <!-- Bootstrap file input -->
    <link rel="stylesheet" href="css/fileinput.min.css">
    <!-- Awesome Bootstrap checkbox -->
    <link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
    <!-- Admin Style -->
    <link rel="stylesheet" href="css/style.css">
    <style>
        .errorWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #dd3d36;
            -webkit-box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
            box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
        }

        .succWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #5cb85c;
            -webkit-box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
            box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
        }
    </style>

</head>

<body>
    <?php include('includes/header.php'); ?>



    <div class="ts-main-content">
        <?php include('includes/leftbar.php'); ?>
        <div class="content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <h2 class="page-title">Income Report</h2>
                        <!-- <button id="downloadPDF">Download PDF</button> -->
                        <!-- DataTales Example -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <div id="manageButton" class="d-flex justify-content-between align-items-center flex-wrap">
                                    <!-- <form action="?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                        <div class="d-flex flex-wrap align-items-center">
                                            <div class="mr-3">
                                                <div class="form-group mb-0">
                                                    <label for="startDate" class="mb-0 mr-1">Start Date:</label>
                                                    <input style="max-height: 20%;" id="startDate" name="startDate"
                                                        class="form-control custom-input" type="date" required
                                                        value="?php echo isset($_POST['startDate']) ? $_POST['startDate'] : ''?>" />
                                                </div>
                                            </div>
                                            <div class="mr-3">
                                                <div class="form-group mb-0">
                                                    <label for="endDate" class="mb-0 mr-1">End Date:</label>
                                                    <input style="max-height: 20%;" id="endDate" name="endDate"
                                                        class="form-control custom-input" type="date" required
                                                        value="?php echo isset($_POST['endDate']) ? $_POST['endDate'] : ''?>" />
                                                </div>
                                            </div>
                                            <div style="margin-top: 22px;">
                                                <button type="submit" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-search"></i> View</button>
                                            </div>
                                        </div>
                                    </form> -->

                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="yearDropdown">Select a Year:</label>
                                            <!-- Add this HTML/PHP code where you want to display the dropdown and table -->
                                            <select class='form-control' id="yearDropdown" name="yearDropdown" onchange="updateMonthsDropdown()">
                                                <?php
                                                $currentYear = date("Y");
                                                for ($year = $currentYear; $year <= $currentYear + 10; $year++) {
                                                    echo "<option value='$year'" . ($selectedYear == $year ? " selected" : "") . ">$year</option>";
                                                }
                                                ?>
                                            </select>
                                            <script>
                                                function updateYearDropdown() {
                                                    var selectedYear = document.getElementById("yearDropdown").value;
                                                    var selectedMonth = document.getElementById("monthDropdown").value;
                                                    var selectedStartWeek = document.getElementById("startWeekDropdown")
                                                        .value;
                                                    var selectedEndWeek = document.getElementById("endWeekDropdown")
                                                        .value;

                                                    window.location.href =
                                                        window.location.pathname + "?yearDropdown=" + selectedYear +
                                                        "&monthDropdown=" + selectedMonth + "&startWeekDropdown=" +
                                                        selectedStartWeek + "&endWeekDropdown=" + selectedEndWeek;
                                                }
                                            </script>
                                        </div>

                                        <div class="col-md-4">
                                            <label for="monthDropdown">Select a Month:</label>
                                            <select class='form-control' id="monthDropdown" name="monthDropdown" onchange="updateWeeksDropdown()">
                                                <!-- Months will be dynamically added here based on the selected year -->
                                            </select>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="startWeekDropdown">Select Start Date:</label>
                                                    <select class='form-control' id="startWeekDropdown" name="startWeekDropdown" onchange="updateDaysDropdown()">
                                                        <!-- Weeks will be dynamically added here based on the selected month -->
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="endWeekDropdown">Select End Date:</label>
                                                    <select class='form-control' id="endWeekDropdown" name="endWeekDropdown" onchange="updateDaysDropdown()">
                                                        <!-- Weeks will be dynamically added here based on the selected month -->
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class='row'>
                                        <div class="col-md-3"><a style="margin-top: 5px;">

                                                <a style="margin-top: 5px;" href="#" class="btn btn-sm btn-danger btn-icon-split" onclick="updateYearDropdown()"><span class="icon">
                                                        <i class="far fa-file-pdf"></i>
                                                    </span>
                                                    <span class="text">Fetch Income Summary</span></a></div>
                                        <div class="col-md-3">

                                            <!-- <a style="margin-top: 5px;" id="exportPdfBtn"
                                                class="btn btn-sm btn-danger btn-icon-split">
                                                <span class="icon">
                                                    <i class="far fa-file-pdf"></i>
                                                </span>
                                                <span class="text"> Export to PDF</span>
                                            </a> -->


                                        </div>
                                    </div>

                                    <script>
                                        function updateMonthsDropdown() {
                                            var selectedYear = document.getElementById("yearDropdown").value;
                                            var monthDropdown = document.getElementById("monthDropdown");
                                            monthDropdown.innerHTML = '';

                                            for (var i = 0; i < 12; i++) {
                                                var option = document.createElement("option");
                                                option.value = i + 1;
                                                option.text = new Date(selectedYear, i, 1).toLocaleString(
                                                    'default', {
                                                        month: 'long'
                                                    });
                                                monthDropdown.appendChild(option);
                                            }

                                            // Update weeks dropdown when changing the year or month
                                            updateWeeksDropdown();
                                        }

                                        function updateWeeksDropdown() {
                                            var selectedYear = document.getElementById("yearDropdown").value;
                                            var selectedMonth = document.getElementById("monthDropdown").value;
                                            var startWeekDropdown = document.getElementById("startWeekDropdown");
                                            var endWeekDropdown = document.getElementById("endWeekDropdown");

                                            startWeekDropdown.innerHTML = '';
                                            endWeekDropdown.innerHTML = '';

                                            var daysInMonth = new Date(selectedYear, selectedMonth, 0).getDate();
                                            var firstDay = new Date(selectedYear, selectedMonth - 1, 1).getDay();
                                            var numberOfWeeks = Math.ceil((daysInMonth + firstDay) / 7);

                                            for (var i = 1; i <= numberOfWeeks; i++) {
                                                var startDay = firstDay === 0 ? (i - 1) * 7 + 1 : (i - 1) * 7 -
                                                    firstDay + 2;
                                                startDay = startDay < 1 ? 1 : startDay;
                                                var endDay = Math.min(i * 7 - firstDay + 1, daysInMonth);
                                                var startOption = document.createElement("option");
                                                startOption.value = getFormattedDate(selectedMonth, startDay);
                                                startOption.text = getFormattedDate(selectedMonth, startDay);
                                                startWeekDropdown.appendChild(startOption);
                                                var endOption = document.createElement("option");
                                                endOption.value = getFormattedDate(selectedMonth, endDay);
                                                endOption.text = getFormattedDate(selectedMonth, endDay);
                                                endWeekDropdown.appendChild(endOption);
                                            }

                                            // Update days dropdown when changing the year or month
                                            updateDaysDropdown();
                                        }

                                        function updateDaysDropdown() {
                                            var selectedYear = document.getElementById("yearDropdown").value;
                                            var selectedMonth = document.getElementById("monthDropdown").value;
                                            var selectedWeek = document.getElementById("weekDropdown").value;
                                            var daysDropdown = document.getElementById("dayDropdown");
                                            daysDropdown.innerHTML = '';

                                            var daysInMonth = new Date(selectedYear, selectedMonth, 0).getDate();
                                            var firstDay = new Date(selectedYear, selectedMonth - 1, 1).getDay();

                                            // Adjust startDay based on the first day of the month
                                            var startDay = firstDay === 0 ? (selectedWeek - 1) * 7 + 1 : (
                                                selectedWeek - 1) * 7 - firstDay + 2;
                                            startDay = startDay < 1 ? 1 :
                                                startDay; // Ensure startDay is not less than 1

                                            for (var i = 0; i < 7; i++) {
                                                var dayIndex = startDay + i;
                                                if (dayIndex >= 1 && dayIndex <= daysInMonth) {
                                                    var option = document.createElement("option");
                                                    option.value = dayIndex;
                                                    option.text =
                                                        `${getFormattedDate(selectedMonth, dayIndex)} (${getDayOfWeek(selectedYear, selectedMonth, dayIndex)})`;
                                                    daysDropdown.appendChild(option);
                                                }
                                            }
                                        }

                                        function getFormattedDate(month, day) {
                                            month = month.toString(); // Convert month to string to remove leading zero
                                            day = day < 10 ? '0' + day : day;
                                            return month + '-' + day;
                                        }

                                        function getDayOfWeek(year, month, day) {
                                            var dayOfWeek = new Date(year, month - 1, day).toLocaleDateString(
                                                'en-US', {
                                                    weekday: 'long'
                                                });
                                            return dayOfWeek;
                                        }

                                        // Initialize the month dropdown when the page loads
                                        window.onload = function() {
                                            updateMonthsDropdown();
                                        };
                                    </script>

                                    <style>
                                        label {
                                            display: block;
                                            margin-bottom: 8px;
                                        }
                                    </style>

                                    <script>
                                        function showCurrentWeek() {
                                            // Make an AJAX request to the server
                                            var xhr = new XMLHttpRequest();
                                            xhr.onreadystatechange = function() {
                                                if (xhr.readyState == 4 && xhr.status == 200) {
                                                    // The server has responded, initiate the download
                                                    var blob = new Blob([xhr.responseText], {
                                                        type: 'text/csv'
                                                    });
                                                    var link = document.createElement('a');
                                                    link.href = window.URL.createObjectURL(blob);
                                                    link.download = 'car_rental_report.csv';
                                                    link.click();
                                                }
                                            };

                                            xhr.open('GET', 'generate_report.php', true);
                                            xhr.send();
                                        }
                                    </script>
                                    </br>

                                </div>
                            </div>
                            <!-- Zero Configuration Table -->
                            <div class="panel panel-default">
                                <div class="panel-heading">Income Data</div>
                                <div class="panel-body">
                                    <form action="generate_report_income.php" method="POST">
                                        <table id="zctb" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Name</th>
                                                    <th>Car Name</th>
                                                    <th>Posting date</th>
                                                    <th>Price Per Day</th>
                                                    <th>Days Rented</th>
                                                    <th>Rent Total</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <th> </th>
                                                    <th> </th>
                                                    <th> </th>
                                                    <th> </th>

                                                    <?php
                                                    $selectedYear2 = isset($_GET['yearDropdown']) ? $_GET['yearDropdown'] : date("Y");
                                                    $selectedMonth2 = isset($_GET['monthDropdown']) ? $_GET['monthDropdown'] : date("m");
                                                    $selectedStartWeek2 = isset($_GET['startWeekDropdown']) ? $_GET['startWeekDropdown'] : date("m-d");
                                                    $selectedEndWeek2 = isset($_GET['endWeekDropdown']) ? $_GET['endWeekDropdown'] : date("m-d");

                                                    $sql = "SELECT
                                                        SUM(`tblbooking`.`totalrentals`) AS `TotalRentals`
                                                        FROM
                                                        `tblbooking`
                                                        INNER JOIN `tblusers` ON (`tblbooking`.`userEmail` = `tblusers`.`EmailId`)
                                                        INNER JOIN `tblvehicles` ON (`tblbooking`.`VehicleId` = `tblvehicles`.`id`) 
                                                        WHERE
                                                        `tblbooking`.`Status` != 2
                                                        AND YEAR(`tblbooking`.`PostingDate`) = :selectedYear2 
                                                        AND MONTH(`tblbooking`.`PostingDate`) = :selectedMonth2 
                                                        AND CONCAT(
                                                            RIGHT('0' + CAST(MONTH(`tblbooking`.`PostingDate`) AS VARCHAR(2)), 2),  '-',
                                                            RIGHT('0' + CAST(DAY(`tblbooking`.`PostingDate`) AS VARCHAR(2)), 2)
                                                        ) BETWEEN :selectedStartWeek2 AND :selectedEndWeek2";

                                                    $query = $dbh->prepare($sql);
                                                    $query->bindParam(':selectedYear2', $selectedYear2, PDO::PARAM_STR); // Bind the parameter before executing
                                                    $query->bindParam(':selectedMonth2', $selectedMonth2, PDO::PARAM_STR); // Bind the parameter before executing
                                                    $query->bindParam(':selectedStartWeek2', $selectedStartWeek2, PDO::PARAM_STR); // Use PDO::PARAM_STR for string parameters
                                                    $query->bindParam(':selectedEndWeek2', $selectedEndWeek2, PDO::PARAM_STR); // Use PDO::PARAM_STR for string parameters
                                                    $query->execute();
                                                    $results = $query->fetchAll(PDO::FETCH_OBJ);
                                                    $cnt = 1;
                                                    if ($query->rowCount() > 0) {
                                                        foreach ($results as $result) { ?>
                                                            <th></th>
                                                            <th>TOTAL EARNINGS :</th>
                                                            <th><?php echo ($result->TotalRentals); ?></th>
                                                    <?php }
                                                    } ?>
                                                    <input type="text" name="selectedYear2" value="<?php echo $selectedYear2; ?>" style="display: none;">
                                                    <input type="text" name="selectedMonth2" value="<?php echo $selectedMonth2; ?>" style="display: none;">
                                                    <input type="text" name="selectedStartWeek2" value="<?php echo $selectedStartWeek2; ?>" style="display: none;">
                                                    <input type="text" name="selectedEndWeek2" value="<?php echo $selectedEndWeek2; ?>" style="display: none;">
                                                </tr>
                                            </tfoot>
                                            <tbody>
                                                <!-- Your table data -->
                                                <?php
                                                $selectedYear = isset($_GET['yearDropdown']) ? $_GET['yearDropdown'] : date("Y");
                                                $selectedMonth = isset($_GET['monthDropdown']) ? $_GET['monthDropdown'] : date("m");
                                                $selectedStartWeek = isset($_GET['startWeekDropdown']) ? $_GET['startWeekDropdown'] : date("m-d");
                                                $selectedEndWeek = isset($_GET['endWeekDropdown']) ? $_GET['endWeekDropdown'] : date("m-d");

                                                // Check if the form is submitted
                                                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                                    $selectedYear = isset($_POST['yearDropdown']) ? $_POST['yearDropdown'] : date("Y");
                                                    // Add similar checks for other dropdowns if needed
                                                }

                                                $sql = "SELECT
                                           YEAR(`tblbooking`.`PostingDate`) AS `YEAR`,
                                           WEEK(`tblbooking`.`PostingDate`) AS `WEEK`,
                                           `tblusers`.`FullName`,
                                           `tblvehicles`.`VehiclesTitle`,
                                           `tblvehicles`.`PricePerDay`,
                                           SUM(DATEDIFF(`tblbooking`.`ToDate`, `tblbooking`.`FromDate`)) AS `NumberOfDays`,
                                           SUM(`tblbooking`.`totalrentals`) AS `totalrentals`,
                                           `tblbooking`.`PostingDate`,
                                           COUNT(*) AS `TotalBookings`
                                       FROM
                                           `tblbooking`
                                           INNER JOIN `tblusers` ON (`tblbooking`.`userEmail` = `tblusers`.`EmailId`)
                                           INNER JOIN `tblvehicles` ON (`tblbooking`.`VehicleId` = `tblvehicles`.`id`)
                                       WHERE
                                           `tblbooking`.`Status` != 2
                                           AND YEAR(`tblbooking`.`PostingDate`) = :selectedYear 
                                           AND MONTH(`tblbooking`.`PostingDate`) = :selectedMonth
                                           AND CONCAT(
                                               RIGHT('0' + CAST(MONTH(`tblbooking`.`PostingDate`) AS VARCHAR(2)), 2),  '-',
                                               RIGHT('0' + CAST(DAY(`tblbooking`.`PostingDate`) AS VARCHAR(2)), 2)
                                           ) BETWEEN :selectedStartWeek AND :selectedEndWeek
                                       GROUP BY
                                           `YEAR`, `WEEK`, `tblusers`.`FullName`, `tblvehicles`.`VehiclesTitle`, `tblvehicles`.`PricePerDay`, `tblbooking`.`PostingDate`
                                       ORDER BY
                                           `YEAR`, `WEEK`";

                                                $query = $dbh->prepare($sql);
                                                $query->bindParam(':selectedYear', $selectedYear, PDO::PARAM_STR);
                                                $query->bindParam(':selectedMonth', $selectedMonth, PDO::PARAM_STR);
                                                $query->bindParam(':selectedStartWeek', $selectedStartWeek, PDO::PARAM_STR);
                                                $query->bindParam(':selectedEndWeek', $selectedEndWeek, PDO::PARAM_STR);
                                                $query->execute();

                                                $results = $query->fetchAll(PDO::FETCH_OBJ);
                                                $cnt = 1;



                                                if ($query->rowCount() > 0) {
                                                    foreach ($results as $result) { ?>
                                                        <tr>
                                                            <td><?php echo htmlentities($cnt); ?></td>
                                                            <td><?php echo htmlentities($result->FullName); ?></td>
                                                            <td><?php echo htmlentities($result->VehiclesTitle); ?></td>
                                                            <td><?php echo htmlentities($result->PostingDate); ?></td>
                                                            <td><?php echo htmlentities($result->PricePerDay); ?></td>
                                                            <td><?php echo htmlentities($result->NumberOfDays); ?></td>
                                                            <td><?php echo htmlentities($result->totalrentals); ?></td>

                                                        </tr>
                                                <?php
                                                        $cnt++;
                                                    }
                                                } ?>
                                            </tbody>

                                        </table>

                                        <input type="submit"  name="submit" value="Generate PDF" style="background-color: #4CAF50; color: white; padding: 10px 20px; border: none; cursor: pointer; border-radius: 5px;">

                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        #weekDisplay {
            font-size: 18px;
            margin-bottom: 20px;
        }
    </style>

    <!-- Loading Scripts -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap-select.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
    <script src="js/Chart.min.js"></script>
    <script src="js/fileinput.js"></script>
    <script src="js/chartData.js"></script>
    <script src="js/main.js"></script>
    <script language="javascript" type="text/javascript">
        function f3() {
            window.print();
        }
    </script>
</body>

</html>