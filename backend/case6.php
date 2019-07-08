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
	$login = Arr::get($input, 'login');
	$admin = Arr::get($input, 'admin');
	if ($login) {

		$caseID = 6;
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

		$casestatus = $mysql->query('SELECT status FROM user_case WHERE user_token = "' . $user->token . '" AND case_id = ' . $caseID)->fetch(PDO::FETCH_ASSOC);
		// Apply the update
		//print_r($casestatus);
		if(!$admin && $casestatus['status'] === "opened"){
			$mysql->query('
                    UPDATE user_case SET
                    status = "in progress",
                    score = 250
                    WHERE user_token = "' . $user->token . '" AND case_id = ' . $caseID);

			$data['success'] = array(
				'score' => 250,
				'message' => 'Keep going!'
			);
		} else if($admin && $casestatus['status'] !== "done") {
			$mysql->query('
                    UPDATE user_case SET
                    status = "done",
                    score = 600
                    WHERE user_token = "' . $user->token . '" AND case_id = ' . $caseID);

			$data['success'] = array(
				'score' => 350,
				'message' => 'Good job'
			);
		}
		//print_r($mysql->query('SELECT status FROM user_case WHERE user_token = "' . $user->token . '" AND case_id = ' . $caseID)->fetch(PDO::FETCH_ASSOC));
	}
}

# Response data omzetten naar json en printen op het scherm
echo Response::json($data);