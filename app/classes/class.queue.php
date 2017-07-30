<?php

class Queue {



	// SETTERS:

	public function __construct() {


    }




	// GETTERS:

    // Get all the works on queue
    public function works($queue_type = "") {
	    global $db, $logger;

	    $db->where('queue_status', 'waiting');
	    $db->orWhere('queue_status', 'working');

	    $works = $db->get('queues');
		if ($works) {

		    $logger->info("Current Queue:", $works);
		    return $works;

		}

	    return false;
    }


    // Is the job ready?
    public function isReady($queue_ID) {
	    global $logger;

		// Current Queue
	    $current_works = $this->works();
	    $count = count($current_works) - 1;


		if ($current_works[0]['queue_ID'] == $queue_ID) {

			$logger->info("Job $queue_ID is ready!");
			return true;

		}

		$logger->info("Job $queue_ID is not ready yet. Waiting $count job(s).");
	    return false;
    }





    // JOBS:

    // Add a new job to the queue
    public function new_job($queue_type, $queue_object_ID, $queue_message = "") {
	    global $db, $logger;

		$data = array(
			"queue_type" => $queue_type,
			"queue_object_ID" => $queue_object_ID,
			"queue_message" => $queue_message,
			"user_ID" => currentUserID()
		);

		$queue_ID = $db->insert('queues', $data);

		if($queue_ID) {

			$logger->info('Queue Added: '.$queue_ID, $data);
			return $queue_ID;

		}

		$logger->error( $db->getLastError() );
		return false;
    }


    // Update the queue status
    public function update_status($queue_ID, $queue_status, $queue_message = "", $queue_PID = null) {
	    global $db, $logger;


		$data = array(
			'queue_status' 	=> $queue_status,
			'queue_message' => $queue_message,
			'queue_PID' 	=> $queue_PID
		);

		$db->where ('queue_ID', $queue_ID);
		if ($db->update('queues', $data)) {

			$logger->info($db->count.' Queue Updated:', $data);
			return true;

		}


		$logger->error( $db->getLastError() );
		return false;
    }

}