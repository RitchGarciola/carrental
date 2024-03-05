<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once('dompdf/autoload.inc.php');
include_once 'generateReport.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->setChroot(__DIR__);
$options->setIsRemoteEnabled(true);

$title = "";
$title2 = "sdsd";
$titlePage = "";


if (isset($_GET['generate'])) {
    $generate = $_GET['generate'];
    $transaction_obj = new Report($dbh);


    $date = "";

    if ($generate == 0) {
        $startDate = $_GET['startDate'];
        $endDate = $_GET['endDate'];
        $results = $transaction_obj->getBookingsSpecificRangeOfDate($startDate, $endDate);
        if ($results == null) {
            header('location: ReportClass.php');
            $_SESSION['no_history'] = "There is no transaction history from " . $startDate . " to " . $endDate . " to generate a report.";
            exit();
        }
        $titlePage = "Transaction History(" . $startDate . "-" . $endDate . ")";
        $timestamp = strtotime($startDate);
        $formattedStartDate = date('F j, Y', $timestamp);
        $timestamp = strtotime($endDate);
        $formattedEndDate = date('F j, Y', $timestamp);
        $date = $formattedStartDate . ' - ' . $formattedEndDate;
    } else {
        $results = $transaction_obj->getAllSuccessBookings();
        // var_dump($results);
        // return;
        if ($results == null) {
            header('location:ReportClass.php');
            $_SESSION['no_history'] = "There is no transaction history to generate a report.";
            exit();
        }

        $titlePage = "Income Report";
        $date = date('F Y');
    }

    $title = "Transaction History.pdf";
    $title2 = "Transaction History - Stanley's Car Rental";

    $amountRent = 0.00;

    // Calculate total
    $totalAmount = $amountRent;

    $dompdf = new Dompdf($options);

    $dompdf->setPaper("A4", "landscape");

    // Define HTML content
    $html = '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Transaction History</title>
        
            <style>
                body {
                    font-family: Arial, sans-serif;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    height: 100vh; /* 100% of the viewport height */
                    margin: 0;
                }
        
                .container {
                    display: flex;
                    justify-content: space-between;
                    width: 80%;
                    margin-top: 20px;
                }
        
                img {
                    height: 200px;
                    width: 200px;
                    /* Center the image horizontally */
                    display: block;
                    margin-left: auto;
                    margin-right: auto;
                    margin-top: 0px;
                }
        
                p {
                    font-size: 15px;
                    margin-bottom: 20px;
                }
        
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 20px;
                }
        
                th, td {
                    border: 1px solid black;
                    padding: 10px;
                    text-align: left;
                }
        
                th {
                    background-color: #f2f2f2;
                }
        
                .text-center {
                    text-align: center;
                }
        
                .total-row {
                    font-weight: bold;
                }
        
                .underline {
                    border-bottom: 1px solid black;
                    display: inline-block;
                }
        
                .body1 {
                    font-size: 16px;
                    margin-bottom: 0;
                }
        
                .body2 {
                    font-size: 14px;
                    margin-top: 5px;
                    margin-bottom: 0;
                    position: relative;
                    top: -5px;
                }
        
                .container {
                    display: flex;
                    justify-content: space-between;
                    width: 100%;
                    margin-top: 150px;
                    margin-left: auto;
                    margin-right: auto;
                }
        
                .left {
                    display: inline-block;
                    width: 49%;
                    text-align: start;
                }
        
                .right {
                    display: inline-block;
                    width: 49%;
                    text-align: end;
                }
            </style>
        </head>
        <body>
        
            <div style=" margin-bottom: 40px;text-align: center;">
                <img src="img/Stanley_Logo.png" alt="img/Stanley_Logo.png">
                <p style="font-size: 20px; margin-bottom: 0;">
                    <strong style="display: block; ">Stanley Car Rental Income Report <br>
                    <strong style="display: block; ">Region V Sorsogon Branch<br>
                    </strong>
                </p>
            </div>
            <p><strong>Income Report</strong> <br> Date: <strong>' . $date . '</strong></p>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center">Name</th>
                        <th class="text-center">Car Name</th>
                        <th class="text-center">Posting Date</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-center">' . number_format($amountRent, 2) . '</td>
                    </tr>
    
                    <tr class="total-row">
                    <td>Total Amount</td>
                    <td colspan="3"></td>
                    <td class="text-center">' . number_format($totalAmount, 2) . '</td>
                    </tr>
                </tbody>
            </table>
        </body>
        </html>
    ';

    $dompdf->loadHtml($html);
    $dompdf->render(); // generates pdf in memory
    $dompdf->addInfo("Title", 'Transaction History');

    $dompdf->stream('Income Report.pdf');
    //  ["Attachment" => 0]
 }
            ?>