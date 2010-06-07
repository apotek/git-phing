<?php
require_once 'GitTask.php';

/**
 * Perform a checkout in git.
 * @author Beau Simensen <simensen@gmail.com>
 */
class GitCheckoutTask extends GitTask {

	/**
	 * Tag name to checkout
	 * @var string
	 */
	private $_tag_name = null;

	/**
	 * Branch name to checkout
	 * @var string
	 */
	private $_branch_name = null;

	/**
	 * Path to cloned repository
	 * @var string
	 */
	private $_path = null;

	/**
	 * Should the build fail if there's an error?
	 * @var bool
	 */
	private $failOnError = TRUE;

	/**
	 * Sets the name for the git tag.
	 */
	public function setTag($tag_name) {
		$this->_tag_name = $tag_name;
	}

	/**
	 * Sets the name for the git branch.
	 */
	public function setBranch($branch_name) {
		$this->_branch_name = $branch_name;
	}

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
			$this->handledError("GitCheckoutTask Fail: PATH must be set!\n");
		}

		if(false == isset($this->_tag_name) and false == isset($this->_branch_name)) {
			$this->handledError("GitCheckoutTask Fail: TAG or BRANCH must be set!\n");
		}

		$which = null;

		$dir = getcwd();
		chdir($this->_path);

		if ( $this->_tag_name !== null ) {
			$which = $this->_tag_name;
		}

		if ( $this->_branch_name !== null ) {
			$branch_exists = false;
			$check_cmd = $this->git_path . ' branch';
			$output = array();
			$output_string = exec($check_cmd, $output, $return);
			$this->log('Executing: ' . $check_cmd);
			foreach ( $output as $branch ) {
				$branch = preg_replace('/^[\W]*/', '', $branch);
				if ( $branch == $this->_branch_name ) {
					$branch_exists = true;
					break;
				}
			}
			$args = $branch_exists ? '' : ' -b ';
			$addn = $branch_exists ? '' : ' origin/' . $this->_branch_name;
			$which = $args . $this->_branch_name . $addn;
		}

		$cmd = $this->git_path . ' checkout ' . $which;

		$this->log("Running " . $cmd);
		passthru($cmd, $return);
		$this->log("Return: " . $return);

		chdir($dir);

		if(intval($return) > 0) {
			if (intval)
			$errmsg='Git Checkout Failed.';
			$this->log($errmsg);
			$this->handledError($errmsg);
		}

	}

 /**
	* Setter for failOnError property
	*/	
	public function setFailonerror($boolean) {
		$this->failOnError = (bool) $boolean;
	}

	/**
	 * Handle errors according to the failOnError setting
	 */
	private function handledError($errorMessage) {
		if ($this->failOnError) {
			throw new BuildException($errorMessage);
		} else {
			$this->log($errorMessage);
		}
	}

}
?>