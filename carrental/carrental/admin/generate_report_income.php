<?php
require('fpdf/fpdf.php'); // Include FPDF library
include('includes/config.php');
// Fetch data from POST
$selectedYear2 = $_POST['selectedYear2'];
$selectedMonth2 = $_POST['selectedMonth2'];
$selectedStartWeek2 = $_POST['selectedStartWeek2'];
$selectedEndWeek2 = $_POST['selectedEndWeek2'];

// Create PDF class
class PDF extends FPDF {
    function Header() {
        $this->Image('../assets/images/Stanley_Logo.png',10,6,30);
        // Header content
        $this->SetFont('Arial','B',15);
        $this->Cell(276,15,'Income Report',0,0,'C');
        $this->Ln(25);
    }

    function Footer() {
        // Footer content
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('L');

// Add table headers
$pdf->SetFont('Arial','B',12);
$pdf->Cell(20,10,'#',1,0,'C');
$pdf->Cell(40,10,'Name',1,0,'C');
$pdf->Cell(40,10,'Car Name',1,0,'C');
$pdf->Cell(40,10,'Posting date',1,0,'C');
$pdf->Cell(40,10,'Price Per Day',1,0,'C');
$pdf->Cell(40,10,'Days Rented',1,0,'C');
$pdf->Cell(40,10,'Rent Total',1,0,'C');
$pdf->Ln();

// Fetch data from the database
// Use your SQL query to fetch data from the database
// Replace the SQL query below with your actual query
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
           AND YEAR(`tblbooking`.`PostingDate`) = :selectedYear2 
           AND MONTH(`tblbooking`.`PostingDate`) = :selectedMonth2
           AND CONCAT(
               RIGHT('0' + CAST(MONTH(`tblbooking`.`PostingDate`) AS VARCHAR(2)), 2),  '-',
               RIGHT('0' + CAST(DAY(`tblbooking`.`PostingDate`) AS VARCHAR(2)), 2)
           ) BETWEEN :selectedStartWeek2 AND :selectedEndWeek2
       GROUP BY
           `YEAR`, `WEEK`, `tblusers`.`FullName`, `tblvehicles`.`VehiclesTitle`, `tblvehicles`.`PricePerDay`, `tblbooking`.`PostingDate`
       ORDER BY
           `YEAR`, `WEEK`";
$query = $dbh->prepare($sql);
$query->bindParam(':selectedYear2', $selectedYear2, PDO::PARAM_STR);
$query->bindParam(':selectedMonth2', $selectedMonth2, PDO::PARAM_STR);
$query->bindParam(':selectedStartWeek2', $selectedStartWeek2, PDO::PARAM_STR);
$query->bindParam(':selectedEndWeek2', $selectedEndWeek2, PDO::PARAM_STR);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);

// Add table data
$pdf->SetFont('Arial','',12);
$cnt = 1;
foreach ($results as $result) {
    $pdf->Cell(20,10,$cnt,1,0,'C');
    $pdf->Cell(40,10,$result->FullName,1,0,'C');
    $pdf->Cell(40,10,$result->VehiclesTitle,1,0,'C');
    $pdf->Cell(40,10,$result->PostingDate,1,0,'C');
    $pdf->Cell(40,10,$result->PricePerDay,1,0,'C');
    $pdf->Cell(40,10,$result->NumberOfDays,1,0,'C');
    $pdf->Cell(40,10,$result->totalrentals,1,0,'C');
    $pdf->Ln();
    $cnt++;
}

// Output PDF
$pdf->Output();
?>
