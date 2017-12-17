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

		$db->orderBy('queue_ID', 'asc');

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


		// Allow only one or two job at a time !!!
		if (
			$current_works[0]['queue_ID'] == $queue_ID
			//|| $current_works[1]['queue_ID'] == $queue_ID
		) {

			$logger->info("Job $queue_ID is ready!");
			return true;

		}

		$logger->info("Job $queue_ID is not ready yet. Waiting $count job(s).");
	    return false;
    }


    // Queue Info
    public function info($queue_ID) {
	    global $db;

		$db->where('queue_ID', $queue_ID);
		return $db->getOne('queues');
    }





    // JOBS:

    // Add a new job to the queue
    public function new_job($queue_type, $queue_object_ID, $queue_message = "", $session_ID = null) {
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


			// START THE PROCESS IF TYPE IS INTERNALIZE
			if ($queue_type == 'internalize') {

				$page_ID = $queue_object_ID;


				// Initiate Internalizator
				$process = new Cocur\BackgroundProcess\BackgroundProcess('php '.dir.'/app/bgprocess/internalize_v3.php '.$page_ID.' '.$session_ID.' '.$queue_ID);
				$process->run(Page::ID($page_ID)->logDir."/internalize-tasks-php.log", true);
				$process_ID = $process->getPid();


				// Add the PID to the queue
				$this->update_status($queue_ID, "waiting", "Waiting other works to be done.", $process_ID);

				return [
					'queue_ID' => $queue_ID,
					'process_ID' => $process_ID
				];

			}


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
			'queue_message' => $queue_message
		);


		// If PID is written
		if ($queue_PID !== null)
			$data['queue_PID'] = $queue_PID;


		$db->where ('queue_ID', $queue_ID);
		if ($db->update('queues', $data)) {

			$logger->info($db->count.' Queue Updated:', $data);
			return true;

		}


		$logger->error( $db->getLastError() );
		return false;
    }

}