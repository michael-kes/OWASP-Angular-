<?php

# Get the sqlite connection
DB::sqliteConnection($conn);

##########################################################################

# Datetime for the messages
$datetime = date("d-m-Y H:i");

$data = array();

$messages = $name = $message = null;

# GET request
if (Request::method() == 'GET') {
    # get all messages
    $query = 'SELECT * FROM Message';
    $data['messages'] = $conn->query($query)->fetchAll();

} # POST request INSERT new message in the table
else if (Request::method() == 'POST') {
    # decode JSON input to array
    $input = Request::input(true);

    # get the variables
    $name = Arr::get($input, 'author');
    $message = Arr::get($input, 'message');
    $delete = Arr::get($input, 'delete');
    $MessageID = Arr::get($input, 'MessageID');
    $admin = Arr::get($input, 'admin');


    # only save when author and message are received
    if ($name && $message) ;
    {
        if (!$delete) {
            # INSERT in message table
            $query = 'INSERT INTO Message ("author", "message", "insertDate") VALUES (:name, :message, "' . $datetime . '")';
            $stmt = $conn->prepare($query);
            # bind the parameters with the correct value
            $stmt->bindValue(':name', $name, PDO::PARAM_STR);
            $stmt->bindValue(':message', $message, PDO::PARAM_STR);
            # execute statement and get the result of the INSERT query
        } else if($delete){
            # INSERT in message table
            if($admin){
                $query = 'DELETE FROM Message WHERE MessageID = :id';
                $stmt = $conn->prepare($query);
                $stmt->bindValue(':id', $MessageID, PDO::PARAM_STR);
            } else {
                $query = 'DELETE FROM Message WHERE MessageID = :id AND author = :author';
                $stmt = $conn->prepare($query);
                $stmt->bindValue(':id', $MessageID, PDO::PARAM_STR);
                $stmt->bindValue(':author', $name, PDO::PARAM_STR);
            }
        }

        if ($stmt->execute()) {
            if($admin){
                $data['success'] = array(
                    'score' => 300,
                    'message' => 'Rekt!',
                    'author' => $name
                );

                $caseID = 7;
                DB::mysqlConnection($mysql);

                $result = $mysql->query('
                    SELECT count(*) FROM
                    user_case
                    WHERE user_token = "' . $user->token . '" AND case_id = ' . $caseID);

                if ($result->fetchColumn() == 0) {
                    $mysql->query('
                        INSERT INTO user_case (case_id, user_token, status)
                        VALUES (' . $caseID . ', "' . $user->token . '", "none")
                    ');
                }

                // Apply the update
                $mysql->query('
                    UPDATE user_case SET
                    status = "done",
                    score = 300
                    WHERE user_token = "' . $user->token . '" AND case_id = ' . $caseID);
            }
            # get the new message
            $query = 'SELECT * FROM Message WHERE MessageID = ' . $conn->lastInsertId();
            $data['message'] = $conn->query($query)->fetch();
        }
        # get all messages
        $query = 'SELECT * FROM Message';
        $data['messages'] = $conn->query($query)->fetchAll();
    }
}

# Response data omzetten naar json en printen op het scherm
echo Response::json($data);