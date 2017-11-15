<?php
    // Start the session
    session_start();
    $sql_result = null;
    // Connect to database.
    $servername = "127.0.0.1";
    $username = "226team666";
    $password = "sesame";
    $dbname = "team666Analysis";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    // Get query results.
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $t = "";
        $ta = "";
        $r = "";
        $tr = "";
        if ($_POST['AnalysisType'] == 'TicketSale') {
            $t = "ticketsale";
            $ta = "TS";
            $r = "TotalCost";
            $tr = "TS.TotalCost";
        }
        if ($_POST['AnalysisType'] == 'TicketCancellation') {
            $t = "ticketcancellation";
            $ta = "TC";
            $r = "Refund";
            $tr = "TC.Refund";
        }
        if (!empty($_POST['analyze'])) {
            $sql1 = "SELECT $tr";   
        }
        if (!empty($_POST['count'])) {
            $sql1 = "SELECT COUNT($tr)";
        }
        if (!empty($_POST['sum'])) {
            $sql1 = "SELECT SUM($tr)";
        }
        if (!empty($_POST['average'])) {
            $sql1 = "SELECT AVG($tr)";
        }
        $sql2 = " FROM $t $ta, flight F, calendar C";
        $sql3 = " WHERE $ta.FlightKey = F.FlightKey AND $ta.CalendarKey = C.CalendarKey";
        if (!empty($_POST['selectAirlineName'])) {
            $sql1 = $sql1 . ", F.AirlineName";
            $sql3 = $sql3 . " AND F.AirlineName = '" . $_POST['selectAirlineName'] . "'";
        }
        if (!empty($_POST['selectDepartAirport'])) {
            $sql1 = $sql1 . ", F.DepartAirport, F.ArrivAirport";
            $sql3 = $sql3 . " AND F.DepartAirport = '" . $_POST['selectDepartAirport'] . "'";
        }
        if (!empty($_POST['selectArrivAirport'])) {
            $sql1 = $sql1 . ", F.DepartAirport, F.ArrivAirport";
            $sql3 = $sql3 . " AND F.ArrivAirport = '" . $_POST['selectArrivAirport'] . "'";
        }
        if (!empty($_POST['selectYear'])) {
            $sql1 = $sql1 . ", C.Year";
            $sql3 = $sql3 . " AND C.Year = " . $_POST['selectYear'];
        }
        if (!empty($_POST['selectMonth'])) {
            $sql1 = $sql1 . ", C.Month";
            $sql3 = $sql3 . " AND C.Month = '" . $_POST['selectMonth'] . "'";
        }
        if (!empty($_POST['selectDayOfWeek'])) {
            $sql1 = $sql1 . ", C.DayOfWeek";
            $sql3 = $sql3 . " AND C.DayOfWeek = '" . $_POST['selectDayOfWeek'] . "'";
        }
        if (!empty($_POST['selectSeatClassF']) xor !empty($_POST['selectSeatClassL'])) {
            $sql1 = $sql1 . ", S.Class";
            $sql2 = $sql2 . ", seat S";
            if (!empty($_POST['selectSeatClassF'])) {
                $sql3 = $sql3 . " AND $ta.SeatKey = S.SeatKey AND S.Class = '" . $_POST['selectSeatClassF'] . "'";
            }
            if (!empty($_POST['selectSeatClassL'])) {
                $sql3 = $sql3 . " AND $ta.SeatKey = S.SeatKey AND S.Class = '" . $_POST['selectSeatClassL'] . "'";
            }
        } 
        if (!empty($_POST['selectSeatClassF']) and !empty($_POST['selectSeatClassL'])) { 
            $sql1 = $sql1 . ", S.Class";
            $sql2 = $sql2 . ", seat S";
            $sql3 = $sql3 . " AND $ta.SeatKey = S.SeatKey AND S.Class IN ('" . $_POST['selectSeatClassF'] . "', '" . $_POST['selectSeatClassL'] . "')";
        }
        if (!empty($_POST['selectQuarter1']) or !empty($_POST['selectQuarter2']) or !empty($_POST['selectQuarter3']) or !empty($_POST['selectQuarter4'])) {
            $sql1 = $sql1 . ", C.Quarter";
            if ((!empty($_POST['selectQuarter1']) and empty($_POST['selectQuarter2']) and empty($_POST['selectQuarter3']) and empty($_POST['selectQuarter4'])) or (empty($_POST['selectQuarter1']) and !empty($_POST['selectQuarter2']) and empty($_POST['selectQuarter3']) and empty($_POST['selectQuarter4'])) or (empty($_POST['selectQuarter1']) and empty($_POST['selectQuarter2']) and !empty($_POST['selectQuarter3']) and empty($_POST['selectQuarter4'])) or (empty($_POST['selectQuarter1']) and empty($_POST['selectQuarter2']) and empty($_POST['selectQuarter3']) and !empty($_POST['selectQuarter4']))) {
                if (!empty($_POST['selectQuarter1'])) {
                    $sql3 = $sql3 . " AND C.Quarter = '" . $_POST['selectQuarter1'] . "'";
                }
                if (!empty($_POST['selectQuarter2'])) {
                    $sql3 = $sql3 . " AND C.Quarter = '" . $_POST['selectQuarter2'] . "'";
                }
                if (!empty($_POST['selectQuarter3'])) {
                    $sql3 = $sql3 . " AND C.Quarter = '" . $_POST['selectQuarter3'] . "'";
                }
                if (!empty($_POST['selectQuarter4'])) {
                    $sql3 = $sql3 . " AND C.Quarter = '" . $_POST['selectQuarter4'] . "'";
                }
                if (!empty($_POST['selectSeatClassF']) and !empty($_POST['selectSeatClassL'])) {
                    $sql3 = $sql3 . " GROUP BY S.Class";
                }
            }            
            else {
                $sql3 = $sql3 . " AND C.Quarter IN (";
                for ($s = 1; $s < 5; $s++) {
                    if(!empty($_POST['selectQuarter'. $s])) {
                    $sql3 = $sql3 . "'" . $_POST['selectQuarter' . $s] . "', ";
                    }
                }
                $sql3 = substr($sql3, 0, -2) . " )";
                if (!empty($_POST['selectSeatClassF']) and !empty($_POST['selectSeatClassL'])) {
                    $sql3 = $sql3 . " GROUP BY S.Class, C.Quarter";
                }
                else {
                    $sql3 = $sql3 . " GROUP BY C.Quarter";
                }
            }
        }
        else {
            if (!empty($_POST['selectSeatClassF']) and !empty($_POST['selectSeatClassL'])) {
                $sql3 = $sql3 . " GROUP BY S.Class";
            }
        }
        
        $sql = $sql1 . $sql2 . $sql3; 
        if ($_POST['selectOrder'] == "desc") { 
            $sql = $sql . " ORDER BY $tr DESC";
        }  
        if ($_POST['selectOrder'] == "asc") { 
            $sql = $sql . " ORDER BY $tr ASC";
        }   
        if (!empty($_POST['textLimit'])) {
                $sql = $sql . " LIMIT " . $_POST['textLimit'];
        }
        $sql_result = $conn->query($sql); 
    }
?>

<?php include 'header.php';?>

<div class="jumbotron feature">
    <div class="container">
        <h1><span class="glyphicon glyphicon-equalizer"></span> Flight Ticket Analysis </h1>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-md-12">

            <?php if(!isset($sql_result)) {?>

                <div class="row">
                    <font size="5">Analysis Information:</font><br>
                    <?php
                    /*function sqlArray($p){
                        $query = "SELECT DISTINCT " . $p . "from flight";
                        $results = $conn->query($query);
                        $i = 0;
                        $results_array = array();             
                        while ($rows = $results->fetch_assoc()){
                            $results_array[$i] = $rows["$p"];  
                            $i++;
                        } 
                        sort($results_array);
                        $results_array_len = count($results_array);
                        for ($j = 0; $j < $results_array_len; $j++){
                            echo "<option value='" . $results_array[$j] . "'>" . $results_array[$j]. "</option>"; 
                        }                                          
                    }*/
                    //sqlArray("AirlineName", "flight");

                    ?>
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                        <input type="radio" name="AnalysisType" value="TicketSale" checked>Ticket Sale  Information
                        <input type="radio" name="AnalysisType" value="TicketCancellation">Ticket Cancellation Information<br>
                        Airline:
                        <select name="selectAirlineName">
                        <option value=""> -- Select an option -- </option>
                        <?php
                            $sql_airline = "SELECT DISTINCT AirlineName from flight";
                            $result_airline = $conn->query($sql_airline);
                            $i = 0;
                            $airline = array();             
                            while ($row_airline = $result_airline->fetch_assoc()){
                                $airline[$i] = $row_airline['AirlineName'];  
                                $i++; 
                            }
                            sort($airline);
                            $airline_len = count($airline);
                            for ($i = 0; $i < $airline_len; $i++){
                                echo "<option value='" . $airline[$i] . "'>" . $airline[$i]. "</option>"; 
                            }
                        ?>
                        </select><br>
                        Depart Airport:
                        <select name="selectDepartAirport">
                        <option value=""> -- Select an option -- </option>
                        <?php
                            $sql_depart = "SELECT DISTINCT DepartAirport from flight";
                            $result_depart = $conn->query($sql_depart);
                            $k = 0;
                            $depart_airport = array();             
                            while ($row_depart = $result_depart->fetch_assoc()){
                                $depart_airport[$k] = $row_depart['DepartAirport'];  
                                $k++; 
                            }
                            sort($depart_airport);
                            $depart_len = count($depart_airport);
                            for ($i = 0; $i < $depart_len; $i++){
                                echo "<option value='" . $depart_airport[$i] . "'>" . $depart_airport[$i]. "</option>"; 
                            }                   
                        ?>
                        </select><br>
                        Arrival Airport:
                        <select name="selectArrivAirport">
                        <option value=""> -- Select an option -- </option>
                        <?php
                            $sql_arriv = "SELECT DISTINCT ArrivAirport from flight";
                            $result_arriv = $conn->query($sql_arriv);
                            $l = 0;
                            $arriv_airport = array();             
                            while ($row_arriv = $result_arriv->fetch_assoc()){
                                $arriv_airport[$l] = $row_arriv['ArrivAirport'];  
                                $l++; 
                            }
                            sort($arriv_airport);
                            $arriv_len = count($arriv_airport);
                            for ($i = 0; $i < $arriv_len; $i++){
                                echo "<option value='" . $arriv_airport[$i] . "'>" . $arriv_airport[$i]. "</option>"; 
                            } 
                        ?>
                        </select><br>
                        Seat Class:
                        <input type="checkbox" name="selectSeatClassF" value="F"> F
                        <input type="checkbox" name="selectSeatClassL" value="L"> L<br>
                        Year:
                        <select name="selectYear">
                        <option value=""> -- Select an option -- </option>
                        <?php
                            $sql_calendar = "SELECT DISTINCT Year from calendar";
                            $result_calendar = $conn->query($sql_calendar);
                            $j = 0;
                            $year = array();             
                            while ($row_calendar = $result_calendar->fetch_assoc()){
                                $year[$j] = $row_calendar['Year'];  
                                $j++; 
                            }
                            sort($year);
                            $year_len = count($year);
                            for ($i = 0; $i < $year_len; $i++){
                                echo "<option value='" . $year[$i] . "'>" . $year[$i]. "</option>"; 
                            }                            
                        ?>
                        </select><br>
                        Quarter:
                        <input type="checkbox" name="selectQuarter1" value="Q1"> Q1
                        <input type="checkbox" name="selectQuarter2" value="Q2"> Q2
                        <input type="checkbox" name="selectQuarter3" value="Q3"> Q3
                        <input type="checkbox" name="selectQuarter4" value="Q4"> Q4<br>
                        Month:
                        <select name="selectMonth">
                            <option value=""> -- Select an option -- </option>
                            <option value="January">January</option>
                            <option value="February">February</option>
                            <option value="March">March</option>
                            <option value="April">April</option>
                            <option value="May">May</option>
                            <option value="June">June</option>
                            <option value="July">July</option>
                            <option value="August">August</option>
                            <option value="September">September</option>
                            <option value="October">October</option>
                            <option value="November">November</option>
                            <option value="December">December</option>
                        </select><br>
                        DayOfWeek:
                        <select name="selectDayOfWeek">
                            <option value=""> -- Select an option -- </option>
                            <option value="Monday">Monday</option>
                            <option value="Tuesday">Tuesday</option>
                            <option value="Wednesday">Wednesday</option>
                            <option value="Thursday">Thursday</option>
                            <option value="Friday">Friday</option>
                            <option value="Saturday">Saturday</option>
                            <option value="Sunday">Sunday</option>
                        </select><br>                        
                        Order:
                        <select name="selectOrder">
                            <option value=""> -- Select an option -- </option>
                            <option value="desc">Hign to Low</option>
                            <option value="asc">Low to High</option>                      
                        </select><br>
                        Limit:
                        <input type="text" name="textLimit"><br>                                              
                        <input type="submit" name="analyze" value="Analyze">                        
                        <input type="submit" name="count" value="Count">
                        <input type="submit" name="sum" value="Sum">
                        <input type="submit" name="average" value="Average">                        
                    </form>
                </div>

            <?php } ?>

                <div class="row">
                    <?php
                        if(isset($sql_result)) {
                            if (!empty($_POST['analyze'])) {
                                echo "Find " . mysqli_num_rows($sql_result) . " Results:<br><br>"; 
                            }                                               
                            echo "<table border='1'>";
                            echo "<tr>";
                            if (!empty($_POST['selectAirlineName'])) {
                                echo "<th><center><h4>&nbsp Airline &nbsp</h4></center></th>"; 
                            }
                            if ((!empty($_POST['selectDepartAirport']) or !empty($_POST['selectArrivAirport'])) and !empty($_POST['analyze'])) {
                                echo "<th><center><h4>&nbsp Depart Airport &nbsp</h4></center></th>";
                                echo "<th><center><h4>&nbsp Arrival Airport &nbsp</h4></center></th>";                                
                            }
                            if ((!empty($_POST['selectDepartAirport']) or !empty($_POST['selectArrivAirport'])) and empty($_POST['analyze'])) {
                                if (!empty($_POST['selectDepartAirport'])) {
                                    echo "<th><center><h4>&nbsp Depart Airport &nbsp</h4></center></th>";
                                }
                                if (!empty($_POST['selectArrivAirport'])) {
                                    echo "<th><center><h4>&nbsp Arrival Airport &nbsp</h4></center></th>"; 
                                }
                            }

/*if (!empty($_POST['selectDepartAirport'])) {
                                echo "<th><center><h4>&nbsp Depart Airport &nbsp</h4></center></th>";
                                if (!empty($_POST['analyze'])) {                              
                                    echo "<th><center><h4>&nbsp Arrival Airport &nbsp</h4></center></th>";
                                } 
                            } 
                            if (!empty($_POST['selectArrivAirport'])) {
                                if (!empty($_POST['analyze'])) {
                                    echo "<th><center><h4>&nbsp Depart Airport &nbsp</h4></center></th>";
                                }                              
                                echo "<th><center><h4>&nbsp Arrival Airport &nbsp</h4></center></th>"; 
                            }     */  
                            if (!empty($_POST['selectSeatClassF']) or !empty($_POST['selectSeatClassL'])) {
                                echo "<th><center><h4>&nbsp Seat Class &nbsp</h4></center></th>";
                            }    
                            if (!empty($_POST['selectYear'])) {
                                echo "<th><center><h4>&nbsp Year &nbsp</h4></center></th>";
                            }    
                            if (!empty($_POST['selectQuarter1']) or !empty($_POST['selectQuarter2']) or !empty($_POST['selectQuarter3']) or !empty($_POST['selectQuarter4'])) {    
                                echo "<th><center><h4>&nbsp Quarter &nbsp</h4></center></th>";
                            }
                            if (!empty($_POST['selectMonth'])) {
                                echo "<th><center><h4>&nbsp Month &nbsp</h4></center></th>";
                            }
                            if (!empty($_POST['selectDayOfWeek'])) {    
                                echo "<th><center><h4>&nbsp DayOfWeek &nbsp</h4></center></th>";
                            }
                            if (!empty($_POST['analyze'])) {
                                if ($_POST['AnalysisType'] == 'TicketSale') {
                                    echo "<th><center><h4>&nbsp Sale &nbsp</h4></center></th>";
                                }
                                if ($_POST['AnalysisType'] == 'TicketCancellation') {
                                    echo "<th><center><h4>&nbsp Cancellation &nbsp</h4></center></th>";
                                }
                            }                            
                            if (!empty($_POST['count'])) {
                                if ($_POST['AnalysisType'] == 'TicketSale') {
                                    echo "<th><center><h4>&nbsp COUNT(Sale) &nbsp</h4></center></th>";
                                }
                                if ($_POST['AnalysisType'] == 'TicketCancellation') {
                                    echo "<th><center><h4>&nbsp COUNT(Cancellation) &nbsp</h4></center></th>";
                                }
                            }
                            if (!empty($_POST['sum'])) {
                                if ($_POST['AnalysisType'] == 'TicketSale') {
                                    echo "<th><center><h4>&nbsp SUM(Sale) &nbsp</h4></center></th>";
                                }
                                if ($_POST['AnalysisType'] == 'TicketCancellation') {
                                    echo "<th><center><h4>&nbsp SUM(Cancellation) &nbsp</h4></center></th>";
                                }
                            }
                            if (!empty($_POST['average'])) {
                                if ($_POST['AnalysisType'] == 'TicketSale') {
                                    echo "<th><center><h4>&nbsp AVG(Sale) &nbsp</h4></center></th>";
                                }
                                if ($_POST['AnalysisType'] == 'TicketCancellation') {
                                    echo "<th><center><h4>&nbsp AVG(Cancellation) &nbsp</h4></center></th>";
                                }
                            }
                            echo "</tr>";  
                            
                            while($row_sql = $sql_result->fetch_assoc()) {
                                echo "<tr>";
                                if (!empty($_POST['selectAirlineName'])) {
                                    echo "<th><p> &nbsp ". $row_sql["AirlineName"] ."&nbsp </p></th>";
                                }
                                if ((!empty($_POST['selectDepartAirport']) or !empty($_POST['selectArrivAirport'])) and !empty($_POST['analyze'])) {
                                    echo "<th><p> &nbsp ". $row_sql["DepartAirport"] ."&nbsp </p></th>";
                                    echo "<th><p> &nbsp ". $row_sql["ArrivAirport"] ."&nbsp </p></th>";
                                }
                                if ((!empty($_POST['selectDepartAirport']) or !empty($_POST['selectArrivAirport'])) and empty($_POST['analyze'])) {
                                    if (!empty($_POST['selectDepartAirport'])) {
                                        echo "<th><p> &nbsp ". $row_sql["DepartAirport"] ."&nbsp </p></th>";
                                    }
                                    if (!empty($_POST['selectArrivAirport'])) {
                                        echo "<th><p> &nbsp ". $row_sql["ArrivAirport"] ."&nbsp </p></th>"; 
                                    }
                                }
                                /*if (!empty($_POST['selectDepartAirport'])) {
                                    echo "<th><p> &nbsp ". $row_sql["DepartAirport"] ."&nbsp </p></th>";
                                    if (!empty($_POST['analyze'])) {
                                        echo "<th><p> &nbsp ". $row_sql["ArrivAirport"] ."&nbsp </p></th>";
                                    }
                                }
                                if (!empty($_POST['selectArrivAirport'])) {
                                    if (!empty($_POST['analyze'])) {
                                        echo "<th><p> &nbsp ". $row_sql["DepartAirport"] ."&nbsp </p></th>";
                                    }
                                    echo "<th><p> &nbsp ". $row_sql["ArrivAirport"] ."&nbsp </p></th>";
                                }*/
                                if (!empty($_POST['selectSeatClassF']) or !empty($_POST['selectSeatClassL'])) {
                                    echo "<th><p> &nbsp ". $row_sql["Class"] ."&nbsp </p></th>";
                                }
                                if (!empty($_POST['selectYear'])) {
                                    echo "<th><p> &nbsp ". $row_sql["Year"] ."&nbsp </p></th>";
                                }
                                if (!empty($_POST['selectQuarter1']) or !empty($_POST['selectQuarter2']) or !empty($_POST['selectQuarter3']) or !empty($_POST['selectQuarter4'])) {  
                                    echo "<th><p> &nbsp ". $row_sql["Quarter"] ."&nbsp </p></th>";
                                }
                                if (!empty($_POST['selectMonth'])) {
                                    echo "<th><p> &nbsp ". $row_sql["Month"] ."&nbsp </p></th>";
                                }
                                if (!empty($_POST['selectDayOfWeek'])) { 
                                    echo "<th><p> &nbsp ". $row_sql["DayOfWeek"] ."&nbsp </p></th>";
                                }
                                if (!empty($_POST['analyze'])) {
                                    echo "<th><p> &nbsp ". $row_sql["$r"] ."&nbsp </p></th>";
                                }                                
                                if (!empty($_POST['count'])) {
                                    echo "<th><p> &nbsp ". $row_sql["COUNT($tr)"] ."&nbsp </p></th>";
                                }
                                if (!empty($_POST['sum'])) {
                                    echo "<th><p> &nbsp ". $row_sql["SUM($tr)"] ."&nbsp </p></th>";
                                }
                                if (!empty($_POST['average'])) {
                                    echo "<th><p> &nbsp ". $row_sql["AVG($tr)"] ."&nbsp </p></th>";
                                }
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