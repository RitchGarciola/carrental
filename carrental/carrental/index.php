<?php
session_start();
include('includes/config.php');
error_reporting(0);

?>

<!DOCTYPE HTML>
<html lang="en">

<head>

  <title>Car Rental Portal</title>
  <!--Bootstrap -->
  <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
  <link rel="stylesheet" href="assets/css/style.css" type="text/css">
  <link rel="stylesheet" href="assets/css/owl.carousel.css" type="text/css">
  <link rel="stylesheet" href="assets/css/owl.transitions.css" type="text/css">
  <link href="assets/css/slick.css" rel="stylesheet">
  <link href="assets/css/bootstrap-slider.min.css" rel="stylesheet">
  <link href="assets/css/font-awesome.min.css" rel="stylesheet">
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

  <!-- Start Switcher -->
  <?php include('includes/colorswitcher.php'); ?>
  <!-- /Switcher -->

  <!--Header-->
  <?php include('includes/header.php'); ?>
  <!-- /Header -->

  <!-- Banners -->
  <section id="banner" class="banner-section">
    <div class="container">
      <div class="div_zindex">
        <div class="row">
          <div class="col-md-5 col-md-push-7">
            <div class="banner_content">
              <h1>&nbsp;</h1>
              <p>&nbsp; </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- /Banners -->


  <!-- Resent Cat-->
  <section class="section-padding gray-bg">
    <div class="container">
      <div class="section-header text-center">
        <h2>Rent The Perfect Car <span>For Your Adventure!</span></h2>
        <p>Unleash the freedom of the open road with our wide selection of rental cars. Whether you're planning a
          weekend gateaway, a business trip, or just need a temporary ride, we've got you covered.</p>
      </div>
      <div class="row">

        <!-- Nav tabs -->
        <div class="recent-tab">
          <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#resentnewcar" role="tab" data-toggle="tab">New Car</a></li>
          </ul>
        </div>
        <!-- Recently Listed New Cars -->
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="resentnewcar">
            <?php $sql = "SELECT tblvehicles.VehiclesTitle,tblbrands.BrandName,tblvehicles.PricePerDay,tblvehicles.FuelType,
            tblvehicles.ModelYear,tblvehicles.id,tblvehicles.SeatingCapacity,tblvehicles.Category,tblvehicles.HP,tblvehicles.FullTank,tblvehicles.VehiclesOverview,tblvehicles.Vimage1 
            from tblvehicles join tblbrands on tblbrands.id=tblvehicles.VehiclesBrand limit 9";
            $query = $dbh->prepare($sql);
            $query->execute();
            $results = $query->fetchAll(PDO::FETCH_OBJ);
            $cnt = 1;
            if ($query->rowCount() > 0) {
              foreach ($results as $result) {

                $vehiclesTitle =  htmlentities($result->VehiclesTitle);

                $ret = "SELECT 
                  tblvehicles.VehiclesTitle,
                  tblbrands.BrandName,
                  tblvehicles.PricePerDay,
                  tblvehicles.FuelType,
                  tblvehicles.ModelYear,
                  tblvehicles.id,
                  tblvehicles.SeatingCapacity,
                  tblvehicles.VehiclesOverview,
                  tblvehicles.Vimage1,
                  tblbooking.FromDate,
                  tblbooking.ToDate
              FROM
                  `tblbooking`
              JOIN
                  `tblvehicles` ON (`tblbooking`.`VehicleId` = `tblvehicles`.`id`)
              JOIN
                  `tblbrands` ON (`tblvehicles`.`VehiclesBrand` = `tblbrands`.`id`)  
              WHERE
                  tblvehicles.VehiclesTitle = :vehiclesTitle
                  AND (tblbooking.Status = 3 AND tblvehicles.carStatus = 'Under Maintenance')";

                $query1 = $dbh->prepare($ret);
                $query1->bindParam(':vehiclesTitle', $vehiclesTitle, PDO::PARAM_STR);
                $query1->execute();
                $results = $query1->fetchAll(PDO::FETCH_OBJ);
                $statusBooking = '';

                if ($query1->rowCount() > 0) {
                  $statusBooking = 'Car Unavailable - Under Maintenance';
                }


            ?>
                <div class="col-list-3">
                  <div class="recent-car-list">
                    <div class="car-info-box">
                      <?php
                      $statusBookingClass = ($statusBooking == 'Car Unavailable - Under Maintenance') ? 'unavailable' : 'available';
                      ?>
                      <?php if ($statusBooking == 'Car Unavailable - Under Maintenance') {
                      ?>
                        <a href="javascript:alert('Car Unavailable - Under Maintenance');" class="watermarked-link">
                          <div class="status-container <?php echo $statusBookingClass; ?>">
                            <p><?php echo $statusBooking; ?></p>
                          </div>
                          <img src="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage1); ?>" class="img-responsive equal-size-image" alt="image">
                        </a>
                      <?php } else { ?>
                        <a href="vehical-details.php?vhid=<?php echo htmlentities($result->id); ?>">
                          <img src="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage1); ?>" class="img-responsive equal-size-image" alt="image">
                        </a>

                      <?php } ?>

                      <ul>
                        <li><i class="fa fa-tint" aria-hidden="true"></i><?php echo htmlentities($result->FuelType); ?></li>
                        <li><i class="fa fa-calendar" aria-hidden="true"></i><?php echo htmlentities($result->ModelYear); ?>
                          Model</li>
                        <li><i class="fa fa-user" aria-hidden="true"></i><?php echo htmlentities($result->SeatingCapacity); ?> seats</li>
                        <li><i class="fa fa-car" aria-hidden="true"></i><?php echo htmlentities($result->Category); ?> </li>
                        <li><i class="fa fa-cogs" aria-hidden="true"></i><?php echo htmlentities($result->HP); ?> HP</li>
                        <li><i class="fa fa-tachometer" aria-hidden="true"></i><?php echo htmlentities($result->FullTank); ?>L Fuel Capacity</li>
                      </ul>
                    </div>
                    <div class="car-title-m">
                      <h6><a href="vehical-details.php?vhid=<?php echo htmlentities($result->id); ?>">
                          <?php echo htmlentities($result->VehiclesTitle); ?></a></h6>
                      <span class="price">₱<?php echo htmlentities($result->PricePerDay); ?> /Day</span>
                    </div>
                    <div class="inventory_info_m">
                      <p><?php echo substr($result->VehiclesOverview, 0, 70); ?></p>
                    </div>


                  </div>
                </div>
            <?php   }
            } ?>

          </div>
        </div>
      </div>
  </section>
  <!-- /Resent Cat -->

  <style>
    .status-container {
      text-align: center;
    }

    .available {
      color: green;
      font-weight: bold;
    }

    .unavailable {
      color: red;
      font-weight: bold;
    }

    .equal-size-image {

      height: 250px;
      /* Adjust the height as needed */
      object-fit: cover;
      /* Preserve aspect ratio and cover container */
    }
  </style>



  <!-- Fun Facts
<section class="fun-facts-section">
  <div class="container div_zindex">
    <div class="row">
      <div class="col-lg-3 col-xs-6 col-sm-3">
        <div class="fun-facts-m">
          <div class="cell">
            <h2><i class="fa fa-calendar" aria-hidden="true"></i>40+</h2>
            <p>Years In Business</p>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-xs-6 col-sm-3">
        <div class="fun-facts-m">
          <div class="cell">
            <h2><i class="fa fa-car" aria-hidden="true"></i>1200+</h2>
            <p>New Cars For Sale</p>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-xs-6 col-sm-3">
        <div class="fun-facts-m">
          <div class="cell">
            <h2><i class="fa fa-car" aria-hidden="true"></i>1000+</h2>
            <p>Used Cars For Sale</p>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-xs-6 col-sm-3">
        <div class="fun-facts-m">
          <div class="cell">
            <h2><i class="fa fa-user-circle-o" aria-hidden="true"></i>600+</h2>
            <p>Satisfied Customers</p>
          </div>
        </div>
      </div>
    </div>
  </div>
 Dark Overlay
  <div class="dark-overlay"></div>
</section>  -->
  <!-- /Fun Facts-->

  <style>
    .watermarked-link {
      position: relative;
      display: inline-block;
    }

    .watermarked-link .status-container {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(255, 255, 255, 0.8);
      /* Adjust the alpha value for transparency */
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
    }

    .watermarked-link .status-container p {
      margin: 0;
      font-size: 18px;
      /* Adjust the font size as needed */
      color: red;
      /* Adjust the font color as needed */
      /* Additional styling if needed */
    }
  </style>


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
  <!--/Forgot-password-Form -->

  <!-- Scripts -->
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>
  <script src="assets/js/interface.js"></script>
  <!--Switcher-->
  <script src="assets/switcher/js/switcher.js"></script>
  <!--bootstrap-slider-JS-->
  <script src="assets/js/bootstrap-slider.min.js"></script>
  <!--Slider-JS-->
  <script src="assets/js/slick.min.js"></script>
  <script src="assets/js/owl.carousel.min.js"></script>

</body>

<!-- Mirrored from themes.webmasterdriver.net/carforyou/demo/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 16 Jun 2017 07:22:11 GMT -->

</html>