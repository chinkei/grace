<?php if ( ! defined('APP_NAME')) exit('No direct script access allowed');

/**************************************************************************
 * Grace web development framework for PHP 5.1.2 or newer
 *
 * @author      陈佳(chinkei) <cj1655@163.com>
 * @copyright   Copyright (c) 2012-2013, 陈佳(chinkei)
 **************************************************************************/

/**
 * Xml数据处理类
 * 
 * @anchor 陈佳(chinkei) <cj1655@163.com>
 * @package Library
 */
class Grace_Library_Xml
{
	/** 
     * xml字符串
     * 
     * @var string
     */ 
    protected $_xmlText; 
    
	/**
	 * 数组转换为xml数据
	 *
	 * @param  array  $data     数组
	 * @param  string $encoding 编码类型
	 * @param  string $root     根节点名
	 * @return string
	 */
    public function arrayToXml($data, $encoding = 'utf-8', $root = 'root')
	{ 
        //star and end the XML document 
        $this->_xmlText = "<?xml version=\"1.0\" encoding=\"$encoding\"?>\n<$root>\n"; 
        $this->_arrayTransform($data);
        $this->_xmlText .="</$root>";
        return $this->_xmlText;
    }
	
	/**
	 * xml数据转换为数组
	 *
	 * @param  string $xml          xml数据
	 * @param  array  $arrSkipIndex 要忽略的节点名数组
	 * @return array
	 */
	public function xmlToArray($xml, $arrSkipIndex = array())
	{
		$this->_xmlText = file_get_contents($xml);
		$xmlObj = simplexml_load_string($this->_xmlText);
		return $this->_xmlToArray($xmlObj, $arrSkipIndex);
	}
	
	/**
	 * 设置子节点xml数据
	 * 
	 * @param array $data
	 */
    private function _arrayTransform($data)
	{
		foreach ($data as $key => $value) {
			if ( !is_array($value) ) {
				$this->_xmlText .= "<$key>$value</$key>";
			} else {
				$this->_xmlText .= "<$key>";
				$this->_arrayTransform($value);
				$this->_xmlText .= "</$key>";
			}
		}
    }
	
	/**
	 * XML数据转换为数组
	 * 
	 * @param  object $arrObjData
	 * @param  array  $arrSkipIndex 要忽略的节点名数组
	 * @return array
	 */
	private function _xmlToArray($arrObjData, &$arrSkipIndex)
	{
		$arrData = array();
    
	    // 如果是对象则设置对象为对象属性组成的关联数组
	    if (is_object($arrObjData)) {
	        $arrObjData = get_object_vars($arrObjData);
	    }
	    
	    if (is_array($arrObjData)) {
	        foreach ($arrObjData as $index => $value) {
	            if (is_object($value) || is_array($value)) {
					// 递归调用
	                $value = $this->_xmlToArray($value, $arrSkipIndex);
	            }
				
	            if (in_array($index, $arrSkipIndex)) {
	                continue;
	            }
	            $arrData[$index] = $value;
	        }
	    }
	    return $arrData;
	}
}
?>