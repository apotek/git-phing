<?php
require_once 'GitTask.php';

/**
 * Pushes a git repo to some remote repo.
 *
 * @author Zach Campbell <zacharydangercampbell@gmail.com>
 */
class GitPushTask extends GitTask {
	private $_remote = '';
	private $_branch = '';
	private $_path = '';

	/**
	 * Main entry point.
	 */
	public function main() {
		if (!empty($this->_path)) {
			$current = getcwd();
			chdir($this->_path);
		}
		$cmd = $this->git_path .' push ';
		if ($this->_remote) {
			$cmd .= ' '. $this->_remote;
			if ($this->_branch) {
				$cmd .= ' '. $this->_branch;
			}
		}

		$this->log("Pushing: " . $cmd);
		passthru($cmd, $return);
		if (isset($current)) {
			chdir($current);
		}
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
