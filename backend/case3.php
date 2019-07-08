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
    $input = Request::input(true);

    # get the variables
    $message = Arr::get($input, 'message');

    if (preg_match('#<script(.*?)>(.*?)</script>#is', $message)) {

        $caseID = 3;
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

        $data['success'] = array(
            'score' => 300,
            'message' => 'Waazzaaahhh!'
        );
    }
}

# Response data omzetten naar json en printen op het scherm
echo Response::json($data);