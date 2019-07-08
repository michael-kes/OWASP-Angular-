<?php

# Get the sqlite connection
DB::sqliteConnection($conn);

##########################################################################


$query = 'SELECT * FROM persons';
$result = $conn->query($query);

$result = $result->fetchAll();

# Data die is gepost naar de server ophalen
$input = Request::input(true);


$email = Arr::get($input, 'email');
$password = Arr::get($input, 'password');


# Save query
$query = 'SELECT * FROM persons WHERE (email = :email) AND (password = :password)';
$stmt = $conn->prepare($query);
$stmt->bindValue(':email', $email, PDO::PARAM_STR);
$stmt->bindValue(':password', $password, PDO::PARAM_STR);
$stmt->execute();
$saveResult = $stmt->fetch();

# Vulnerable Query
$query = 'SELECT * FROM persons WHERE (email = \''.$email.'\') AND (password = \''.$password.'\')';
$result = false;
$error = null;

try {
    $result = $conn->query($query)->fetch();
} catch (PDOException $e) {
    $error = $e->getMessage();
}

# Does the user have completed the hack
$hacked = $result && ! $saveResult;

if ($hacked)
{
    $caseID = 1;
    DB::mysqlConnection($mysql);

    $result = $mysql->query('
        SELECT count(*) FROM
        user_case
        WHERE user_token = "'.$user->token.'" AND case_id = '.$caseID);

    if ($result->fetchColumn() == 0)
    {
        $mysql->query('
            INSERT INTO user_case (case_id, user_token, status)
            VALUES ('.$caseID.', "'.$user->token.'", "none")
        ');
    }

    // Apply the update
    $mysql->query('
        UPDATE user_case SET
        status = "done",
        score = 250
        WHERE user_token = "'.$user->token.'" AND case_id = '.$caseID);
}

# Response data opstellen
$data = array(
    'query'             => $query,
    'result'            => $result,
    'error'             => $error
);

if ($hacked)
{
    $data['success'] = array(
        'score'     => 250,
        'message'   => 'Geweldig!'
    );
}

# Response data omzetten naar json en printen op het scherm
echo Response::json($data);