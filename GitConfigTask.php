<?php
require_once 'GitTask.php';

/**
 * Interface to using git config command
 *
 * @author Kristofer Widholm <help@theapotek.com>
 */
class GitConfigTask extends GitTask {

	/** @string $repository: path to the local Git repository */
	private $path = '';
	private $property = '';
  private $value = '';
	private $failOnError = TRUE;

	/**
	 * Main entry point.
	 */
	public function main() {

		if (empty($this->path)) {
			$this->handledError("You must set the 'path' property");
		}
		if (empty($this->property)) {
			$this->handledError("You must set the property, or we won't know what to configure.");
		}
		if (empty($this->value)) {
			$this->handledError("You must set the property's value, or we won't know what to set {$this->property} to.");
		}

		$current = getcwd();
		chdir($this->path);
		$cmd = $this->git_path .' config '. escapeshellarg($this->property) .' '. escapeshellarg($this->value);
		$this->log("Running `$cmd` in directory {$this->repository}");
		passthru($cmd, $return);
		chdir($current);
		/** 
		 * git-commit return code checks. 
		 0: success
		 1: nothing committed, but not really an error
		 ....: add others
		 128: Not a git repository
		 etc.*/
		switch($return) {
			case 0:
				$this->log('Config was successful.');
				break;
			case 128:
				$this->handledError("{$this->path} does not contain a valid git repository. Config failed.");
				break;
			default:
				$this->handledError('Unknown error during config call.');
				break;
		}
	}

	
	/**
	 * Setter for path property
	 * This should be set to the root directory of the git repository you are working with
	 */
	public function setPath($path) {
		if (!file_exists($path) || !is_dir($path)){
      $this->handledError('The passed local git repository path is not a valid directory');
		}
		if (!file_exists("$path/.git")) {
			$this->handledError("No git repository found at '$path/.git'");
		}
		$this->path = $path;
	}

	/** setter for property property. */
	function setProperty($property) {
	  $this->property = (string) $property;
	}

	/** setter for value property. */
	function setValue($value) {
	  $this->value = (string) $value;
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