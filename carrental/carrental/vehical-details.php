<?php
session_start();
include('includes/config.php');
error_reporting(0);
if (isset($_POST['submit'])) {

  $fromdate = $_POST['fromdate'];
  $todate = $_POST['todate'];
  $message = $_POST['message'];
  $total = $_POST['total'];
  $useremail = $_SESSION['login'];
  $status = 0;
  $vhid = $_GET['vhid'];
  // $vhid = isset($_GET['vhid']) ? $_GET['vhid'] : null;
  $bookingno = mt_rand(100000000, 999999999);
  $ret = "SELECT * FROM tblbooking where (:fromdate BETWEEN date(FromDate) and date(ToDate) ||
 :todate BETWEEN date(FromDate) and date(ToDate) || date(FromDate) BETWEEN :fromdate and :todate) and VehicleId=:vhid and status!='2'";
  $query1 = $dbh->prepare($ret);
  $query1->bindParam(':vhid', $vhid, PDO::PARAM_STR);
  $query1->bindParam(':fromdate', $fromdate, PDO::PARAM_STR);
  $query1->bindParam(':todate', $todate, PDO::PARAM_STR);
  $query1->execute();
  $results1 = $query1->fetchAll(PDO::FETCH_OBJ);
  $statusBooking = '';
  if ($query1->rowCount() == 0) {
    $sql = "INSERT INTO tblbooking(BookingNumber,userEmail,VehicleId,FromDate,ToDate,totalrentals,message,Status) VALUES(:bookingno,:useremail,:vhid,:fromdate,:todate,:total,:message,:status)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':bookingno', $bookingno, PDO::PARAM_STR);
    $query->bindParam(':useremail', $useremail, PDO::PARAM_STR);
    $query->bindParam(':vhid', $vhid, PDO::PARAM_STR);
    $query->bindParam(':fromdate', $fromdate, PDO::PARAM_STR);
    $query->bindParam(':todate', $todate, PDO::PARAM_STR);
    $query->bindParam(':total', $total, PDO::PARAM_STR);
    $query->bindParam(':message', $message, PDO::PARAM_STR);
    $query->bindParam(':status', $status, PDO::PARAM_STR);
    $query->execute();
    $lastInsertId = $dbh->lastInsertId();
    if ($lastInsertId) {
      echo "<script>alert('Booking successfull.');</script>";
      echo "<script type='text/javascript'> document.location = 'my-booking.php'; </script>";
    } else {
      echo "<script>alert('Something went wrong. Please try again');</script>";
      echo "<script type='text/javascript'> document.location = 'car-listing.php'; </script>";
    }
  } else {

    $statusBooking = 'CAR ALREADY BOOKED FOR THESE DAYS';
    // echo "<script>alert('Car already booked for these days');</script>"; 
    // echo "<script type='text/javascript'> document.location = 'car-listing.php'; </script>";
  }
}
?>


<?php
$vhid = $_GET['vhid'];

$ret = "SELECT `FromDate`, `ToDate` FROM tblbooking WHERE VehicleId = :vhid AND (Status = 0 or status = 1)";
$query1 = $dbh->prepare($ret);
$query1->bindParam(':vhid', $vhid, PDO::PARAM_STR);
$query1->execute();
$results1 = $query1->fetchAll(PDO::FETCH_OBJ);

$bookedDateRanges = array();

foreach ($results1 as $result) {
  $fromDate = new DateTime($result->FromDate);
  $toDate = new DateTime($result->ToDate);

  // Add booked date range to the array
  $bookedDateRanges[] = array(
    'start' => $fromDate->format('Y-m-d'),
    'end' => $toDate->format('Y-m-d')
  );
}

// Convert PHP array to JavaScript array using json_encode
$bookedDateRangesJSON = json_encode($bookedDateRanges);
?>

<!DOCTYPE HTML>
<html lang="en">

<head>

  <title>Car Rental | Vehicle Details</title>
  <!--Bootstrap -->
  <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
  <!--Custome Style -->
  <link rel="stylesheet" href="assets/css/style.css" type="text/css">
  <!--OWL Carousel slider-->
  <link rel="stylesheet" href="assets/css/owl.carousel.css" type="text/css">
  <link rel="stylesheet" href="assets/css/owl.transitions.css" type="text/css">
  <!--slick-slider -->
  <link href="assets/css/slick.css" rel="stylesheet">
  <!--bootstrap-slider -->
  <link href="assets/css/bootstrap-slider.min.css" rel="stylesheet">
  <!--FontAwesome Font Style -->
  <link href="assets/css/font-awesome.min.css" rel="stylesheet">

  <!-- SWITCHER -->
  <link rel="stylesheet" id="switcher-css" type="text/css" href="assets/switcher/css/switcher.css" media="all" />
  <link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/red.css" title="red" media="all" data-default-color="true" />
  <link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/orange.css" title="orange" media="all" />
  <link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/blue.css" title="blue" media="all" />
  <link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/pink.css" title="pink" media="all" />
  <link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/green.css" title="green" media="all" />
  <link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/purple.css" title="purple" media="all" />
  <link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/images/favicon-icon/apple-touch-icon-144-precomposed.png">
  <link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/images/favicon-icon/apple-touch-icon-114-precomposed.html">
  <link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/images/favicon-icon/apple-touch-icon-72-precomposed.png">
  <link rel="apple-touch-icon-precomposed" href="assets/images/favicon-icon/apple-touch-icon-57-precomposed.png">
  <link rel="shortcut icon" href="assets/images/favicon-icon/favicon.png">
  <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900" rel="stylesheet">
</head>

<body>

  <style>
    .booked-date {
      background-color: #ff8080;
      /* Change the color as needed */
      color: white;
      font-weight: bold;
    }
  </style>

  <!-- Start Switcher -->
  <?php include('includes/colorswitcher.php'); ?>
  <!-- /Switcher -->
  <!--Header-->
  <?php include('includes/header.php'); ?>
  <!-- /Header -->
  <!--Listing-Image-Slider-->
  <?php
  $vhid = intval($_GET['vhid']);
  $sql = "SELECT tblvehicles.*,tblbrands.BrandName,tblbrands.id as bid  from tblvehicles join tblbrands on tblbrands.id=tblvehicles.VehiclesBrand where tblvehicles.id=:vhid";
  $query = $dbh->prepare($sql);
  $query->bindParam(':vhid', $vhid, PDO::PARAM_STR);
  $query->execute();
  $results = $query->fetchAll(PDO::FETCH_OBJ);
  $cnt = 1;
  if ($query->rowCount() > 0) {
    foreach ($results as $result) {
      $_SESSION['brndid'] = $result->bid;
  ?>


      <!--/Listing-Image-Slider-->


      <!--Listing-detail-->
      <section class="listing-detail">
        <div class="container">
          <div class="listing_detail_head row">
            <div class="col-md-9">
              <h2><?php echo htmlentities($result->BrandName); ?> , <?php echo htmlentities($result->VehiclesTitle); ?></h2>
            </div>
            <div class="col-md-3">
              <div class="price_info">
                <p>₱<?php echo htmlentities($result->PricePerDay); ?> </p>Per Day

              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-9">

              <div>
                <div style="text-align: center;">

                  <h4 id="monthAndYear"></h4>
                  <button onclick="prevMonth()" style="padding: 5px 10px; font-size: 12px; background-color: #4CAF50; color: white;">&larr; Previous Month</button>
                  <button onclick="nextMonth()" style="padding: 5px 10px; font-size: 12px; background-color: #4CAF50; color: white;">Next Month &rarr;</button>

                  </br></br>
                </div>


                <table style="border:#4CAF50">
                  <thead>
                    <tr>
                      <th>Sun</th>
                      <th>Mon</th>
                      <th>Tue</th>
                      <th>Wed</th>
                      <th>Thu</th>
                      <th>Fri</th>
                      <th>Sat</th>
                    </tr>
                  </thead>
                  <tbody id="calendarBody"></tbody>
                </table>
              </div>


              <script>
                var bookedDateRanges = <?php echo $bookedDateRangesJSON; ?>;
                console.log(bookedDateRanges);
                let currentYear = 2024;
                let currentMonth = 1;

                function generateCalendar(year, month, bookedDateRanges) {
                  const firstDay = new Date(year, month - 1, 1);
                  const lastDay = new Date(year, month, 0);
                  const daysInMonth = lastDay.getDate();
                  const startingDay = firstDay.getDay();

                  const monthAndYearElement = document.getElementById('monthAndYear');
                  monthAndYearElement.innerText = `${getMonthName(month)} ${year}`;

                  const calendarBody = document.getElementById('calendarBody');
                  calendarBody.innerHTML = '';

                  let date = 1;

                  for (let i = 0; i < 6; i++) {
                    const row = document.createElement('tr');

                    for (let j = 0; j < 7; j++) {
                      const cell = document.createElement('td');
                      if (i === 0 && j < startingDay) {
                        // Empty cells before the first day of the month
                        cell.innerText = '';
                      } else if (date > daysInMonth) {
                        // Empty cells after the last day of the month
                        cell.innerText = '';
                      } else {
                        cell.innerText = date;

                        // Check if the date is booked and mark it as booked
                        const currentDate = new Date(year, month - 1, date);
                        if (isDateBooked(currentDate, bookedDateRanges)) {
                          cell.classList.add('booked-date');
                        }

                        date++;
                      }
                      row.appendChild(cell);
                    }

                    calendarBody.appendChild(row);
                  }
                }

                // Function to check if a date is booked
                function isDateBooked(currentDate, bookedDateRanges) {
                  const currentYear = currentDate.getFullYear();
                  const currentMonth = currentDate.getMonth() + 1; // Month is zero-indexed
                  const currentDay = currentDate.getDate();

                  const currentDateString =
                    `${currentYear}-${currentMonth < 10 ? '0' : ''}${currentMonth}-${currentDay < 10 ? '0' : ''}${currentDay}`;

                  for (let i = 0; i < bookedDateRanges.length; i++) {
                    const startDate = bookedDateRanges[i]['start'];
                    const endDate = bookedDateRanges[i]['end'];

                    // Ensure that the current date is within the booked date range without adding 1 day to the end date
                    if (
                      currentDateString >= startDate &&
                      currentDateString <= endDate
                    ) {
                      return true;
                    }
                  }
                  return false;
                }

                function getMonthName(month) {
                  const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September',
                    'October', 'November', 'December'
                  ];
                  return months[month - 1];
                }

                function updateCalendar() {
                  generateCalendar(currentYear, currentMonth, bookedDateRanges);
                }

                function nextMonth() {
                  if (currentMonth === 12) {
                    currentMonth = 1;
                    currentYear++;
                  } else {
                    currentMonth++;
                  }
                  updateCalendar();
                }

                function prevMonth() {
                  if (currentMonth === 1) {
                    currentMonth = 12;
                    currentYear--;
                  } else {
                    currentMonth--;
                  }
                  updateCalendar();
                }

                // Initial calendar for January 2024
                updateCalendar();
              </script>



              <div class="listing_more_info">
                <div class="listing_detail_wrap">
                  <!-- Nav tabs -->
                  <ul class="nav nav-tabs gray-bg" role="tablist">
                    <!-- <li role="presentation" class="active"><a href="#vehicle-overview " aria-controls="vehicle-overview" role="tab" data-toggle="tab">Vehicle Overview </a></li> -->
                    <li role="presentation"><a href="#accessories" aria-controls="accessories" role="tab" data-toggle="tab">Accessories</a></li>
                    <li role="presentation" class="active-tab"><a href="#CarFeatures" aria-controls="CarFeatures" role="tab" data-toggle="tab">Car Features & Overview</a></li> 
                  </ul>

                  <!-- Tab panes -->
                  <div class="tab-content">
                    <!-- vehicle-overview -->
                    <!-- <div role="tabpanel" class="tab-pane " id="vehicle-overview">

                      <p>?php echo htmlentities($result->VehiclesOverview); ?></p>
                    </div> -->

                    <div id="CarFeatures" role="tabpanel" class="tab-pane main_features active">
                      <ul class="features_list">
                        <li class="feature_item">
                          <i class="fa fa-calendar" aria-hidden="true"></i>
                          <div class="feature_content">
                            <h5><?php echo htmlentities($result->ModelYear); ?></h5>
                            <p>Model Year</p>
                          </div>
                        </li>
                        <li class="feature_item">
                          <i class="fa fa-cogs" aria-hidden="true"></i>
                          <div class="feature_content">
                            <h5><?php echo htmlentities($result->FuelType); ?></h5>
                            <p>Fuel Type</p>
                          </div>
                        </li>
                        <li class="feature_item">
                          <i class="fa fa-car" aria-hidden="true"></i>
                          <div class="feature_content">
                            <h5><?php echo htmlentities($result->Category); ?></h5>
                            <p>Transmission</p>
                          </div>
                        </li>
                        <li class="feature_item">
                          <i class="fa fa-cog" aria-hidden="true"></i>
                          <div class="feature_content">
                            <h5><?php echo htmlentities($result->HP); ?></h5>
                            <p>HP</p>
                          </div>
                        </li>
                        <li class="feature_item" style="margin-bottom: 20px;">
                          <i class="fa fa-user-plus" aria-hidden="true"></i>
                          <div class="feature_content">
                            <h5><?php echo htmlentities($result->SeatingCapacity); ?></h5>
                            <p>Seats</p>
                          </div>
                        </li>

                        <li class="feature_item">
                          <i class="fa fa-tachometer" aria-hidden="true"></i>
                          <div class="feature_content">
                            <h5><?php echo htmlentities($result->FullTank); ?></h5>
                            <p>Fuel Capacity</p>
                          </div>
                        </li>
                      </ul>
                    </div>
                    <!-- Accessories -->
                    <div role="tabpanel" class="tab-pane" id="accessories">
                      <!-- Accessories -->
                      <table>
                        <thead>
                          <tr>
                            <th colspan="2">Accessories</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          function getIconClass($value)
                          {
                            return $value == 1 ? 'fa fa-check' : 'fa fa-times';
                          }

                          $accessoriesData = array(
                            'Air Conditioner' => 'fa  fa-snowflake-o',
                            'AntiLock Braking System' => 'fa fa-bus',
                            'Power Steering' => 'fa fa-street-view',
                            'Power Windows' => 'fa fa-window-maximize',
                            'CD Player' => 'fa fa-music',
                            'Leather Seats' => 'fa fa-car',
                            'Central Locking' => 'fa fa-lock',
                            'Power Door Locks' => 'fa fa-key',
                            'Brake Assist' => 'fa fa-car',
                            'Driver Airbag' => 'fa fa-user',
                            'Passenger Airbag' => 'fa fa-user',
                            'Crash Sensor' => 'fa fa-exclamation-triangle',
                          );


                          foreach ($accessoriesData as $accessory => $iconClass) :
                            $propertyName = str_replace(' ', '', $accessory);
                            $propertyValue = $result->{$propertyName};
                          ?>
                            <tr>
                              <td><i class="<?php echo $iconClass; ?>" aria-hidden="true"></i> <?php echo $accessory; ?></td>
                              <td><i class="<?php echo getIconClass($propertyValue); ?>" aria-hidden="true"></i></td>
                            </tr>
                          <?php endforeach; ?>

                        </tbody>
                      </table>
                    </div>

                    <!-- <style>
                      #gallery-container {
                        text-align: center;
                        padding: 20px;
                        background-color: #fff;
                        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                        margin: 20px auto;
                        max-width: 900px;
                      }

                      #listing_img_slider {
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        margin-top: 20px;
                        overflow: hidden;
                      }

                      #listing_img_slider img {
                        max-width: 100%;
                        height: 200px;
                        /* Set a fixed height for all images */
                        object-fit: cover;
                        /* Maintain aspect ratio */
                        border: 2px solid #3498db;
                        /* Change the border color here */
                        border-radius: 5px;
                        margin: 0 10px;
                      }
                    </style> -->

                    <div>
                      <h4>Car Gallery</h4>
                    </div>

                    <section id="listing_img_slider">
                      <?php for ($i = 1; $i <= 5; $i++) : ?>
                        <?php $image = $result->{'Vimage' . $i}; ?>
                        <?php if (!empty($image)) : ?>
                          <div><img src="admin/img/vehicleimages/<?php echo htmlentities($image); ?>" class="img-responsive" alt="image"></div>
                        <?php endif; ?>
                      <?php endfor; ?>
                    </section>
                  </div>
                </div>

              </div>
          <?php }
      } ?>

            </div>

            <aside class="col-md-3">
              <div class="share_vehicle">
                <p>Share: <a href="#"><i class="fa fa-facebook-square" aria-hidden="true"></i></a> <a href="#"><i class="fa fa-twitter-square" aria-hidden="true"></i></a> <a href="#"><i class="fa fa-linkedin-square" aria-hidden="true"></i></a> <a href="#"><i class="fa fa-google-plus-square" aria-hidden="true"></i></a> </p>
              </div>
              <div class="sidebar_widget">
                <?php echo "<p style='color: red; font-weight: bold;'>{$statusBooking}</p>"; ?>
                <div class="widget_heading">
                  <h5><i class="fa fa-envelope" aria-hidden="true"></i><?php echo $statusbooking ?> Book Now</h5>
                </div>
                <form method="post">
                  <div class="hidden price_info">
                    <p>₱<?php echo htmlentities($result->PricePerDay); ?> </p>
                  </div>
                  <div class="form-group">
                    <label>From Date:</label>
                    <input type="date" class="form-control" id="fromDate" name="fromdate" placeholder="From Date" required min="<?php echo date('Y-m-d'); ?>">
                  </div>
                  <div class="form-group">
                    <label>To Date:</label>
                    <input type="date" class="form-control" id="toDate" name="todate" placeholder="To Date" required min="<?php echo date('Y-m-d'); ?>">
                  </div>
                  <div class="form-group">
                    <textarea rows="4" class="form-control" name="Note" placeholder="Note" required></textarea>
                  </div>

                  <div class="form-group">
                    <label>TOTAL:</label>
                    <input type="text" class="form-control" id="total" name="total" placeholder="Total" required>

                  </div>

                  <?php if ($_SESSION['login']) { ?>
                    <div class="form-group">
                      <?php if ($statusBooking) : ?>
                        <input type="button" class="btn" value="Rebook" onclick="reloadPage()">
                      <?php else : ?>
                        <input type="submit" class="btn" name="submit" value="Book Now">
                      <?php endif; ?>
                    </div>

                    <script>
                      function reloadPage() {
                        // Change from location.reload() to window.location.href
                        window.location.href = 'vehical-details.php?vhid=<?php echo isset($_GET['vhid']) ? $_GET['vhid'] : ''; ?>';
                      }
                    </script>


                  <?php } else { ?>
                    <a href="#loginform" class="btn btn-xs uppercase" data-toggle="modal" data-dismiss="modal">Login For
                      Book</a>
                  <?php } ?>
                </form>
              </div>
            </aside>
            <!--/Side-Bar-->
            <script>
              document.getElementById('toDate').addEventListener('change', function() {
                var fromDate = new Date(document.getElementById('fromDate').value + 'T00:00:00');
                var toDate = new Date(this.value + 'T00:00:00');
                var pricePerDay = <?php echo $result->PricePerDay; ?>;
                var totalDays = Math.ceil((toDate - fromDate) / (1000 * 60 * 60 * 24));
                var total = pricePerDay * totalDays;

                if (!isNaN(total) && total >= 0) {
                  document.getElementById('total').value = total;
                } else {
                  document.getElementById('total').value = '';
                }
              });
            </script>
          </div>

          <div class="space-20"></div>
          <div class="divider"></div>

          <!--Similar-Cars-->
          <div class="similar_cars">
            <h3>Similar Cars</h3>
            <div class="row">
              <?php
              $bid = $_SESSION['brndid'];
              $sql = "SELECT tblvehicles.VehiclesTitle,tblbrands.BrandName,tblvehicles.PricePerDay,
            tblvehicles.FuelType,tblvehicles.ModelYear,tblvehicles.id,tblvehicles.SeatingCapacity,tblvehicles.Category,tblvehicles.FullTank,
            tblvehicles.VehiclesOverview,tblvehicles.Vimage1 from tblvehicles join tblbrands on 
            tblbrands.id=tblvehicles.VehiclesBrand where tblvehicles.VehiclesBrand=:bid";
              $query = $dbh->prepare($sql);
              $query->bindParam(':bid', $bid, PDO::PARAM_STR);
              $query->execute();
              $results = $query->fetchAll(PDO::FETCH_OBJ);
              $cnt = 1;
              if ($query->rowCount() > 0) {
                foreach ($results as $result) { ?>
                  <div class="col-md-3 grid_listing">
                    <div class="product-listing-m gray-bg">
                      <!-- <style>
                        .product-listing-img {
                          width: 200px;
                          /* Set your desired width */
                          height: 150px;
                          /* Set your desired height */
                          overflow: hidden;
                          /* Ensure that content doesn't overflow */
                        }

                        .product-listing-img img {
                          width: 100%;
                          /* Make sure the image fills the container */
                          height: 100%;
                          /* Make sure the image fills the container */
                          object-fit: cover;
                          /* Maintain aspect ratio and cover the container */
                        }
                      </style> -->

                      <!-- Your HTML code with fixed size for the product-listing-img div -->
                      <div class="product-listing-img">
                        <a href="vehical-details.php?vhid=<?php echo htmlentities($result->id); ?>">
                          <img src="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage1); ?>" class="img-responsive" alt="image" />
                        </a>
                      </div>

                      <div class="product-listing-content">
                        <h6><a href="vehical-details.php?vhid=<?php echo htmlentities($result->id); ?>"><?php echo htmlentities($result->BrandName); ?>
                            , <?php echo htmlentities($result->VehiclesTitle); ?></a></h6>
                        <p class="list-price">₱<?php echo htmlentities($result->PricePerDay); ?></p>

                        <ul class="features_list">

                          <li><i class="fa fa-user" aria-hidden="true"></i><?php echo htmlentities($result->SeatingCapacity); ?>
                            seats</li>
                            <li><i class="fa fa-car" aria-hidden="true"></i><?php echo htmlentities($result->Category); ?>
                            </li>
                          <li><i class="fa fa-calendar" aria-hidden="true"></i><?php echo htmlentities($result->ModelYear); ?>
                            model</li>
                          <li><i class="fa fa-tint" aria-hidden="true"></i><?php echo htmlentities($result->FuelType); ?></li>
                          <li><i class="fa fa-tachometer" aria-hidden="true"></i><?php echo htmlentities($result->FullTank); ?>L Fuel Capacity</li>
                        </ul>
                      </div>
                    </div>
                  </div>
              <?php }
              } ?>

            </div>
          </div>
          <!--/Similar-Cars-->

        </div>
      </section>
      <!--/Listing-detail-->

      <!--Footer -->
      <?php include('includes/footer.php'); ?>
      <!-- /Footer-->

      <!--Back to top-->
      <div id="back-top" class="back-top"> <a href="#top"><i class="fa fa-angle-up" aria-hidden="true"></i> </a> </div>
      <!--/Back to top-->

      <!--Login-Form -->
      <?php include('includes/login.php'); ?>
      <!--/Login-Form -->

      <!--Register-Form -->
      <?php include('includes/registration.php'); ?>

      <!--/Register-Form -->

      <!--Forgot-password-Form -->
      <?php include('includes/forgotpassword.php'); ?>

      <script src="assets/js/jquery.min.js"></script>
      <script src="assets/js/bootstrap.min.js"></script>
      <script src="assets/js/interface.js"></script>
      <script src="assets/switcher/js/switcher.js"></script>
      <script src="assets/js/bootstrap-slider.min.js"></script>
      <script src="assets/js/slick.min.js"></script>
      <script src="assets/js/owl.carousel.min.js"></script>

</body>

</html>