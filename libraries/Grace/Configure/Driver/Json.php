<?php
class Grace_Configure_Driver_Json implements Grace_Configure_Interface
{

	/**
     * The path to read ini files from.
     *
     * @var array
     */
    protected $_path;
	
	public function setPath($path)
	{
		$this->_path = rtrim($path, '/') . '/';
	}

    /**
     * Read an ini file and return the results as an array.
     *
     * @param string $file Name of the file to read. The chosen file
     *    must be on the reader's path.
     * @return array
     * @throws ConfigureureException
     */
    public function read($key)
    {
        $fileName = $this->_path . $key . '.json';

		if ( !file_exists($fileName) ) {
        	throw new Grace_Configure_Exception(_vf('Could not load Config file: %s', $fileName));
        }

        $json = file_get_contents($file);
        return json_decode($json);
    }
}
?>