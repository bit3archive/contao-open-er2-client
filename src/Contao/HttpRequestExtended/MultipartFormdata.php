<?php

/**
 * PHP version 5
 * @copyright	Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @copyright   InfinitySoft 2011 <http://www.infinitysoft.de>
 * @package		RequestExtended
 * @license		LGPL 
 * @filesource
 */

namespace Contao\HttpRequestExtended;

if(!defined('CRLF'))
	define('CRLF', "\r\n");

/**
 * Class MultipartFormdata
 *
 * Provide methods to encode MultipartFormdata content for HTTP POST requests.
 * @copyright  Christian Schiffler 2009
 * @copyright  InfinitySoft 2011 <http://www.infinitysoft.de>
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Library
 */
class MultipartFormdata
{
	
	/**
	 * The boundary to use for form data.
	 */
	protected $boundary;

	/**
	 * The boundary to use for form data.
	 */
	protected $fields=array();

	/**
	 * Set default values
	 */
	public function __construct()
	{
		$this->boundary = uniqid();
	}
	
	public function setField($name, $value)
	{
		$this->fields[$name]=array('value' => $value);
	}
	
	public function setFileField($name, $filename, $contentType='', $encoding='binary')
	{
		if(!file_exists($filename))
			return false;
		$this->fields[$name]=array('value' => 'file', 'filename' => $filename, 'contentType' => $contentType, 'encoding' => $encoding);
		return true;
	}
	
	public function getContentTypeHeader($nested=false)
	{
		return 'multipart/' . ($nested ? 'mixed' : 'form-data'). ', boundary=' . $this->boundary;
	}

	public function compile($nested=false)
	{
		$boundaryline = '--' . (!$nested? $this->boundary : $nested);
		if(!$nested)
			$ret = $boundaryline . CRLF;
		$first=true;
		foreach($this->fields as $name=>$data)
		{
			if(!$first)
				$ret .= $boundaryline . CRLF;
			else
				$first=false;
			$df='';
			// nested MultipartFormdata?
			if($data['value'] instanceof MultipartFormdata)
			{
				// TODO: is there a better approach to merge sub data in? I guess this way we can get collisions but the commented approach did not work out at all.
				//$df .= 'Content-Disposition: attachment; name="' . $name . '"' . CRLF;
				//$ret .= 'Content-type: ' . $data['value']->getContentTypeHeader($this->boundary) . CRLF
				//$df .= CRLF;
				$df .= $data['value']->compile($this->boundary);
			} 
			else if (isset($data['filename']))
			{
				// TODO: is there a better approach? 
				// like storing everything to a tempfile before? Might run out of RAM this way.
				// add the file now.
				if(!$nested)
					$df .= 'Content-Disposition: form-data; name="' . $name . '"; filename="' . basename($data['filename']) . '"' . CRLF;
				else
					$df .= 'Content-Disposition: ' . $data['contentType'] . '; filename="' . basename($data['filename']) . '"' . CRLF;
				if(strlen($data['contentType']))
					$df .= 'Content-type: ' . $data['contentType'] . CRLF;
				// TODO: Handle encoding automatically here.
				$df .= 'Content-Transfer-Encoding: ' . $data['encoding'] . CRLF;
				$df .= CRLF;
				// add file content.
				$df .= file_get_contents($data['filename']) . CRLF;
				//$df .= 'file: ' . basename($data['filename']) . CRLF;
			}
			else
			{
				$df .= 'Content-Disposition: form-data; name="' . $name . '"' . CRLF . CRLF;
				$df .= $data['value']  . CRLF;
			}
			if($df)
				$ret .= $df;
		}
		if(!$nested)
			$ret .= $boundaryline . '--';
		$ret .= CRLF;
		return $ret;
	}
}

?>