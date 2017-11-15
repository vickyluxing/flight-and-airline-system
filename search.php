<?php
   // Start the session
   session_start();
   $insert_message = null;
   $ticket_result = null;
   $city_result = null;
       $servername = "127.0.0.1";
       $username = "226team666";
       $password = "sesame";
       $dbname = "team666";
   
       // Create connection
       $conn = new mysqli($servername, $username, $password, $dbname);
       // Check connection
   if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
       if ($conn->connect_error) {
           die("Connection failed: " . $conn->connect_error);
       } 
   
   
       if (!empty($_POST['search-ticket'])) {
           if ($_POST['searchTicketBy'] == 'searchTicketID') {
               $sql = "SELECT * FROM PASSENGER p,TICKET t,FLIGHT f, AIRLINE a 
               WHERE p.PassengerID = t.PassengerID 
               AND t.FlightNo = f.FlightNo
               AND t.AirlineID = a.AirlineID 
               AND TicketID ='" . $_POST['searchValue'] . "'";
           } 
           if ($_POST['searchTicketBy'] == 'searchPassengerID') {
               $sql = "SELECT * FROM PASSENGER p,TICKET t,FLIGHT f, AIRLINE a  
               WHERE p.PassengerID = t.PassengerID 
               AND t.FlightNo = f.FlightNo
               AND t.AirlineID = a.AirlineID 
               AND t.PassengerID ='" . $_POST['searchValue'] . "'";
           }
           $ticket_result = $conn->query($sql);      
       }
   
       if (!empty($_POST['search-flight'])) {
               $sql = "SELECT * FROM FLIGHT f, AIRPORT dep, AIRPORT arr
               WHERE f.DepartAirport = dep.AirportCode
               AND f.ArrivAirport = arr.AirportCode
               AND dep.City = '" . $_POST['DepartCity'] . "'
               AND arr.City ='" . $_POST['ArrivalCity'] . "'";
               $airline_result = $conn->query($sql);
       }
   }
?>
<?php include 'header.php';?>
<div class="jumbotron feature">
   <div class="container">
      <h1><span class="glyphicon glyphicon-equalizer"></span> Search</h1>
   </div>
</div>
<div class="container">
   <div class="row">
      <div class="col-md-12">
        <?php if(!isset($ticket_result) and !isset($airline_result)) {?>
         <div class="row">
            <font size="5">Search Ticket Information:</font><br>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
               Search By:
               <select name="searchTicketBy">
                  <option value="searchTicketID">TicketID</option>
                  <option value="searchPassengerID">PassengerID</option>
               </select>
               <input type="text" name="searchValue">
               <input type="submit" name="search-ticket" value="Search">
            </form>
         </div>
         <div class="row">
            <font size="5">Search Flight Information:</font><br>
            <?php
               $sqlcity="SELECT City from AIRPORT";
               $city_result = $conn->query($sqlcity);
               $i = 0;
               $city = array();             
               while ($row = mysqli_fetch_assoc($city_result)){
                   $city[$i] = $row['City'];  
                   $i++; 
               }
               $city = array_unique($city);
               sort($city); 
               ?>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
               Departure City:
               <select name="DepartCity">
               <?php
                  $arrlength = count($city);
                  for ($i = 0; $i < $arrlength; $i++){
                      echo "<option value='{$city[$i]}''>{$city[$i]}</option>"; 
                  }                
                  ?>
               </select>
               Arrival City:
               <select name="ArrivalCity">
               <?php
                  for ($i = 0; $i < $arrlength; $i++){
                      echo "<option value='{$city[$i]}''>{$city[$i]}</option>"; 
                  } 
                ?>
               </select>
               <input type="submit" name="search-flight" value="Search">
            </form>
         </div>
    <?php } ?>
         <div class="row">
            <?php
               if(isset($ticket_result)) {
                   echo "Find " . mysqli_num_rows($ticket_result) . " Ticket Information:<br><br>";
                   echo "<table border='1'>";
                   if(mysqli_num_rows($ticket_result) > 0){
                       echo "<th><center><h4>&nbsp TicketID&nbsp&nbsp</h4></center></th> ";
                       // echo "<th><center><h4>PassengerID</h4></center></th>";
                       echo "<th><center><h4>Passenger Name</h4></center></th>";
                       echo "<th><center><h4>Price</h4></center></th>";
                       echo "<th><center><h4>&nbsp Depart Airport &nbsp</h4></center></th>";
                       echo "<th><center><h4>&nbsp Arrive Airport &nbsp</h4></center></th>";
                       echo "<th><center><h4>&nbsp SeatNo &nbsp</h4></center></th>";
                       echo "<th><center><h4>&nbsp Airline &nbsp</h4></center></th>";
                       echo "<th><center><h4>&nbsp Depater Time &nbsp</h4></center></th>";
                       echo "<th><center><h4>&nbsp Arrival Time &nbsp</h4></center></th>";
                       echo "</tr>";
                  }
                   while($row = mysqli_fetch_assoc($ticket_result)) {
                       // echo "<tr>";
                       echo "<th><p> &nbsp ". $row["TicketID"] ."&nbsp </p></th>";
                       // echo "<th><p> &nbsp ". $row["PassengerID"] ."&nbsp </p></th>";
                       echo "<th><p> &nbsp ". $row["FirstName"] ."&nbsp". $row["LastName"]." </p></th>";
                       echo "<th><p> &nbsp $". $row["Price"] ."&nbsp </p></th>";
                       echo "<th><p> &nbsp ". $row["DepartAirport"] ."&nbsp </p></th>";
                       echo "<th><p> &nbsp ". $row["ArrivAirport"] ."&nbsp </p></th>";
                       echo "<th><p> &nbsp ". $row["SeatNo"] ."&nbsp </p></th>";
                       echo "<th><p> &nbsp ". $row["AirlineName"] ."&nbsp </p></th>";
                       echo "<th><p> &nbsp ". $row["FlightDepartTime"] ."&nbsp </p></th>";
                       echo "<th><p> &nbsp ". $row["FlightArrivTime"] ."&nbsp </p></th>";
                       echo "</tr>";
                   }
                   echo "</table>";
               }
               if(isset($airline_result)) {
                   echo "Find " . mysqli_num_rows($airline_result) . " Flight Information:<br><br>";
                   echo "<table border='1'>";
                   if(mysqli_num_rows($airline_result) > 0){
                       echo "<th><center><h4>&nbsp FlightNo &nbsp&nbsp</h4></center></th> ";
                       echo "<th><center><h4>AirlineID</h4></center></th>";
                       echo "<th><center><h4>Depart Airport </h4></center></th>";
                       echo "<th><center><h4>Arrive Airport </h4></center></th>";
                       echo "<th><center><h4>&nbsp Depater Time &nbsp</h4></center></th>";
                       echo "<th><center><h4>&nbsp Arrival Time &nbsp</h4></center></th>";
                       echo "</tr>";
               }
                   while($row = mysqli_fetch_assoc($airline_result)) {
                       // echo "<tr>";
                       echo "<th><p> &nbsp ". $row["FlightNo"] ."&nbsp </p></th>";
                       echo "<th><p> &nbsp ". $row["AirlineID"] ."&nbsp </p></th>";
                       echo "<th><p> &nbsp ". $row["DepartAirport"] ."&nbsp </p></th>";
                       echo "<th><p> &nbsp ". $row["ArrivAirport"] ."&nbsp </p></th>";
                       echo "<th><p> &nbsp ". $row["FlightDepartTime"] ."&nbsp </p></th>";
                       echo "<th><p> &nbsp ". $row["FlightArrivTime"] ."&nbsp </p></th>";
                       echo "</tr>";
                   }
                   echo "</table>";
               }
               ?>
         </div>
      </div>
   </div>
</div>
<?php 
   $conn->close();  
   ?>