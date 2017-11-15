<?php
// Start the session
session_start();
$insert_message   = null;
$passenger_result = null;
$city_result      = null;
$insertpassenger_result = null;
$addinsert_message = null;
$servername       = "127.0.0.1";
$username         = "226team666";
$password         = "sesame";
$dbname           = "team666";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    
    if (!empty($_POST['update-passanger'])) {
        $PassangerID = mysqli_real_escape_string($conn , $_POST['PassangerID']);
        $update = "SELECT * FROM PASSENGER
        WHERE PASSENGER.PassengerID = '$PassangerID'";
        $res = $conn->query($update);
        if (mysqli_num_rows($res) == 0) {
            $insert_message = "There is no this passenger!";
        } else {
            if ($_POST['update'] == 'FirstName') {
                $sql = "UPDATE PASSENGER 
               SET     FirstName = '" . $_POST['change'] . "'
               WHERE PASSENGER.PassengerID = '$PassangerID'";
            }
            if ($_POST['update'] == 'LastName') {
                $sql = "UPDATE PASSENGER 
               SET     LastName = '" . $_POST['change'] . "'
               WHERE PASSENGER.PassengerID = '$PassangerID'";
            }
            if ($_POST['update'] == 'Email') {
                $sql = "UPDATE PASSENGER 
               SET     Email = '" . $_POST['change'] . "'
               WHERE PASSENGER.PassengerID = '$PassangerID'";
            }
            
            if ($conn->query($sql) === true) {
                $insert_message   = "User Information updated successfully!";
                $passenger_result = $conn->query($update);
            } else {
                $insert_message = "Error: " . $sql . "<br>" . $conn->error;
            }
        }
      }
      if (!empty($_POST['add-passanger'])) {
          $PassangerID = mysqli_real_escape_string($conn , $_POST['PassangerID']);
          $search = "SELECT * FROM PASSENGER
                      WHERE PASSENGER.PassengerID = '$PassangerID'";
          $res = $conn->query($search);
          if (mysqli_num_rows($res) > 0) {
            $addinsert_message = "This PassengerID already existed!";
        } else { 
          $add = "INSERT INTO `PASSENGER` (`PassengerID`, `FirstName`, `LastName`, `DateOfBirth`, `Email`) 
                  VALUES ('$PassangerID', 
                    '" . $_POST['FirstName'] . "', 
                    '" . $_POST['LastName'] . "', 
                    '" . $_POST['DateOfBirth'] . "', 
                    '" . $_POST['Email'] . "'); ";
          if ($conn->query($add) === true) {
              $addinsert_message   = "User Information inserted successfully!";
              $addres = "SELECT * FROM PASSENGER
                        WHERE PASSENGER.PassengerID = '$PassangerID'";
              $insertpassenger_result = $conn->query($addres);
          } else {
              $addinsert_message = "Error: " . $sql . "<br>" . $conn->error;
          }
        }
      }
}
$conn->close();
?>
<?php
include 'header.php';
?>
<div class="jumbotron feature">
   <div class="container">
      <h1><span class="glyphicon glyphicon-equalizer"></span> Update Information</h1>
   </div>
</div>
<div class="container">
   <div class="row">
      <div class="col-md-12">
         <div class="row">
            <font size="5">Add Passanger Information:</font><br>
            <form method="post" action="<?php
echo htmlspecialchars($_SERVER["PHP_SELF"]);
?>">
               PassangerID:
               <input type="text" name="PassangerID">
               FirstName:
               <input type="text" name="FirstName">
               LastName:
               <input type="text" name="LastName"><br>
               DateOfBirth（yyyy-mm-dd):
               <input type="text" name="DateOfBirth">
               Email:
               <input type="text" name="Email">
             </br>
               <input type="submit" name="add-passanger" value="Add">
            </form>
             <?php
if (isset($addinsert_message)) {
    echo "<font size=\"3\">" . $addinsert_message . "</font><br><br>";
}
if (isset($insertpassenger_result)) {
    echo "<table border='1'>";
    if (mysqli_num_rows($insertpassenger_result) > 0) {
        echo "<th><center><h4>&nbsp passengerID&nbsp&nbsp</h4></center></th> ";
        echo "<th><center><h4>First Name</h4></center></th>";
        echo "<th><center><h4>Last Name</h4></center></th>";
        echo "<th><center><h4>Date of Birth</h4></center></th>";
        echo "<th><center><h4>Email</h4></center></th>";
        // echo "<th><center><h4>PhoneNum</h4></center></th>";
        echo "</tr>";
        $row = mysqli_fetch_assoc($insertpassenger_result);
        echo "<th><p> &nbsp " . $row["PassengerID"] . "&nbsp </p></th>";
        echo "<th><p> &nbsp " . $row["FirstName"] . " </p></th>";
        echo "<th><p> &nbsp " . $row["LastName"] . " </p></th>";
        echo "<th><p> &nbsp " . $row["DateOfBirth"] . "&nbsp </p></th>";
        echo "<th><p> &nbsp " . $row["Email"] . "&nbsp </p></th>";
        
    }
    echo "</table>";
}
?>
         </div>
      </div>
   </div>
   <div class="row">
      <div class="col-md-12">
         <div class="row">
            <font size="5">Update passanger Information:</font><br>
            <form method="post" action="<?php
echo htmlspecialchars($_SERVER["PHP_SELF"]);
?>">
               Please Input PassangerID:
               <input type="text" name="PassangerID">
               Updata information
               <select name="update">
                  <option value="FirstName">FirstName</option>
                  <option value="LastName">LastName</option>
                  <option value="Email">Email</option>
               </select>
               <input type="text" name="change">
                   </br>
               <input type="submit" name="update-passanger" value="Updata">
            </form>
             <?php
if (isset($insert_message)) {
    echo "<font size=\"3\">" . $insert_message . "</font><br><br>";
}
if (isset($passenger_result)) {
    echo "<table border='1'>";
    if (mysqli_num_rows($passenger_result) > 0) {
        echo "<th><center><h4>&nbsp passengerID&nbsp&nbsp</h4></center></th> ";
        echo "<th><center><h4>First Name</h4></center></th>";
        echo "<th><center><h4>Last Name</h4></center></th>";
        echo "<th><center><h4>Date of Birth</h4></center></th>";
        echo "<th><center><h4>Email</h4></center></th>";
        // echo "<th><center><h4>PhoneNum</h4></center></th>";
        echo "</tr>";
        
    }
    while ($row = mysqli_fetch_assoc($passenger_result)) {
        // echo "<tr>";
        echo "<th><p> &nbsp " . $row["PassengerID"] . "&nbsp </p></th>";
        echo "<th><p> &nbsp " . $row["FirstName"] . " </p></th>";
        echo "<th><p> &nbsp " . $row["LastName"] . " </p></th>";
        echo "<th><p> &nbsp " . $row["DateOfBirth"] . "&nbsp </p></th>";
        echo "<th><p> &nbsp " . $row["Email"] . "&nbsp </p></th>";
       
    }
    echo "</table>";
    echo "<br><br>";
}
?>
         </div>
      </div>
   </div>
</div>