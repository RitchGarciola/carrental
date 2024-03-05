<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

//include('includes/config.php');
if (isset($_POST['submit'])) {
  $fname = $_POST['fullname'];
  $email = $_POST['emailid'];
  $mobile = $_POST['mobileno'];
  $password = md5($_POST['password']);
  $password2 = md5($_POST["confirmpassword"]);

  $frontImage = $_FILES["frontImage"]["name"];
  $backImage = $_FILES["backImage"]["name"];
  $profilePicture = $_FILES["profilePicture"]["name"];

  move_uploaded_file($_FILES["frontImage"]["tmp_name"], "uploadsFront/" . $frontImage);
  move_uploaded_file($_FILES["backImage"]["tmp_name"], "uploadsBack/" . $backImage);
  move_uploaded_file($_FILES["profilePicture"]["tmp_name"], "uploadsProfile/" . $profilePicture);

  // Initialize PDO connection
  // $dbh = new PDO('mysql:host=localhost;dbname=your_database', 'username', 'password');

  // Check if email already exists
  $stmt = $dbh->prepare("SELECT * FROM tblusers WHERE EmailId = :email");
  $stmt->bindParam(':email', $email);
  $stmt->execute();
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($row) {
      echo "<script>alert('Email already exists');</script>";
  } else {
      // Check if passwords match
      if ($password !== $password2) {
          echo "<script>alert('Passwords do not match');</script>";
      } else {
          // Insert new user
          $sql = "INSERT INTO tblusers(FullName, EmailId, ContactNo, Password, FrontImage, BackImage, profileImage) 
              VALUES(:fname, :email, :mobile, :password, :frontImage, :backImage, :profilePicture)";

          $query = $dbh->prepare($sql);
          $query->bindParam(':fname', $fname, PDO::PARAM_STR);
          $query->bindParam(':email', $email, PDO::PARAM_STR);
          $query->bindParam(':mobile', $mobile, PDO::PARAM_STR);
          $query->bindParam(':password', $password, PDO::PARAM_STR);
          $query->bindParam(':frontImage', $frontImage, PDO::PARAM_STR);
          $query->bindParam(':backImage', $backImage, PDO::PARAM_STR);
          $query->bindParam(':profilePicture', $profilePicture, PDO::PARAM_STR);

          if ($query->execute()) {
              $lastInsertId = $dbh->lastInsertId();
              if ($lastInsertId) {
                  echo "<script>alert('Registration successful. You Can Now Rent A Car!');</script>";
                  // Redirect to login page or dashboard
                 // header("Location: login.php");
                 // exit();
              } else {
                  echo "<script>alert('Something went wrong. Please try again');</script>";
              }
          } else {
              echo "<script>alert('Error in executing query');</script>";
          }
      }
  }
}

?>


<div class="modal fade" id="signupform">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">Sign Up</h3>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="signup_wrap">
            <div class="col-md-12 col-sm-6">
              <form method="post" name="signup" onsubmit="return valid()" enctype="multipart/form-data">
                <div class="col-md-6">
                  <div class="form-group">
                    <input type="text" class="form-control" name="fullname" placeholder="Full Name" required="required">
                  </div>
                  <div class="form-group">
                    <input type="text" class="form-control" name="mobileno" placeholder="Mobile Number" maxlength="12" required="required">
                  </div>
                  <div class="form-group">
                    <input type="email" class="form-control" name="emailid" id="emailid" onblur="checkAvailability()" placeholder="Email Address" required="required">
                    <span id="user-availability-status" style="font-size:12px;"></span>
                  </div>
                  <div class="form-group">
                    <input type="password" class="form-control" name="password" placeholder="Password" required="required">
                  </div>
                  <div class="form-group">
                    <input type="password" class="form-control" onblur="valid()" name="confirmpassword" placeholder="Confirm Password" required="required">
                    <span id="password-mismatch" style="color: red;"></span>
                  </div>

                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="frontImage">Front of License:</label>
                    <input type="file" id="frontImage" name="frontImage" accept="image/*" onchange="previewImage('frontImage', 'frontPreview')" required>
                    <img id="frontPreview" src="#" alt="Front Preview" style="display:none; max-width: 100px; max-height: 100px;">
                  </div>

                  <div class="form-group">
                    <label for="backImage">Back of License:</label>
                    <input type="file" id="backImage" name="backImage" accept="image/*" onchange="previewImage('backImage', 'backPreview')" required>
                    <img id="backPreview" src="#" alt="Back Preview" style="display:none; max-width: 100px; max-height: 100px;">
                  </div>

                  <div class="form-group">
                    <label for="profilePicture">Profile Picture:</label>
                    <input type="file" id="profilePicture" name="profilePicture" accept="image/*" onchange="previewImage('profilePicture', 'profilePreview')" required>
                    <img id="profilePreview" src="#" alt="Profile Preview" style="display:none; max-width: 100px; max-height: 100px;">
                  </div>



                  <div class="form-group hidden checkbox">
                    <input type="checkbox" id="terms_agree" required="required" checked="">
                    <label for="terms_agree">I Agree with <a href="#">Terms and Conditions</a></label>
                  </div>
                  <div class="form-group">
                    <input type="submit" value="Sign Up" name="submit" id="submit" class="btn btn-block">
                  </div>
                </div>
              </form>
            </div>

          </div>
        </div>
      </div>
      <div class="modal-footer text-center">
        <p>Already got an account? <a href="#loginform" data-toggle="modal" data-dismiss="modal">Login Here</a></p>
      </div>
    </div>
  </div>
</div>




<script>
  function previewImage(inputId, previewId) {
    var input = document.getElementById(inputId);
    var preview = document.getElementById(previewId);

    var file = input.files[0];
    var reader = new FileReader();

    reader.onload = function(e) {
      preview.src = e.target.result;
      preview.style.display = 'block';
    };

    if (file) {
      reader.readAsDataURL(file);
    } else {
      preview.src = "#";
      preview.style.display = 'none';
    }
  }
</script>

<script>
  function checkAvailability() {
    $("#loaderIcon").show();
    jQuery.ajax({
      url: "check_availability.php",
      data: 'emailid=' + $("#emailid").val(),
      type: "POST",
      success: function(data) {
        $("#user-availability-status").html(data);
        $("#loaderIcon").hide();
      },
      error: function() {}
    });
  }
</script>
<script type="text/javascript">
  function valid() {
    var password = document.signup.password.value;
    var confirmPassword = document.signup.confirmpassword.value;

    if (password !== confirmPassword) {
      document.getElementById('password-mismatch').innerHTML = 'Passwords do not match!';
      return false;
    } else {
      document.getElementById('password-mismatch').innerHTML = '';
      return true;
    }
  }
</script>