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
namespace LiteMVC\Form\Validator;

class Required implements Validator {

	/**
	 * Validate value
	 *
	 * @return string
	 */
	public function validate($value, $params)
	{
		if ($params === true) {
			if (is_null($value) || empty($value)) {
				return 'A value is required';
			}
		}
		return null;
	}

}