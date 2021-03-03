<?php


/*
 *
 * Methods: tanken, delete, insert, update, get, get_by_id
 * Optional: id
 *
 */

define('DB_NAME', 'm307_tobias');
define('DB_USER', 'root');
define('DB_PSWD', '');
define('DB_HOST', 'localhost');
define('DB_TABLE', 'tobias_inventar');

$response = [];

checkDB();

$conn = new mysqli(DB_HOST, DB_USER, DB_PSWD, DB_NAME);

if($conn->connect_errno) {
    $response['success'] = false;
    $response['data'] = 'DB-Connection error';
}


$method = 'none';
if(isset($_GET['method'])) {
    $method = $_GET['method'];
    $request = $_GET;
}
if(isset($_POST['method'])) {
    $method = $_POST['method'];
    $request = $_POST;
}


if($method !== 'none' && empty($response)) {
    switch ($method) {
        /*case 'tanken':
            $success = true;
            if(isset($_REQUEST['id'])) {
                $sql = 'UPDATE ' . DB_TABLE . ' SET `tank` = (`tank` + 1) WHERE `id` = ' . $_REQUEST['id'];
                $conn->query($sql);
            } else {
                $success = false;
                $response['error'] = 'id not set';
            }
            $response['success'] = $success;
            $response['method'] = 'tanken';
            break;*/
        case 'delete':  // GET, Method, ID

            $success = true;
            if(isset($_REQUEST['id'])) {
                $sql = 'DELETE FROM ' . DB_TABLE . ' WHERE `id` = ' . $_REQUEST['id'];
                $conn->query($sql);
            } else {
                $success = false;
            }
            $response['success'] = $success;
            $response['method'] = 'delete';

            break;
        case 'insert': // Post, Method and Form
            $name       = $_REQUEST['name'];
            $invnr      = $_REQUEST['invnr'];
            $kategorie  = $_REQUEST['kategorie'];
            $date       = isset($_REQUEST['date']) ? $_REQUEST['date'] : '0000-00-00';
            if($date) {
                $date_obj = DateTime::createFromFormat('d.m.Y', $date);
                $date = date_format($date_obj, 'Y-m-d');
            }
            $bemerkung  = isset($_REQUEST['bemerkung']) ? $_REQUEST['bemerkung'] : '';
            $error_msg  = [];

            $success[] = checkRequiredField($name, 'Gerätename', $error_msg);
            $success[] = checkRequiredField($invnr, 'Inventarnummer', $error_msg);
            $success[] = checkRequiredField($kategorie, 'Kategorie', $error_msg);
            $success = !in_array(false, $success);
            if($success) {
                $query = "INSERT INTO " . DB_TABLE . " (`name`, `invnr`, `kategorie`, `date`, `bemerkung`) VALUES ('$name', '$invnr', '$kategorie', '$date', '$bemerkung')";
                $conn->query($query);
            }


            $response['success'] = $success;
            $response['message'] = $error_msg;
            $response['method']  = 'insert';
            $response['id']      = $conn->insert_id;;
            break;
        case 'update':
            $name       = $_REQUEST['name'];
            $invnr      = $_REQUEST['invnr'];
            $kategorie  = $_REQUEST['kategorie'];
            $date       = isset($_REQUEST['date']) ? $_REQUEST['date'] : '';
            $date_obj   = DateTime::createFromFormat('d.m.Y', $date);
            $date       = date_format($date_obj, 'Y-m-d');
            $bemerkung  = isset($_REQUEST['bemerkung']) ? $_REQUEST['bemerkung'] : '';
            $id         = $_REQUEST['id'];
            $error_msg  = [];

            $success[] = checkRequiredField($name, 'Gerätename', $error_msg);
            $success[] = checkRequiredField($invnr, 'Inventarnummer', $error_msg);
            $success[] = checkRequiredField($kategorie, 'Kategorie', $error_msg);
            $success   = !in_array(false, $success);
            if($success) {
                $query = "UPDATE " . DB_TABLE . " SET `name` = '$name', `invnr` = '$invnr', `kategorie` = '$kategorie', `date` = '$date', `bemerkung` = '$bemerkung' WHERE `id` = $id";
                $conn->query($query);
            }


            $response['success'] = $success;
            $response['message'] = $error_msg;
            $response['method'] = 'update';
            $response['id'] = $id;
            break;
        case 'get':

            $was = isset($_REQUEST['col']) ? $_REQUEST['col'] : '*';
            $query = 'SELECT ' . $was . ' FROM ' . DB_TABLE;
            $id = isset($request['id']) ? intval($request['id']) : -1;
            if($id !== -1) $query .= ' WHERE `id`=' . $id . ';';

            $result = $conn->query($query);

            $success = $conn->errno == 0;

            $data = [];
            $error_msg = [];
            if($success) {
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $row['date'] = date_format(DateTime::createFromFormat('Y-m-d', $row['date']), 'd.m.Y');
                        $data[] = $row;
                    }
                }
            } else {
                $error_msg[] = 'Daten konnten nicht geholt werden!';
            }

            $response['success'] = $success;
            $response['method'] = 'delete';
            $response['message'] = $error_msg;
            $response['data'] = $data;
            break;
        default:
            $response['success'] = false;
            $response['method'] = $method;
            $response['data'] = 'unknown method';
            break;
    }

} else {
    $response['success'] = false;
    $response['data'] = 'no method declared';
}

$conn->close();

echo json_encode($response);

function checkRequiredField($string, $field, &$error_msg) {

    if(strlen($string) < 3 || strlen($string) > 255) {
        $error_msg[] = 'Feld "' . $field . '" muss mindestens 3 Zeichen und höchstens 255 Zeichen lang sein.';
        return false;
    }

    return true;
}

function checkDB() {
    $con = NULL;
    if($con = new mysqli(DB_HOST, DB_USER, DB_PSWD)) {
        if (!$con->select_db(DB_NAME)) {
            $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME . " DEFAULT CHARACTER SET utf8";
            $con->query($sql);
            $con->select_db(DB_NAME);
            $sql = "CREATE TABLE ". DB_TABLE . " (
                id INT NOT NULL AUTO_INCREMENT,
                name VARCHAR(255) NOT NULL,
                invnr VARCHAR(255) NOT NULL,
                kategorie enum('Computer', 'Audio', 'Monitor') DEFAULT('Computer'),
                `date` DATE NOT NULL,
                bemerkung VARCHAR(255) NOT NULL,
                PRIMARY KEY(id)
            )";

            $con->query($sql);

            $sql = "INSERT INTO
                      " . DB_TABLE . " (`name`, `invnr`, `kategorie`, `date`, `bemerkung`)
                    VALUES
                      ('Apple Macbook Air 13.3\"', 'KL156', 'Computer', '2016-01-01', 'Bemerkung'),
                      ('Apple Magic Mouse 2', 'ZL862', 'Audio', '2017-01-01', 'Bemerkung'),
                      ('Apple Thunderbolt/Ethernet', 'DL866', 'Monitor', '2018-01-01', 'Bemerkung')";
            $con->query($sql);
        }
    }
}

?>