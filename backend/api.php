<?php
    // database connection
    $servername = "rootpowered.nl";
    $username = "owasp_owasp";
    $password = "QuDh4PDS3b";
    $dbname = "owasp_owasp";
    if(isset($_GET['Token'])){
        $token= $_GET['Token'];
        $case= $_GET['Case'];

    }else{
        $succes= array('score'=> "ERROR");
            echo json_encode($succes);
        return;
    }
    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Check connection
    if (!$conn) 
    {
        die("Connection failed: " . mysqli_connect_error());
    }else{

        // needs to be deleted after test <<-------------------
//            echo "$token";

        //check if there is a row of that case with that token
        $sql = "SELECT *
                FROM scores
                WHERE `user_token`='".$token."' AND `case`='".$case."'";

        $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0){

        //tijd in sec vanaf 1970 ofzo
        $time=date('Y-m-d H:i:s');

        $sql= "Select start_time 
              FROM scores WHERE `user_token`='".$token."' AND `case`='".$case."'";


        //get starttime from db and check if it is there
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) 
        {
        // output data of each row
            while($row = $result->fetch_assoc()) 
            {
            //wijzigen in var straks
            $starttime= $row["start_time"];
            }

        } else 
        {
           echo "0 results";
        }
        //score berekenen
        $score = (strtotime($time) - strtotime($starttime));
        
        // kijken of ze over de tijd heen zijn
        if ($score > 600 ) 
        {

            $score = 600; 
        } 
            
        $topscore = 600;

        if($score < 60) 
        {

            $score = 60;

        }      
            
        // score berekenen
        $scoreber= $topscore - $score;
                
        $endscore= array('score'=> "$scoreber");

        $sql= "Select end_time
              FROM scores WHERE `user_token`='".$token."' AND `case`='".$case."'";

        //checken of er een eindtijd is
        $result = mysqli_query($conn, $sql);
        while($row = $result->fetch_assoc()) 
        {
            //score overschrijven 
            $res= $row['end_time'];

        }

        if ($res === NULL) 
        {    
        // update score and endtime
        $sql= "UPDATE scores 
        SET `score`='".$scoreber."', `end_time`='".$time."'
        WHERE `user_token`='".$token."' AND `case`='".$case."'";

        //update table
        if (mysqli_query($conn, $sql)) 
        {
            // de score teruggeven in json
            echo json_encode($endscore);
        } else 
        {
            echo "Error updating record: " . mysqli_error($conn);
        }

        }else{
            // voor als er een derde keer een request wordt gemaakt wordt score uit db gehaald en overschrijft $endscore
            $sql= "Select score
                    FROM scores WHERE `user_token`='".$token."' AND `case`='".$case."'";

            //get endtime if it is there
            $result = mysqli_query($conn, $sql);

            // output data of each row
            while($row = $result->fetch_assoc()) 
            {
            //score overschrijven 
             $endscore= array('score'=> $row["score"]);
            }
            
            // de score teruggeven in json
            echo json_encode($endscore);

        }

        mysqli_close($conn);

    }else
    {
        //insert a new row because it didn't exist, first visit
        $time=date('Y-m-d H:i:s');
        $sql = "INSERT INTO scores (`user_token`, `start_time`, `case`)
        VALUES ('".$token."', '".$time."', '".$case."')"; 

        if (mysqli_query($conn, $sql))
        {
            $succes= array('succes'=> "1");
            echo json_encode($succes);

        } else 
        {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
        mysqli_close($conn);
    }

    }

    ?>
