<?php
require_once 'GitTask.php';

/**
 * Perform a pull in git.
 * @author Beau Simensen <simensen@gmail.com>
 */
class GitPullTask extends GitTask {

	/**
	 * Path to cloned repository
	 * @var string
	 */
	private $_path = null;
	/**
	 * @todo add a param for public key identity file for remote ssh repos
	private $_ssh_id_file = NULL;
	 * @todo add a param for $remote
	private $_remote = NULL;
	 * @todo add a param for $branch
	private $_branch = NULL;
	 */

	/**
	 * Sets the target path for the cloned repository.
	 */
	public function setPath($path) {
		$this->_path = $path;
	}

	/**
	 * Main entry point.
	 */
	public function main() {

		if(false == isset($this->_path)) {
			$this->log("GitPullTask Fail: PATH must be set!\n");
			exit(1);
		}

		$dir = getcwd();
		chdir($this->_path);

		$cmd = $this->git_path . ' pull';

		$this->log("Running " . $cmd);
		passthru($cmd, $return);
		$this->log("Return: " . $return);

		chdir($dir);

		if(intval($return) > 0) {
			if ( intval)
			$this->log("Git Pull Failed.");
			exit(1);
		}

	}

}
?>