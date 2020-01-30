<?php
include_once '../../shared/headers/header.php';
include_once '../../object/job.php';
include_once '../../shared/database.php';
$executionStartTime = microtime(true);

$database = new Database();
$db = $database->getConnection();
$job = new Job($db);

$stmt = $job->read();
$num = $stmt->rowCount();

if ($num > 0) {

  $job_arr["records"] = array();
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $job_item = array(
      "job_id" =>  $row["job_id"],
      "description" => $row["description"],
      "submitter_id" => $row["submitter_id"],
      "processor_id" => $row["processor_id"],
      "status" => $row["status_name"],
      "priority" => $row["priority"]
    );
    array_push($job_arr["records"], $job_item);
  }

  $executionEndTime = microtime(true);
  array_push($job_arr["records"], array("process_time" => $executionEndTime - $executionStartTime));

  http_response_code(200);
  echo json_encode($job_arr);
} else {
  http_response_code(404);

  echo json_encode(
    array("message" => "No jobs found.")
  );
}
