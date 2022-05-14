<?php
function originAccess($host = "https://anvarmaksudov.vercel.app"): bool
{
    if ($host === $_SERVER['HTTP_ORIGIN']) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: *');
        header("Access-Control-Allow-Headers: *");
        return true;
    }
    return false;
}

function cryptPassword($val, $type)
{
    $cryptList = array(
        'des' => 'rl',
        'md5' => '$1$rasmusle$',
        'blowshift' => '$2a$07$usesomesillystringforsalt$',
        'sha-256' => '$5$rounds=5000$usesomesillystringforsalt$',
        'sha-562' => '$6$rounds=5000$usesomesillystringforsalt$'
    );
    if ($cryptList[$type]) return crypt($val, $cryptList[$type]);
    return '';
}

function checkValidate($data): string
{
    $data = strip_tags($data);
    $data = htmlentities($data, ENT_QUOTES, "UTF-8");
    return htmlspecialchars($data, ENT_QUOTES);
}

function dateZone(): array
{
    $return = array();
    $zone = new DateTime("now", new DateTimeZone('Asia/Tashkent'));
    $return['years'] = $zone->format('Y');
    $return['month'] = $zone->format('m');
    $return['date'] = $zone->format('d');
    $return['hours'] = $zone->format('H');
    $return['minutes'] = $zone->format('i');
    $return['seconds'] = $zone->format('s');

    return $return;
}


function filterUserType($type): string
{
    $types = array(
        'admin' => "Admin",
        'graphic-designer' => "Grafik Dizayner",
        'video-maker' => "Video Montajchi",
        'teacher' => "O'qituvchi",
    );
    return $types[$type];
}

function infoReturn($info, $type): array
{
    return array(
        'content' => $info,
        'type' => $type,
    );
}

function checkUserLogged(): bool
{
    $username = checkValidate($_POST['check-username']);
    $id = checkValidate($_POST['check-id']);

    $user = getUser('', "username='$username'");
    if (cryptPassword($user[0]['id'], 'md5') . cryptPassword($user[0]['password'], 'md5') === $id) return true;
    return false;
}

function checkUser(): string
{
    $return = "";
    $username = checkValidate($_POST['check-username']);
    $con = connect();
    $sql = "SELECT type FROM accounts WHERE username='$username'";
    $result = $con->query($sql);
    if ($result->num_rows > 0)
        while ($row = $result->fetch_assoc()) {
            $return = $row['type'];
        }
    return $return;
}

function getCRMData(): array
{
    $con = connect();
    $sql = "SELECT * FROM crm_data";
    $result = $con->query($sql);
    $return = array();
    while ($row = $result->fetch_assoc()) {
        $return['ip'] = $row['work_ip'];
    }
    return $return;
}

function getUserLocation()
{
    $url = "http://ipinfo.io?token=8eba98c3e38551";
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache"
        ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    return $response;
}

function getUser($dontGet = '', $where = ''): array
{
    $return = array();
    $sql = "SELECT * FROM accounts" . ($where ? " WHERE $where" : "");
    $con = connect();
    $result = $con->query($sql);
    if ($result->num_rows > 0)
        while ($row = $result->fetch_assoc()) {
            if ($row['type'] !== $dontGet)
                $return[] = array(
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'lastname' => $row['lastname'],
                    'username' => $row['username'],
                    'password' => $row['password'],
                    'phone' => $row['phone'],
                    'type' => $row['type'],
                    'work-start-time' => $row['work_start_time'],
                    'work-end-time' => $row['work_end_time'],
                );
        }
    $con->close();
    return $return;
}

function removeUser()
{
}

function checkSqlUsername($username): bool
{
    $con = connect();
    $sql = "select * from accounts where username='$username'";
    $result = $con->query($sql);
    if ($result->num_rows)
        return false;
    return true;
}

function createUser($name, $lastname, $username, $password, $type, $social, $phone, $workStartTime, $workEndTime, $access, $appended_time): bool
{
    if (checkSqlUsername($username)) {
        $con = connect();
        $sql = "INSERT INTO accounts (id, name, lastname, username, password, type, social, phone, work_start_time, work_end_time, access, added_time) 
            VALUES ('','$name','$lastname','$username','$password','$type', '$social', '$phone', '$workStartTime', '$workEndTime', '$access', $appended_time)";
        if ($con->query($sql))
            return true;
    }
    return false;
}

function updateUser()
{
}

function getWorkTime($where = "")
{
    $return = array();
    $con = connect();
    $sql = "SELECT * FROM work_time" . ($where ? " WHERE $where" : "");
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $return[] = array(
                'id' => $row['id'],
                'name' => $row['name'],
                'lastname' => $row['lastname'],
                'username' => $row['username'],
                'work-start-time' => $row['work_start_time'],
                'work-start-location' => $row['work_start_location'],
                'work-end-time' => $row['work_end_time'],
                'work-end-location' => $row['work_end_location'],
                'date' => $row['date'],
                'time' => $row['time'],
            );
        }
    } else {
        $return = false;
    }
    return $return;
}

function createWorkTime($workTime, $location): array
{
    $dateZone = dateZone();
    $date = $dateZone['years'] . "-" . $dateZone['month'] . "-" . $dateZone['date'];
    $usernameCheck = checkValidate($_POST['check-username']);
    $dateNow = getWorkTime("username='$usernameCheck' AND date='$date'");

    $userID = getUser('', "username='$usernameCheck'")[0]['id'];
    $name = getUser('', "username='$usernameCheck'")[0]['name'];
    $lastname = getUser('', "username='$usernameCheck'")[0]['lastname'];
    $username = getUser('', "username='$usernameCheck'")[0]['username'];

    $time = time();

    $con = connect();
    if (!$dateNow) {
        $sql = "INSERT INTO work_time (id, user_id, name, lastname, username, work_start_time, work_end_time, work_start_location, work_end_location, date, time) 
            VALUES ('','$userID','$name','$lastname','$username','$workTime','','$location','[]','$date','$time')";
        $con->query($sql);
    }
    $return = array();
    $return['info'] = infoReturn("Ish boshlandi", "success");
    return $return;
}

function updateWorkTime($workTime, $date, $location): array
{
    $usernameCheck = checkValidate($_POST['check-username']);

    $con = connect();
    $sql = "select * from work_time where username='$usernameCheck' and date='$date'";
    $result = $con->query($sql);
    $return = array();
    if ($result->num_rows)
        while ($row = $result->fetch_assoc())
            if ($row['work_end_time'] === '') {
                $username = checkValidate($_POST['check-username']);
                $id = getWorkTime("username='$username' AND date='$date'")[0]['id'];
                $con = connect();
                $sql = "UPDATE work_time SET work_end_time='$workTime', work_end_location='$location' WHERE id='$id'";
                $con->query($sql);
                $return['info'] = infoReturn("Ish tugatildi", "success");
            } else {
                $return['info'] = infoReturn("Ish tugatilgan", "warn");
            }

    return $return;
}

function removeWorkTime()
{
}
