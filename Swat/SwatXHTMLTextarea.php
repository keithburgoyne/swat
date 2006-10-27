<?php

require_once 'Swat/SwatTextarea.php';

/**
 * A text area that validates its content as an XHTML fragment against the
 * XHTML Strict DTD
 *
 * @package   Swat
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatXHTMLTextarea extends SwatTextarea
{
	// {{{ protected properties

	/**
	 * An array of document validation errors used by the custom error handler
	 * for loading XML documents
	 *
	 * @var array
	 */
	protected static $validation_errors = array();

	// }}}
	// {{{ public function process

	public function process()
	{
		static $xhtml_template = '';

		if (strlen($xhtml_template) == 0) {
			$xhtml_template = <<<XHTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<title>SwatXHTMLTextarea Content</title>
	</head>
	<body>
		<div>
		%s
		</div>
	</body>
</html>

XHTML;
		}

		parent::process();

		$xhtml_content = sprintf($xhtml_template, $this->value);

		$html_errors_value = self::initializeErrorHandler();

		$document = DOMDocument::loadXML($xhtml_content);

		if (count(self::$validation_errors) > 0 || !$document->validate())
			$this->addMessage($this->getValidationErrorMessage());

		self::restoreErrorHandler($html_errors_value);
	}

	// }}}
	// {{{ protected function getValidationErrorMessage()

	/**
	 * Gets a human readable error message for XHTML validation errors on
	 * this textarea's value
	 *
	 * @return SwatMessage a human readable error message for XHTML validation
	 *                      errors on this textarea's value.
	 */
	protected function getValidationErrorMessage()
	{
		$ignored_errors = array(
			'extra content at the end of the document',
			'premature end of data in tag html',
			'opening and ending tag mismatch between html and body',
			'opening and ending tag mismatch between body and html',
		);

		$errors = array();
		$load_xml_error = '/^DOMDocument::loadXML\(\): (.*)?$/u';
		$validate_error = '/^DOMDocument::validate\(\): (.*)?$/u';
		foreach (self::$validation_errors as $error) {
			$matches = array();

			// parse errors into human form
			if (preg_match($validate_error, $error, $matches) === 1) {
				$error = $matches[1];
			} elseif (preg_match($load_xml_error, $error, $matches) === 1) {
				$error = $matches[1];
			}

			// further humanize
			$error = str_replace('tag mismatch:', 'tag mismatch between',
				$error);

			// remove some stuff that only makes sense in document context
			$error = preg_replace('/\s?line:? [0-9]+\s?/ui', ' ', $error);
			$error = preg_replace('/in entity[:,.]?/ui', '', $error);
			$error = strtolower($error);
			$error = trim($error);

			if (!in_array($error, $ignored_errors))
				$errors[] = $error;
		}

		$content = '%s must be valid XHTML markup: ';
		$content.= '<ul><li>'.implode(',</li><li>', $errors).'.</li></ul>';
		$message = new SwatMessage($content, SwatMessage::ERROR);
		$message->content_type = 'text/xml';

		return $message;
	}

	// }}}
	// {{{ public static function handleValidationErrors()

	/**
	 * Handles errors generated by loading and validation of malformed or
	 * non-conformant XML documents
	 *
	 * @param integer $errno the level of the error raised.
	 * @param string $errstring the error message.
	 */
	public static function handleValidationErrors($errno, $errstr)
	{
		$error = $errstr;
		self::$validation_errors[] = $error;
	}

	// }}}
	// {{{ private static function initializeErrorHandler()

	/**
	 * Initializes the custom error handler for loading and validating XML
	 * files
	 *
	 * Make sure to call {@link SwatXHTMLTextarea::restoreErrorHandler()} some
	 * time after calling this function.
	 *
	 * @return boolean the old value of the ini value for html_errors.
	 */
	private static function initializeErrorHandler()
	{
		self::$validation_errors = array();
		$html_errors = ini_set('html_errors', false);
		set_error_handler(array(__CLASS__, 'handleValidationErrors'),
			E_NOTICE | E_WARNING);

		return $html_errors;
	}

	// }}}
	// {{{ private static function restoreErrorHandler()

	/**
	 * Restores the custom error handler used for loading and validating XML
	 * files
	 *
	 * @param boolean $html_errors whether to turn on or off html_errors when
	 *                              restoring regular error handling.
	 *
	 * @see SwatXHTMLTextarea::initializeErrorHandler()
	 */
	private static function restoreErrorHandler($html_errors)
	{
		ini_set('html_errors', $html_errors);
		restore_error_handler();
	}

	// }}}
}

?>
