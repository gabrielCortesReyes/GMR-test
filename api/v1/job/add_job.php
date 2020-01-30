<?php
error_reporting(E_ALL);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../object/job.php';
include_once '../../shared/database.php';

$executionStartTime = microtime(true);


$database = new Database();
$db = $database->getConnection();

$job = new Job($db);
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->job_id) && !empty($data->submitter_id) && !empty($data->description)) {

  $job->job_id = $data->job_id;
  $job->submitter_id = $data->submitter_id;
  $job->processor_id = 0;
  $job->description = $data->description;
  $job->priority = $data->priority;
  $job->status_id = 0;

  if ($job->addJob()) {
    http_response_code(201);
    $executionEndTime = microtime(true);
    echo json_encode(array(
      "message" => "Job was created.",
      "process_time" => $executionEndTime - $executionStartTime
    ));
  } else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to create job. Data is incomplete."));
  }
}
