<?php

include('includes/config.php');
include_once('Report.php');

class Report
{
    private $connection; // Add a property to store the database connection

    public function __construct($dbh)
    {
        $this->connection = $dbh;
    }

    function getAllSuccessBookings()
    {
        try {
            $status = 1; // confirmed
            $status1 = 2; // returned

            $query = "SELECT 
                    u.FullName,
                    b.BrandName,
                    v.VehiclesTitle,
                    tb.FromDate,
                    tb.ToDate,
                    tb.message,
                    tb.VehicleId as vid,
                    tb.Status,
                    tb.PostingDate,
                    tb.id
                FROM tblbooking AS tb
                JOIN tblvehicles AS v ON tb.VehicleId = v.id
                JOIN tblusers AS u ON u.EmailId = tb.userEmail
                JOIN tblbrands AS b ON v.VehiclesBrand = b.id
                WHERE tb.Status = :status OR tb.Status = :status1;";
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':status1', $status1);
            $stmt->execute();
            $bookingHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($bookingHistory != null) {
                return $bookingHistory;
            }
            return null;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return null;
        }
    }

    function getBookingsSpecificRangeOfDate($startDate, $endDate)
    {
        try {
            $status = 1; // confirmed
            $status1 = 2; // returned
            $query = "SELECT 
                    u.FullName,
                    b.BrandName,
                    v.VehiclesTitle,
                    tb.FromDate,
                    tb.ToDate,
                    tb.message,
                    tb.VehicleId as vid,
                    tb.Status,
                    tb.PostingDate,
                    tb.id
                FROM tblbooking AS tb
                JOIN tblvehicles AS v ON tb.VehicleId = v.id
                JOIN tblusers AS u ON u.EmailId = tb.userEmail
                JOIN tblbrands AS b ON v.VehiclesBrand = b.id
                WHERE (tb.Status = :status OR tb.Status = :status1) AND (tb.PostingDate BETWEEN :startDate AND :endDate)";
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':status1', $status1);
            $stmt->bindParam(':startDate', $startDate, PDO::PARAM_STR);
            $stmt->bindParam(':endDate', $endDate, PDO::PARAM_STR);
            $stmt->execute();
            $bookingHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($bookingHistory != null) {
                return $bookingHistory;
            }
            return null;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return null;
        }
    }

    // end functions
}

// Create an instance of the Report class and pass the $dbh connection
$report = new Report($dbh);

?>
