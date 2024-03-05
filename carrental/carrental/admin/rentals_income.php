<?php
require_once('tcpdf/tcpdf.php');

// Extend TCPDF to create custom header and footer
class MYPDF extends TCPDF {
    // Page header
    public function Header() {
        // Set font
        $this->SetFont('helvetica', 'B', 12);

        // Title
        $this->Cell(0, 10, 'Weekly Rental Report', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->Ln(10);
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

// Create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Weekly Rental Report');
$pdf->SetSubject('Weekly Rental Report');
$pdf->SetKeywords('TCPDF, PDF, rental, report');

// Set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' Weekly Rental Report', PDF_HEADER_STRING);

// Set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Set font
$pdf->SetFont('helvetica', '', 10);

// Fetch data from the database
include_once 'includes/config.php'; // Include database connection file

// SQL query to fetch rental data
$sql = "SELECT
            `tblusers`.`FullName`,
            `tblvehicles`.`VehiclesTitle`,
            `tblbooking`.`PostingDate`,
            `tblvehicles`.`PricePerDay`,
            SUM(DATEDIFF(`tblbooking`.`ToDate`, `tblbooking`.`FromDate`)) AS `NumberOfDays`,
            SUM(`tblbooking`.`totalrentals`) AS `totalrentals`
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
            `tblusers`.`FullName`, `tblvehicles`.`VehiclesTitle`, `tblvehicles`.`PricePerDay`, `tblbooking`.`PostingDate`
        ORDER BY
            `tblbooking`.`PostingDate`";

$query = $dbh->prepare($sql);
$yearDropdown = isset($_GET['yearDropdown']) ? $_GET['yearDropdown'] : null;
$monthDropdown = isset($_GET['monthDropdown']) ? $_GET['monthDropdown'] : null;
$startWeekDropdown = isset($_GET['startWeekDropdown']) ? $_GET['startWeekDropdown'] : null;
$endWeekDropdown = isset($_GET['endWeekDropdown']) ? $_GET['endWeekDropdown'] : null;


// Bind parameters
$query->bindParam(':selectedYear', $_GET['yearDropdown'], PDO::PARAM_STR);
$query->bindParam(':selectedMonth', $_GET['monthDropdown'], PDO::PARAM_STR);
$query->bindParam(':selectedStartWeek', $_GET['startWeekDropdown'], PDO::PARAM_STR);
$query->bindParam(':selectedEndWeek', $_GET['endWeekDropdown'], PDO::PARAM_STR);

// Execute the query
$query->execute();

// Check if there are results
if ($query->rowCount() > 0) {
    // Add a page
    $pdf->AddPage();

    // Set table headers
    $pdf->SetFillColor(255, 255, 0);
    $pdf->Cell(20, 10, '#', 1, 0, 'C', 1);
    $pdf->Cell(50, 10, 'Name', 1, 0, 'C', 1);
    $pdf->Cell(60, 10, 'Car Name', 1, 0, 'C', 1);
    $pdf->Cell(40, 10, 'Posting Date', 1, 0, 'C', 1);
    $pdf->Cell(30, 10, 'Price Per Day', 1, 0, 'C', 1);
    $pdf->Cell(30, 10, 'Days Rented', 1, 0, 'C', 1);
    $pdf->Cell(40, 10, 'Rent Total', 1, 1, 'C', 1);

    $num = 0; // Initialize counter

    // Loop through each row
    while ($row = $query->fetch(PDO::FETCH_OBJ)) {
        $num++;
        // Output data to PDF
        $pdf->Cell(20, 10, $num, 1, 0, 'C');
        $pdf->Cell(50, 10, $row->FullName, 1, 0, 'C');
        $pdf->Cell(60, 10, $row->VehiclesTitle, 1, 0, 'C');
        $pdf->Cell(40, 10, $row->PostingDate, 1, 0, 'C');
        $pdf->Cell(30, 10, $row->PricePerDay, 1, 0, 'C');
        $pdf->Cell(30, 10, $row->NumberOfDays, 1, 0, 'C');
        $pdf->Cell(40, 10, $row->totalrentals, 1, 1, 'C');
    }
} else {
    // No rentals found
    $pdf->Cell(0, 10, "No rentals found", 0, 1);
}

// Output PDF to the browser
$pdf->Output('weekly_rental_report.pdf', 'I');
?>
