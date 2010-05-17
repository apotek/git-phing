<?php
require_once 'GitTask.php';

/**
 * Creates a new tag in git.
 *
 * @author Zach Campbell <zacharydangercampbell@gmail.com>
 */
class GitTagTask extends GitTask {
	private $_tag_name;

	/**
	 * Sets the name for the new git tag.
	 */
	public function setTag($tag_name) {
		$this->_tag_name = $tag_name;
	}

	/**
	 * Sets the path for the git repo to tag
	 */
	public function setPath($path) {
		$this->_path = (string) $path;
	}

	/**
	 * Main entry point.
	 */
	public function main() {
		$current = getcwd();
		chdir($this->_path);
		$cmd = $this->git_path . " tag " . escapeshellarg($this->_tag_name);
		$this->log("Running " . $cmd);
		passthru($cmd, $return);
		chdir($current);
		$this->log("Return: " . $return);
	}
}
?>
