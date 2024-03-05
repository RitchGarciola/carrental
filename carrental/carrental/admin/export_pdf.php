<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start(); // Start output buffering
require_once('tcpdf/tcpdf.php'); // Include the TCPDF library

define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PASS','');
define('DB_NAME','carrental');

// Establish database connection.
try {
    $dbh = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME,DB_USER, DB_PASS,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
} catch (PDOException $e) {
    exit("Error: " . $e->getMessage());
}

$cellspacing = 0;
$cellspacingx = 0;

// Create PDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetCreator('Your Creator');
$pdf->SetAuthor('Your Author');
$pdf->SetTitle('Table to PDF');
$pdf->SetHeaderData('', 0, 'Table to PDF', '');

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Add a page
$pdf->AddPage();

// Table header
$html = '<table border="1">
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
            <tbody>';

// Your database query and loop to fetch data here...
$selectedYear = isset($_GET['yearDropdown']) ? $_GET['yearDropdown'] : date("Y");
$selectedMonth = isset($_GET['monthDropdown']) ? $_GET['monthDropdown'] : date("m");
$selectedStartWeek = isset($_GET['startWeekDropdown']) ? $_GET['startWeekDropdown'] : date("m-d");
$selectedEndWeek = isset($_GET['endWeekDropdown']) ? $_GET['endWeekDropdown'] : date("m-d");

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
           `tblbooking`.`Status` > 0
           AND YEAR(`tblbooking`.`PostingDate`) = :selectedYear 
           AND MONTH(`tblbooking`.`PostingDate`) = :selectedMonth
           AND CONCAT(
               RIGHT('0' + CAST(MONTH(`tblbooking`.`PostingDate`) AS VARCHAR(2)), 2),  '-',
               RIGHT('0' + CAST(DAY(`tblbooking`.`PostingDate`) AS VARCHAR(2)), 2)
           ) BETWEEN :selectedStartWeek AND :selectedEndWeek
        GROUP BY
           `YEAR`, `WEEK`, `tblusers`.`FullName`, `tblvehicles`.`VehiclesTitle`, `tblvehicles`.`PricePerDay`, `tblbooking`.`PostingDate`
           ORDER BY `YEAR`, `WEEK`";

$query = $dbh->prepare($sql);
$query->bindParam(':selectedYear', $selectedYear, PDO::PARAM_STR);
$query->bindParam(':selectedMonth', $selectedMonth, PDO::PARAM_STR);
$query->bindParam(':selectedStartWeek', $selectedStartWeek, PDO::PARAM_STR);
$query->bindParam(':selectedEndWeek', $selectedEndWeek, PDO::PARAM_STR);

try {
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
    $cnt = 1;

    if ($query->rowCount() > 0) {
        foreach ($results as $result) {
            $html .= '<tr>
                        <td>' . htmlentities($cnt) . '</td>
                        <td>' . htmlentities($result->FullName) . '</td>
                        <td>' . htmlentities($result->VehiclesTitle) . '</td>
                        <td>' . htmlentities($result->PostingDate) . '</td>
                        <td>' . htmlentities($result->PricePerDay) . '</td>
                        <td>' . htmlentities($result->NumberOfDays) . '</td>
                        <td>' . htmlentities($result->totalrentals) . '</td>
                     </tr>';
            $cnt++;
        }
    }
} catch (Exception $e) {
    echo 'Error during query execution: ', $e->getMessage(), "\n";
    exit();
}

$html .= '</tbody></table>';

$pdf->writeHTML($html, true, false, true, false, '');

// Table footer (assuming the total earnings part should be displayed at the end of the table)
$html = '<tfoot>
            <tr>
                <th> </th>
                <th> </th>
                <th> </th>
                <th> </th>';

$sqlTotalEarnings = "SELECT
                        SUM(`tblbooking`.`totalrentals`) AS `TotalRentals`
                    FROM
                        `tblbooking`
                        INNER JOIN `tblusers` ON (`tblbooking`.`userEmail` = `tblusers`.`EmailId`)
                        INNER JOIN `tblvehicles` ON (`tblbooking`.`VehicleId` = `tblvehicles`.`id`) 
                    WHERE
                        `tblbooking`.`Status` > 0
                        AND YEAR(`tblbooking`.`PostingDate`) = :selectedYear2 
                        AND MONTH(`tblbooking`.`PostingDate`) = :selectedMonth2 
                        AND CONCAT(
                            RIGHT('0' + CAST(MONTH(`tblbooking`.`PostingDate`) AS VARCHAR(2)), 2),  '-',
                            RIGHT('0' + CAST(DAY(`tblbooking`.`PostingDate`) AS VARCHAR(2)), 2)
                        ) BETWEEN :selectedStartWeek2 AND :selectedEndWeek2";

$queryTotalEarnings = $dbh->prepare($sqlTotalEarnings);
$queryTotalEarnings->bindParam(':selectedYear2', $selectedYear2, PDO::PARAM_STR);
$queryTotalEarnings->bindParam(':selectedMonth2', $selectedMonth2, PDO::PARAM_STR);
$queryTotalEarnings->bindParam(':selectedStartWeek2', $selectedStartWeek2, PDO::PARAM_STR);
$queryTotalEarnings->bindParam(':selectedEndWeek2', $selectedEndWeek2, PDO::PARAM_STR);

try {
    $queryTotalEarnings->execute();
    $resultsTotalEarnings = $queryTotalEarnings->fetchAll(PDO::FETCH_OBJ);

    if ($queryTotalEarnings->rowCount() > 0) {
        foreach ($resultsTotalEarnings as $resultTotalEarnings) {
            $html .= '<th> </th>
                      <th>TOTAL EARNINGS :</th>
                      <th>' . htmlentities($resultTotalEarnings->TotalRentals) . '</th>';
        }
    }
} catch (Exception $e) {
    echo 'Error during total earnings query execution: ', $e->getMessage(), "\n";
    exit();
}

$html .= '</tr>
          </tfoot>';

// Output the PDF
try {
    $pdf->Output('table_to_pdf.pdf', 'I');
    ob_end_clean(); // Clean (erase) the output buffer and turn off output buffering 
} catch (Exception $e) {
    echo 'Error during PDF output: ', $e->getMessage(), "\n";
    exit();
}
?>
