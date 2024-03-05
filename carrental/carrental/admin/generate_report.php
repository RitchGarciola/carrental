<?php
// (Your existing database connection code)
include('includes/config.php');
// (Your existing SQL query)
// SQL query
$sql = "
    SELECT
        YEAR(`tblbooking`.`PostingDate`) AS YEAR,
        WEEK(`tblbooking`.`PostingDate`) AS WEEK,
        `tblusers`.`FullName`,
        `tblvehicles`.`VehiclesTitle`,
        `tblvehicles`.`PricePerDay`,
        SUM(DATEDIFF(`tblbooking`.`ToDate`, `tblbooking`.`FromDate`)) AS NumberOfDays,
        SUM(`tblbooking`.`totalrentals`) AS totalrentals,
        `tblbooking`.`PostingDate`,
        COUNT(*) AS TotalBookings
    FROM
        `tblbooking`
        INNER JOIN `tblusers` ON (`tblbooking`.`userEmail` = `tblusers`.`EmailId`)
        INNER JOIN `tblvehicles` ON (`tblbooking`.`VehicleId` = `tblvehicles`.`id`)
    WHERE
        `tblbooking`.`Status` > 0
    GROUP BY
        YEAR, WEEK, `tblusers`.`FullName`, `tblvehicles`.`VehiclesTitle`, `tblvehicles`.`PricePerDay`, `tblbooking`.`PostingDate`
    ORDER BY
        WEEK;
";

// Execute the query
$stmt = $dbh->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Set the response headers
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="car_rental_report.csv"');

// Open the output stream
$output = fopen('php://output', 'w');

// Write the header row
$columns = array('YEAR', 'WEEK', 'FullName', 'VehiclesTitle', 'PricePerDay', 'NumberOfDays', 'totalrentals', 'PostingDate', 'TotalBookings');
fputcsv($output, $columns);

// Write the data rows
foreach ($results as $result) {
    fputcsv($output, $result);
}

// Close the output stream
fclose($output);
 ?>