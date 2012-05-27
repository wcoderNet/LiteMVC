<?php
/**
 * LiteMVC Application Framework
 *
 * @author Phil Bayfield
 * @copyright 2012
 * @license Creative Commons Attribution-Share Alike 2.0 UK: England & Wales License
 * @package LiteMVC\REST
 * @version 0.1.0
 */
namespace LiteMVC\REST;

interface Parser
{
	
	/**
	 * Parse data and return it
	 * 
	 * @param string $data
	 * @return string
	 */
	public function parse($data);
	
}