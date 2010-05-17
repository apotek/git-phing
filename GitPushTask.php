<?php
require_once 'GitTask.php';

/**
 * Pushes a git repo to some remote repo.
 *
 * @author Zach Campbell <zacharydangercampbell@gmail.com>
 */
class GitPushTask extends GitTask {
	private $_remote;
	private $_branch;
	private $_path;

	/**
	 * Main entry point.
	 */
	public function main() {
		$current = getcwd();
		chdir($this->_path);
		$command = $this->git_path . " push " . $this->_remote . " " . $this->_branch;
		$this->log("Pushing: " . $command);
		passthru($command, $return);
		chdir($current);
		$this->log("Push Return: " . $return);
	}

	/**
	 * Sets the target path for the repository to push
	 */
	public function setPath($path) {
		$this->_path = $path;
	}

	/**
	 * Sets the remote repository to push to.
	 */
	public function setRemote($remote) {
		$this->_remote = $remote;
	}

	/**
	 * OPTIONAL: Set the branch for the git push command.
	 */
	public function setBranch($branch) {
		$this->_branch = $branch;
	}
}
?>
