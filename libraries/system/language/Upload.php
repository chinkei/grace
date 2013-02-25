<?php
class Upload
{
	public $allowContentTypes;
	
	public $allowExtensions;
	
	private $_err = 0;
	
	private static $_instance;
	
	public function __construct()
	{
		// 载入语言文件
		$this->loadLanguageFile();
	}
	
	public static function getInstance()
	{
		if (NULL === self::$_instance) {
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}
	
	public function setAllowSize($size)
	{
		ini_set('upload_max_filesize', $size);
		ini_set('post_max_size', $size);
	}
	
	public function save($field, $path, $newName = NULL, $autoRename = TRUE)
	{
		if (!isset($_FILES[$field])) {
			$this->_err = 23;
			return FALSE;
		}
		$file = $_FILES[$field];
		
		if (UPLOAD_ERR_OK = $file['error']) {
			if (0 === $file['error'] && $file['size'] > 0) {
				$ext = strtolower(substr(strrchr($file['name'], '.'), 1));
				if (isset($this->allowExtensions) && !in_array($ext, $this->allowExtensions)) {
					$this->_err = 21;
					return FALSE;
				}
			}
			
			if (isset($this->allowContentTypes)) {
				
			}
			
			$name = $file['name'];
			
			if (NULL !== $newName) {
				$name = $newName . '.' . $ext;
			}
			
			$fileName = $path . $name;
			
			if ($autoRename) {
				$finfo = pathinfo($fileName);
				$i = 1;
				while(file_exists($fileName)) {
					$fileName = $path . $finfo['filename'] . '(' .$i . ')' . $ext;
					++$i;
				}
			}
			
			move_uploaded_file($file['tmp_name'], $filename);
			$this->_err = 0;
			return $filename;
		}
		
		$this->_err = $file['error'];
		return FALSE;
	}
}
?>