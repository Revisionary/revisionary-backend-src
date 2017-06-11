<?php
/*
    Title: PHP Script Background Processer
    Version: 1.1 - By Bilal TAS
    Author: Sanjay Kumar Panda
    Description: Here we can run a PHP file (script) in background, This process is hidden to the end user. It improves your website efficiency.
    URL: https://coderexample.com/how-to-run-a-php-script-in-background/
    Github: https://github.com/pandasanjay/php-script-background-processer
*/

class BackgroundProcess{
    private $pid = "";
    private $command;
    private $debugger = true;
    private $msg = array();

    /*
    * @Param $cmd: Pass the linux command want to run in background
    */
    public function __construct($cmd = null, $run = false){

        if(!empty($cmd)) {
            $this->command=$cmd;
            if ($run) $this->do_process();
        } else {
            $this->msg['error']="Please Provide the Command Here";
        }
    }

    public function setCmd($cmd){
        $this->command = $cmd;
        return true;
    }

    public function setProcessId($pid){
        $this->pid = $pid;
        return true;
    }

    public function getProcessId(){
        return $this->pid;
    }

    public function status($pid = false){
        $command = 'ps -p '.(!$pid ? $this->pid : $pid);
        exec($command,$op);
        if (!isset($op[1]))return false;
        else return true;
    }

    public function showAllPocess(){
        $command = 'ps -ef '.$this->pid;
        exec($command,$op);
        return $op;
    }

    public function start(){
        if ($this->command != '') $this->do_process();
        else return false;
    }

    public function stop(){
        $command = 'kill '.$this->pid;
        exec($command);
        if ($this->status() == false) return true;
        else return false;
    }

    //do the process in background
    public function do_process(){
        $command = $this->command.' > /dev/null 2>&1 & echo $!';
        exec($command ,$pross);
        $this->pid = (int)$pross[0];
    }

    /*
    * To execute a PHP url on background you have to do the following things.
    * $process=new BackgroundProcess("curl -s -o <Base Path>/log/log_storewav.log <PHP URL to execute> -d param_key=<Param_value>");
    */

}


/*

USAGE

// Our command
$cmd = 'php '.realpath('.').'/process.php hello world';


// Auto start
//$process = new BackgroundProcess($cmd, true);

// Or set command and use .start() / .stop()
$process = new BackgroundProcess($cmd);
//$process->start();
$process->setProcessId(36998);


// Status check
if ($process->status()){
    echo " The process <b>".$process->getProcessId()."</b> is currently running";
}else{
    echo " The process <b>".$process->getProcessId()."</b> is not running.";
}


// List all the process
echo "<pre>";
print_r($process->showAllPocess());
echo "</pre>";
*/


?>