<?php
# Get the sqlite connection
DB::sqliteConnection($conn);

##########################################################################

$data = array();

# GET request
if (Request::method() == 'GET') {
    # get all messages
    //$query = 'SELECT * FROM Message';
    //$data['messages'] = $conn->query($query)->fetchAll();

} # POST request INSERT new message in the table
else if (Request::method() == 'POST') {
    # decode JSON input to array
    $input = Request::input(true)[0];

    if ($input === 2) {

        $caseID = 4;
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
                    score = 350
                    WHERE user_token = "' . $user->token . '" AND case_id = ' . $caseID);

        $data['success'] = array(
            'score' => 350,
            'message' => 'Awesome!'
        );
    }
}

# Response data omzetten naar json en printen op het scherm
echo Response::json($data);