<?php if ( ! defined('APP_NAME')) exit('No direct script access allowed');

/**************************************************************************
 * Grace web development framework for PHP 5.1.2 or newer
 *
 * @author      陈佳(chinkei) <cj1655@163.com>
 * @copyright   Copyright (c) 2012-2013, 陈佳(chinkei)
 **************************************************************************/

/**
 * xml配置文件解析类
 * 
 * @anchor  陈佳(chinkei) <cj1655@163.com>
 * @package Config
 */
class Grace_Configure_Driver_Xml implements Grace_Configure_Interface
{
	public function __construct($path, $key = '')
	{
		$this->_path = $path;
		
		if ($key != '') {
			$this->read($key);
		}
	}
	
	public function read($key)
	{
		$filename = rtrim($this->_path, '/') . '/' . $key . '.xml';
		
        if ( !file_exists($filename) ) {
        	throw new ConfigureureException(_vf('Could not load Config file: %s', $filename));
        }
		
		$document = new DOMDocument();
		$document->load($filename);
		$root = $document->documentElement;
		
		$data =array();
		foreach ($root->childNodes as $item) {
			if ($item->hasChildNodes()) {
				$tmp = array();
				
			}
		}
		foreach ($root->childNodes as $item)   {   
		    if($item->hasChildNodes()){   
		  
				$tmp=array();   
		    foreach($item->childNodes as $one){   
		        if(!emptyempty($one->tagName)){   
		        $tmp[$one->tagName]=$one->nodeValue;   
		        }   
		    }   
		    $arr[$item->tagName]=$tmp;   
		    }   
		}   
		print_r($arr); 
	}
	
	public function parsexml($menus){
         $result = array();
         foreach($menus as $menu){
             $tmparr = array();

             //    处理空文本节点方式a
             if( $menu->nodename !='#text'){

                 //    检索子元素时跳跃过文本节点  - 处理空文本节点方式b
                 for($i=1; $i<$menu->childnodes->length; $i+=2) {
                     $anode = $menu->childnodes->item($i);

                     //    子元素遍历
                     $anode->childnodes->length > 1 ? $tmparr[$anode->nodename] = $this->parsexml( $anode->childnodes)
                     : $tmparr[$anode->nodename] = $anode->nodevalue;
                 }
                 array_push($result,$tmparr);
             }
         }
        return $result;
     }
}
?>