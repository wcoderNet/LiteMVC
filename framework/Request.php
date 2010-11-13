<?php
/**
 * LiteMVC Application Framework
 *
 * @author Phil Bayfield
 * @copyright 2010
 * @license Creative Commons Attribution-Share Alike 2.0 UK: England & Wales License
 * @package LiteMVC
 * @version 0.1.0
 */
namespace LiteMVC;

// Namespace aliases
use LiteMVC\App as App;
use LiteMVC\Request as Request;

class Request
{

	/**
	 * Config data
	 * 
	 * @var array
	 */
	protected $_config = array();

	/**
	 * Application path
	 * 
	 * @var string
	 */
	protected $_appPath;

	/**
	 * Relative path to docroot
	 *
	 * @var string
	 */
	protected $_relativePath;

	/**
	 * URI relative to framework root
	 *
	 * @var string
	 */
	protected $_uri;

	/**
	 * Module requested
	 *
	 * @var string
	 */
	protected $_module;

	/**
	 * Controller requested
	 *
	 * @var string
	 */
	protected $_controller;

	/**
	 * Action requested
	 *
	 * @var string
	 */
	protected $_action;

	/**
	 * Params
	 *
	 * @var array
	 */
	protected $_params = array();

	/**
	 * Constructor
	 *
	 * @param App $app
	 * @return void
	 */
	public function __construct(App $app)
	{
		// Check config
		$config = $app->getResource('Config')->request;
		if (!is_null($config)) {
			$this->_config = $config;
		}
		// Set app path
		$this->_appPath = $app::PATH_APP;
		// Determine script path relative to doc root
		// Allows for flexible deployment
		// e.g. framework root could reside at example.com/a/b/c/d/
		$this->_relativePath = substr($_SERVER['SCRIPT_NAME'], 0 , strrpos($_SERVER['SCRIPT_NAME'], '/'));
		// Determine URI relative to framework root
		$this->_uri = str_replace($this->_relativePath, '', $_SERVER['REQUEST_URI']);
	}

	/**
	 * Process request
	 *
	 * @return void
	 */
	public function process()
	{
		// Split up URI
		$uri = trim($this->_uri, '/');
		// Ignore query strings
		if (strpos($uri, '?') !== false) {
			$uri = substr($uri, 0, strpos($uri, '?'));
		}
		// Ignore bookmarks
		if (strpos($uri, '#') !== false) {
			$uri = substr($uri, 0, strpos($uri, '#'));
		}
		$parts = explode('/', $uri);
		// Array index to check
		$index = 0;
		// Determine module
		if (isset($parts[$index]) && !empty($parts[$index]) && file_exists(\PATH . $this->_appPath . $parts[0])) {
			$this->_module = $parts[$index];
			unset($parts[$index]);
			$index ++;
		} elseif (isset($this->_config['default']['module'])) {
			$this->_module = $this->_config['default']['module'];
		} else {
			throw new App\Exception('Unable to determine which module to load, no default module specified in config.');
		}
		// Determine controller
		if (isset($parts[$index]) && !empty($parts[$index])) {
			$this->_controller = $parts[$index];
			unset($parts[$index]);
			$index ++;
		} elseif (isset($this->_config[$this->_module]['default']['controller'])) {
			$this->_controller = $this->_config[$this->_module]['default']['controller'];
		} else {
			throw new App\Exception('Unable to determine controller, no default specified in config for module ' . $this->_module . '.');
		}
		// Determine action
		if (isset($parts[$index]) && !empty($parts[$index])) {
			$this->_action = $parts[$index];
			unset($parts[$index]);
			$index ++;
		} elseif (isset($this->_config[$this->_module]['default']['action'])) {
			$this->_action = $this->_config[$this->_module]['default']['action'];
		} else {
			throw new App\Exception('Unable to determine action, no default specified in config for module ' . $this->_module . '.');
		}
		// Get any params
		if (count($parts)) {
			$key = null;
			foreach ($parts as $value) {
				if (is_null($key)) {
					$key = $value;
				} else {
					$this->_params[$key] = $value;
					$key = null;
				}
			}
		}
	}

	/**
	 * Get relative path of web root
	 *
	 * @return string
	 */
	public function getRelativePath()
	{
		return $this->_relativePath;
	}

	/**
	 * Get module name
	 *
	 * @return string
	 */
	public function getModule()
	{
		return $this->_module;
	}

	/**
	 * Get controller name
	 *
	 * @return string
	 */
	public function getController()
	{
		return $this->_controller;
	}

	/**
	 * Get action name
	 *
	 * @return string
	 */
	public function getAction()
	{
		return $this->_action;
	}

	/**
	 * Get layout
	 *
	 * @param string $controller
	 * @return string
	 */
	public function getLayout($controller)
	{
		if (isset($this->_config[$this->_module][$controller]['layout'])) {
			return ucfirst($this->_config[$this->_module][$controller]['layout']);
		} elseif (isset($this->_config[$this->_module]['default']['layout'])) {
			return ucfirst($this->_config[$this->_module]['default']['layout']);
		}
		return null;
	}

	/**
	 * Return a section of the config if it exists
	 *
	 * @param string $section
	 * @return array
	 */
	public function getConfig($section)
	{
		if (isset($this->_config[$this->_module][$section])) {
			return $this->_config[$this->_module][$section];
		}
		return null;
	}

	/**
	 * Check if request was a POST
	 *
	 * @return bool
	 */
	public function isPost()
	{
		return $_SERVER['REQUEST_METHOD'] == 'POST';
	}

	/**
	 * Check if request was AJAX
	 *
	 * @return bool
	 */
	public function isAjax()
	{
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
	}

	/**
	 * Get a param from URI
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function getParam($key)
	{
		if (isset($this->_params[$key])) {
			// Magic quotes should be off, but just in case
			if (get_magic_quotes_gpc()) {
				return stripslashes($this->_params[$key]);
			}
			return $this->_params[$key];
		}
		return null;
	}

	/**
	 * Get a post param
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function getPostParam($key)
	{
		if (isset($_POST[$key])) {
			return $this->filterValue($_POST[$key]);
		}
		return null;
	}

	/**
	 * Get all post params
	 *
	 * @return array
	 */
	public function getAllPostParams()
	{
		return $this->filterValue($_POST);
	}

	/**
	 * Filter a value or array of values
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public function filterValue($value) {
		if (is_array($value)) {
			foreach ($value as $arrKey => $arrValue) {
				$value[$arrKey] = $this->filterValue($arrValue);
			}
		} elseif (get_magic_quotes_gpc()) {
			return stripslashes($value);
		}
		return $value;
	}

	/**
	 * Redirect request
	 *
	 * @param string $action
	 * @param string $controller
	 * @param string $module
	 * @return void
	 */
	public function redirect($action = null, $controller = null, $module = null, $params = null, $return = false)
	{
		// Replace null values with current values
		if (is_null($module)) {
			$module = $this->_module;
		}
		if (is_null($controller)) {
			$controller = $this->_controller;
		}
		if (is_null($action)) {
			$action  = $this->_action;
		}
		// Redirect to new uri
		$uri = $this->_relativePath . '/';
		if (isset($this->_config['default']['module']) && $module != $this->_config['default']['module']) {
			$uri .= $module . '/';
		}
		$uri .= $controller;
		if (!is_null($params) || (isset($this->_config['default']['action']) && $action != $this->_config['default']['module'])) {
			$uri .= '/' . $action;
		}
		if (!is_null($params)) {
			foreach ($params as $key => $value) {
				$uri .= '/' . $key . '/' . $value;
			}
		}
		if ($return) {
			return $uri;
		}
		header('Location: ' . $uri);
		exit;
	}

}