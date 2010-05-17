<?php
require_once 'GitTask.php';

/**
 * Adds files in a fileset to a repository.
 *
 * @author Kristofer Widholm <help@theapotek.com>
 */
class GitAddTask extends GitTask {

	/** @string $dir path to the local Git repository */
	private $repository = '';
    private $filelists = array();
    private $fileListFiles = array();
    private $filesets = array();
    private $fileSetFiles = array();
    private $failOnError = TRUE;

	/**
	 * Main entry point.
	 */
	public function main() {

		if (empty($this->repository)) {
            throw new BuildException("You must set the 'repository' property");
		}

        $project = $this->getProject();
        $srcFiles = array();
        $srcDirs = array();

        // process filelists
        foreach($this->filelists as $fl) {
            $fromDir  = $fl->getDir($project);
            $srcFiles = $fl->getFiles($project);
            $srcDirs  = array($fl->getDir($project));
        }
        
        // process filesets
        foreach($this->filesets as $fs) {
            $ds = $fs->getDirectoryScanner($project);
            $fromDir  = $fs->getDir($project);
            $srcFiles = array_merge($srcFiles, $ds->getIncludedFiles());
            $srcDirs  = array_merge($srcDirs, $ds->getIncludedDirectories());            
        }

        //get rid of duplicates
        $srcDirs = array_unique($srcDirs);
        $srcFiles = array_unique($srcFiles);

		$current = getcwd();
		chdir($this->repository);
		$cmd = $this->git_path .' add '. implode(' ', $srcDirs) . implode(' ', $srcFiles);
		$this->log("Running `$cmd` in directory {$this->repository}");
		passthru($cmd, $return);
		chdir($current);
		if ($return==0) {
			$this->log('GitAdd: Files were added successfully');
		} else {
			$this->handledError('GitAdd: Error adding the files.');
		}
	}

    /**
     * Nested creator, adds a set of files (nested fileset attribute).
     *
     * @access  public
     * @return  object  The created filelist object
     */
    function createFileList() {
        $num = array_push($this->filelists, new FileList());
        return $this->filelists[$num-1];
    }

	/**
	* Add a new fileset.
	* @return FileSet
	*/
	public function createFileSet() {
		$this->fileset = new FileSet();
		$this->filesets[] = $this->fileset;
		return $this->fileset;
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