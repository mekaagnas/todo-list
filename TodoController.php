<?php

header("Content-Type:application/json");
include('connection.php');

function cors() {
    
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');  
    }
    
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         
        
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    
        exit(0);
    }
    
}

cors();

if ($con->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$choice = $_POST['choice'];


switch($choice)
{
    case 'store':
        store($con);
        break;
    case 'update':
        update($con);
        break;
    case 'delete':
        delete($con);
        break;
    case 'index':
        index($con);
        break;
}

function store($con){
    
    $stmt = $con->prepare("INSERT INTO todo (name) VALUES (?)");
    $stmt->bind_param("s", $name);

    $name = $_POST['name'];
    $stmt->execute();

    $response['data'] = array('id' => $con->insert_id, 'name' => $name);
    $response['status'] = 200;



    echo json_encode($response);

}

function update($con){
    $name = $_POST['name'];
    $id = $_POST['id'];

    $sql = "UPDATE todo SET name= '$name' WHERE id=$id";

    if($con->query($sql) === TRUE) {
        $response['status'] = '200';
        $response['data'] = array('name' => $name, 'id' => $id);
    } else {
        $response['status'] = '500';
        $response['message'] = 'Server error' . $con->error;
    }

    echo json_encode($response);
}

function delete($con){
    $id = $_POST['id'];

    $sql = "DELETE FROM todo WHERE id=$id";

    if ($con->query($sql) === TRUE) {
        $response['status'] = '200';
        $response['id'] = $id;
    } else {
        $response['status'] = '500';
        $response['message'] = 'Server error';
        $response['id'] = $id;
    }

    echo json_encode($response);
}

function index($con){
    $sql = "SELECT id,name FROM todo";
    $result = $con->query($sql);
    $jsonResult = array();

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            array_push($jsonResult, array('id' => $row['id'],'name' => $row['name']));
        }
        echo json_encode($jsonResult);
      } 
}

?>
