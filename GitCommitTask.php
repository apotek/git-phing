<?php
require_once 'GitTask.php';

/**
 * Adds files in a fileset to a repository.
 *
 * @author Kristofer Widholm <help@theapotek.com>
 */
class GitCommitTask extends GitTask {

	/** @string $repository: path to the local Git repository */
	private $repository = '.';
  private $comment = '';
	private $failOnError = TRUE;
  private $allModified = TRUE;

	/**
	 * Main entry point.
	 */
	public function main() {

		if (empty($this->repository)) {
            throw new BuildException("You must set the 'repository' property");
		}
		if (empty($this->comment)) {
            throw new BuildException("You must set the commit 'comment' property, or add a comment within <gitcommit></gitcommit> tags.");
		}

		$current = getcwd();
		chdir($this->repository);
		$opts = '-m';
		if ($this->allModified) {
			$opts = '-a '. $opts;
		}
		$cmd = $this->git_path .' commit '. $opts .' '. escapeshellarg($this->comment);
		$this->log("Running `$cmd` in directory {$this->repository}");
		passthru($cmd, $return);
		chdir($current);
		/** 
		 * git-commit return code checks. 
		 0: items committed successfully
		 1: nothing committed, but not really an error
		 ....: add others
		 128: Not a git repository
		 etc.*/
		switch($return) {
			case 0:
				$this->log('Commit was successful.');
				break;
			case 1:
				$this->log('Nothing to commit.');
				break;
			case 128:
				$this->handledError("{$this->repository} does not contain a valid git repository. Commit failed.");
				break;
			default:
				$this->handledError('Unknown error during commit.');
				break;
		}
	}

	
	/**
	 * Setter for dir property
	 * This should be set to the root directory of the git repository you are working with
	 */
	public function setRepository($path) {
		if (!file_exists($path) || !is_dir($path)){
            $this->handledError('The passed local git repository path is not a valid directory');
		}
		if (!file_exists("$path/.git")) {
			$this->handledError("No git repository found at '$path/.git'");
		}
		$this->repository = $path;
	}

    /** setter for comment property. Comments from between tags (addText) over-
     * ride the comment property */
    function setComment($comment) {
        $this->comment = (string) $comment;
    }
    
    /** Supporting the <gitcommit>Message</gitcommit> syntax. */
    function addText($comment) {
        $this->comment = (string) $comment;
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