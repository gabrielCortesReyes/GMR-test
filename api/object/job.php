<?php

class Job
{
  private $conn;

  public $job_id;
  public $submitter_id;
  public $processor_id;
  public $status_id;
  public $priority;
  public $description;

  public function __construct($db)
  {
    $this->conn = $db;
  }

  public function read()
  {
    $query = "SELECT j.*, s.status_name FROM job j, status s where j.status_id = s.status_id";

    $stmt = $this->conn->prepare($query);
    $stmt->execute();

    return $stmt;
  }

  public function getUnfinishJob()
  {
    $query = "SELECT j.*, s.status_name 
              FROM job j, status s 
              WHERE j.status_id = s.status_id AND
              j.processor_id = 0 AND 
              j.status_id = 0 
              ORDER BY FIELD(priority,'HIGH','MEDIUM','LOW') LIMIT 1";


    $stmt = $this->conn->prepare($query);
    $stmt->execute();

    return $stmt;
  }

  public function setProcessor($job_id, $processor_id, $status_id)
  {
    $query = "UPDATE job SET processor_id=:processor_id, status_id=:status_id WHERE job_id=:job_id";
    $stmt = $this->conn->prepare($query);

    $job_id = htmlspecialchars(strip_tags($job_id));
    $processor_id = htmlspecialchars(strip_tags($processor_id));
    $status_id = htmlspecialchars(strip_tags($status_id));

    $stmt->bindParam(":job_id", $job_id);
    $stmt->bindParam(":processor_id", $processor_id);
    $stmt->bindParam(":status_id", $status_id);

    if ($stmt->execute()) {
      return true;
    }

    return false;
  }

  public function readOne($job_id)
  {
    $query = "SELECT j.*, s.status_name FROM job j, status s where j.status_id = s.status_id and j.job_id = " . $job_id . ";";

    $stmt = $this->conn->prepare($query);
    $stmt->execute();

    return $stmt;
  }

  public function addJob()
  {
    $query = "INSERT INTO job SET 
              job_id=:job_id, 
              submitter_id=:submitter_id,
              processor_id=:processor_id,
              status_id=:status_id,
              priority=:priority,
              description=:description";

    $stmt = $this->conn->prepare($query);

    $this->job_id = htmlspecialchars(strip_tags($this->job_id));
    $this->submitter_id = htmlspecialchars(strip_tags($this->submitter_id));
    $this->processor_id = htmlspecialchars(strip_tags($this->processor_id));
    $this->status_id = htmlspecialchars(strip_tags($this->status_id));
    $this->priority = htmlspecialchars(strip_tags($this->priority));
    $this->description = htmlspecialchars(strip_tags($this->description));

    $stmt->bindParam(":job_id", $this->job_id);
    $stmt->bindParam(":submitter_id", $this->submitter_id);
    $stmt->bindParam(":processor_id", $this->processor_id);
    $stmt->bindParam(":status_id", $this->status_id);
    $stmt->bindParam(":priority", $this->priority);
    $stmt->bindParam(":description", $this->description);

    if ($stmt->execute()) {
      return true;
    }

    return false;
  }
}
