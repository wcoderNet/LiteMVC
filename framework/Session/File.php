<?php
/**
 * LiteMVC Application Framework
 *
 * @author Phil Bayfield
 * @copyright 2010 - 2012
 * @license GNU General Public License version 3
 * @package LiteMVC
 * @version 0.2.0
 */
namespace LiteMVC\Session;

class File implements Session
{

	/**
	 * File caching object
	 *
	 * @var LiteMVC\Cache\File
	 */
	protected $_file;


	/**
	 * Key prefix
	 *
	 * @var string
	 */
	protected $_prefix;

	/**
	 * Configuration keys
	 *
	 * @var string
	 */
	const CONFIG_PREFIX = 'prefix';

	/**
	 * Constructor
	 *
	 * @param LiteMVC\Cache\File $file
	 * @param LiteMVC\App\Config $config
	 * @return void
	 */
	public function __construct($file, $config)
	{
		$this->_file = $file;
		if (isset($config[self::CONFIG_PREFIX])) {
			$this->_prefix = $config[self::CONFIG_PREFIX];
		}
	}

	/**
	 * Open session LiteMVC\Cache\File
	 *
	 * @param string $path
	 * @param string $name
	 * @return void
	 */
	public function open($path, $name) {}

	/**
	 * Close session
	 *
	 * @return void
	 */
	public function close() {}

	/**
	 * Read session data
	 *
	 * @param string $id
	 * @return string
	 */
	public function read($id)
	{
		return $this->_file->get($this->_prefix . $id);
	}

	/**
	 * Write session data
	 *
	 * @param string $id
	 * @param string $data
	 * @return void
	 */
	public function write($id, $data, $expiry)
	{
		$this->_file->set($this->_prefix . $id, $data, 0, $expiry);
	}

	/**
	 * Destroy session
	 *
	 * @param string $id
	 * @return void
	 */
	public function destroy($id)
	{
		$this->_file->delete($this->_prefix . $id);
	}

	/**
	 * Garbage collection
	 *
	 * @return void
	 */
	public function gc()
	{
		$this->_file->clean($this->_prefix);
	}

}