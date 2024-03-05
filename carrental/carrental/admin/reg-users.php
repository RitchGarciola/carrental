<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
	{	
header('location:index.php');
}
else{
if(isset($_GET['del']))
{
$id=$_GET['del'];
$sql = "delete from tblusers  WHERE id=:id";
$query = $dbh->prepare($sql);
$query -> bindParam(':id',$id, PDO::PARAM_STR);
$query -> execute();
$msg="Customer Removed";

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

	<title>Car Rental Portal |Customer Information </title>

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
	<!-- Admin Stye -->
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
	<?php include('includes/header.php');?>

	<div class="ts-main-content">
		<?php include('includes/leftbar.php');?>
		<div class="content-wrapper">
			<div class="container-fluid">

				<div class="row">
					<div class="col-md-12">

						<h2 class="page-title">Customer Information</h2>

						<!-- Zero Configuration Table -->
						<div class="panel panel-default">
							<div class="panel-heading">Customer Information</div>
							<div class="panel-body">
								<?php if($error){?><div class="errorWrap">
									<strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php } 
				else if($msg){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php }?>
								<table id="zctb" class="display table table-striped table-bordered table-hover"
									cellspacing="0" width="100%">
									<thead>
										<tr>
											<th>#</th>
											<th>Name</th>
											<th>Email</th>
											<th>Contact no</th>
											<th>Reg Date</th>
											<th>Driver's License</th>
											<th>ProfilePicture</th>
											<th>Action</th>
										</tr>
									</thead>
									<tfoot>
										<tr>
											<th>#</th>
											<th>Name</th>
											<th>Email </th>
											<th>Contact no</th>
											<th>Reg Date</th>
											<th>Driver's License</th>
											<th>ProfilePicture</th>
											<th>Action</th>
										</tr>
										</tr>
									</tfoot>
									<tbody>
										<?php
    $sql = "SELECT * FROM tblusers";
    $query = $dbh->prepare($sql);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
    $cnt = 1;

    if ($query->rowCount() > 0) {
        foreach ($results as $result) {
            ?>
										<tr>
											<td><?php echo htmlentities($cnt); ?></td>
											<td><?php echo htmlentities($result->FullName); ?></td>
											<td><?php echo htmlentities($result->EmailId); ?></td>
											<td><?php echo htmlentities($result->ContactNo); ?></td>
											
											<td><?php echo htmlentities($result->RegDate); ?></td>
											<td><img src="../uploadsFront/<?= htmlentities($result->FrontImage) ?>"
													alt="image" width="100" height="100">
												<img src="../uploadsBack/<?= htmlentities($result->BackImage) ?>"
													alt="image" width="100" height="100"></td>

											
											<td><img src="../uploadsProfile/<?= htmlentities($result->profileImage) ?>"
													alt="image" width="100" height="100"><td>
												

											
											
												<a href="reg-users.php?del=<?php echo htmlentities($result->id); ?>"
													onclick="return confirm('Do you want to delete');">
													<i class="fa fa-trash"></i>
												</a>
											</td>
										</tr>
										<?php $cnt = $cnt + 1;
        }
    }
    ?>
									</tbody>

								</table>



							</div>
						</div>



					</div>
				</div>
				<!-- Add this CSS code within your document's head or in a separate stylesheet -->
				<style>
					/* Style for the modal */
					.modal {
						display: none;
						position: fixed;
						top: 50%;
						left: 50%;
						transform: translate(-40%, -40%);
						z-index: 1;
						background-color: white;
						/* Set background color to white */
						border: 1px solid #ccc;
						/* Add a border for visibility */
						box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
						/* Add a subtle box shadow */
					}

					/* Style for the modal content */
					.modal img {
						
						width: 100%;
						/* Make the image fill the modal */
						height: auto;
						/* Maintain aspect ratio */
					}

					/* Style for the close button */
					.close {
						position: absolute;
						top: 10px;
						right: 10px;
						padding: 10px;
						cursor: pointer;
						background-color: #fff;
						/* White background for visibility */
						border: 1px solid #ccc;
						/* Add a border for better visibility */
						border-radius: 4px;
						/* Optional: Add border radius for a rounded look */
					}


					/* Style for the previous and next buttons */
					.prevBtn,
					.nextBtn {
						position: absolute;
						top: 50%;
						width: auto;
						padding: 16px;
						margin-top: -22px;
						font-size: 18px;
						cursor: pointer;
						background-color: #fff;
						border: 1px solid #ccc;
						border-radius: 4px;
						user-select: none;
					}

					/* Position the previous button to the left */
					.prevBtn {
						left: 10px;
					}

					/* Position the next button to the right */
					.nextBtn {
						right: 10px;
					}
				</style>

				<!-- Add this HTML code within your document's body -->
				<div id="myModal" class="modal">
					<span class="close" onclick="closeModal()">&times;</span>
					<div id="modalContent"></div>
					<button id="prevBtn" class="prevBtn" onclick="plusSlides(-1)">Prev</button>
					<button id="nextBtn" class="nextBtn" onclick="plusSlides(1)">Next</button>
				</div>

				<!-- Add this script to handle modal functionality -->
				<script>
					var imageContainers = document.querySelectorAll('td');
					var modal = document.getElementById('myModal');
					var modalContent = document.getElementById('modalContent');
					var prevBtn = document.getElementById('prevBtn');
					var nextBtn = document.getElementById('nextBtn');
					var slideIndex = 1;

					// Attach a click event listener to each <td> element
					imageContainers.forEach(function (container) {
						container.addEventListener('click', function () {
							var clickedImages = container.querySelectorAll('img');

							if (clickedImages.length > 0) {
								modalContent.innerHTML = '';

								clickedImages.forEach(function (img) {
									var imgClone = img.cloneNode(true);
									modalContent.appendChild(imgClone);
								});

								// Display the modal
								modal.style.display = 'block';

								// Show the first image by default
								showSlides(slideIndex);
							}
						});
					});

					// Get the close button inside the modal
					var closeButton = document.querySelector('.close');

					// Attach a click event listener to the close button to hide the modal
					closeButton.addEventListener('click', function () {
						closeModal();
					});

					// Attach a click event listener to the modal background to hide the modal
					window.addEventListener('click', function (event) {
						if (event.target === modal) {
							closeModal();
						}
					});

					// Functions to handle next and previous slides
					function plusSlides(n) {
						showSlides(slideIndex += n);
					}

					function showSlides(n) {
						var slides = modalContent.querySelectorAll('img');

						if (n > slides.length) {
							slideIndex = 1;
						} else if (n < 1) {
							slideIndex = slides.length;
						}

						for (var i = 0; i < slides.length; i++) {
							slides[i].style.display = 'none';
						}

						slides[slideIndex - 1].style.display = 'block';
					}

					// Function to close the modal
					function closeModal() {
						modal.style.display = 'none';
					}
				</script>


			</div>
		</div>
	</div>

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
</body>

</html>
<?php } ?>