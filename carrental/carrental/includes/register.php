<?php
//error_reporting(0);
if(isset($_POST['signup']))
{
    $fname = $_POST['fullname'];
    $email = $_POST['emailid']; 
    $mobile = $_POST['mobileno'];
    $password = md5($_POST['password']);

    // File upload handling for Image1
    $frontImage = $_FILES['frontImage']['name'];
    $temp_name1 = $_FILES['frontImage']['tmp_name'];
    move_uploaded_file($temp_name1, "uploads/".$frontImage);

    // File upload handling for Image2
    $backImage = $_FILES['backImage']['name'];
    $temp_name2 = $_FILES['backImage']['tmp_name'];
    move_uploaded_file($temp_name2, "uploads/".$backImage);

    $sql = "INSERT INTO tblusers (FullName, EmailId, ContactNo, Password, FrontImage, BackImage) VALUES (:fname, :email, :mobile, :password, :frontImage, :backImage)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':fname', $fname, PDO::PARAM_STR);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':mobile', $mobile, PDO::PARAM_STR);
    $query->bindParam(':password', $password, PDO::PARAM_STR);
    $query->bindParam(':frontImage', $frontImage, PDO::PARAM_STR);
    $query->bindParam(':backImage', $backImage, PDO::PARAM_STR);
    $query->execute();

    $lastInsertId = $dbh->lastInsertId();
    if($lastInsertId)
    {
        echo "<script>alert('Registration successful. Now you can login and complete your information in Profile Setting');</script>";
    }
    else 
    {
        echo "<script>alert('Something went wrong. Please try again');</script>";
    }
}



