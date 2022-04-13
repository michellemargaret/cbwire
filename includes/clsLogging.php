<?php
/**  
 * Logging class:  
 * - contains lopen and lwrite methods  
 * - lwrite will write message to the log file  
 * - first call of the lwrite will open log file implicitly  
 * - message is written with the following format: hh:mm:ss (script name) message  
 * - From http://www.redips.net/php/write-to-log-file/
 */  
class Logging{   
  // define log file   
  private $log_file = 'errs.log';
  
  // define file pointer   
  private $fp = null;   
  // write message to the log file   
  public function lwrite($message){   
    // if file pointer doesn't exist, then open log file   
    if (!$this->fp) $this->lopen();  

    // define script name   
    $script_name = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME); 

    // define current time   
    $time = date('H:i:s');   

    // write current time, script name and message to the log file   
    fwrite($this->fp, "$time ($script_name) $message\n");   
  }   
  // open log file   
  private function lopen(){   
    // define log file path and name   
    $lfile = $this->log_file;  
    // define the current date (it will be appended to the log file name)   
    $today = date('Y-m-d');   
    // open log file for writing only; place the file pointer at the end of the file   
    // if the file does not exist, attempt to create it   
    $this->fp = fopen($lfile . '_' . $today, 'a') or exit("Can't open $lfile!");   
  }  
  // Used in recording errors func.inc.php : function arrayToString($inArray)
	public function print_log() {
		return "clsLoggin";
	} 
	
	// Read the log file
	
	public function lread(){   
		// if file pointer doesn't exist, then open log file   
		$arrFiles = array();
		if (is_dir($this->log_directory)) {
			if ($dh = opendir($this->log_directory)) {
				while (($file = readdir($dh)) !== false) {
					if (($file <> ".") && ($file <> "..")) {
						$arrFiles[] = $this->log_directory . $file; 
					}
				}
				closedir($dh);
			}
		}
		
		echo "Log File Count: <b>" . count($arrFiles) . "</b><br><br>Logs:<br>";
		
		foreach ($arrFiles as $strFile) {
			echo "
				********************************************************************************************<BR>
				" . $strFile . "<br>
				********************************************************************************************<br>";
			 echo "<pre>" . file_get_contents($strFile) . "</pre>";
			 echo "<br><br><br><br>";
		}
	}
	
	// Return the number of files in the log directory
	public function lcount(){   
		// if file pointer doesn't exist, then open log file   
		$arrFiles = array();
		if (is_dir($this->log_directory)) {
			if ($dh = opendir($this->log_directory)) {
				while (($file = readdir($dh)) !== false) {
					if (($file <> ".") && ($file <> "..")) {
						$arrFiles[] = $this->log_directory . $file; 
					}
				}
				closedir($dh);
			}
		}
		
		echo "Log File Count: <b>" . count($arrFiles) . "</b>";
	}
}  
?>