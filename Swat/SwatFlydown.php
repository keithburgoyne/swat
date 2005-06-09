<?php

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatState.php';

/**
 * A flydown (aka combo-box) selection widget
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatFlydown extends SwatControl implements SwatState
{
	/**
	 * Flydown options
	 *
	 * An array of options for the flydown in the form:
	 *    value => title
	 *
	 * @var array
	 */
	public $options = null;

	/**
	 * Flydown value
	 *
	 * The index value of the selected option, or null if no option is
	 * selected.
	 *
	 * @var string
	 */
	public $value = null;
	
	/**
	 * Required
	 *
	 * Whether or not a value is required to be selected.
	 *
	 * @var bool
	 */
	public $required = false;

	/**
	 * Show a blank option
	 *
	 * Whether or not to show a blank value at the top of the flydown.
	 *
	 * @var boolean
	 */
	public $show_blank = true;

	/**
	 * Blank title
	 *
	 * The user visible title to display in th eblank field.
	 *
	 * @var string
	 */
	public $blank_title = '';

	/**
	 * On change
	 *
	 * The onchange attribute of the XHTML select tag, or null.
	 *
	 * @var string
	 */
	public $onchange = null;

	/**
	 * Displays this flydown
	 *
	 * Displays this flydown as a XHTML select.
	 */
	public function display()
	{
		$options = $this->getOptions();

		$select_tag = new SwatHtmlTag('select');
		$select_tag->name = $this->id;
		$select_tag->id = $this->id;

		if ($this->onchange !== null)
			$select_tag->onchange = $this->onchange;

		$option_tag = new SwatHtmlTag('option');

		$select_tag->open();

		if ($options !== null) {
			if ($this->show_blank) {
				// Empty string HTML option value is considered to be null
				$option_tag->value = '';
				$option_tag->open();
				echo $this->blank_title;
				$option_tag->close();
			}
			
			foreach ($options as $value => $title) {
				$option_tag->value = (string)$value;
				$option_tag->removeAttr('selected');
				
				if ((string)$this->value === (string)$value)
					$option_tag->selected = 'selected';

				$option_tag->open();
				echo $title;
				$option_tag->close();
			}
		}

		$select_tag->close();
	}	

	/**
	 * Figures out what option was selected
	 *
	 * Processes this widget and figures out what select element from this
	 * flydown was selected. Any validation errors cause an error message to
	 * be attached to this widget in thie method.
	 */
	public function process()
	{
		$value = $_POST[$this->id];

		// Empty string HTML option value is considered to be null
		if (strlen($value) == 0)
			$this->value = null;
		else
			$this->value = $value;
		
		if ($this->required && $this->value === null) {
			$msg = _S("The %s field is required.");
			$this->addMessage(new SwatMessage($msg, SwatMessage::USER_ERROR));
		}
	}

	/**
	 * Resets this flydown
	 *
	 * Resets this flydown to its default state. This methods is useful to
	 * call from a display() method when form persistence is not desired.
	 */
	public function reset()
	{
		reset($this->options);
		$this->value = null;	
	}
	
	/**
	 * Gets the current state of this flydown
	 *
	 * @return boolean the current state of this flydown.
	 *
	 * @see SwatState::getState()
	 */
	public function getState()
	{
		return $this->value;
	}
	
	/**
	 * Sets the current state of this flydown
	 *
	 * @param boolean $state the new state of this flydown.
	 *
	 * @see SwatState::setState()
	 */
	public function setState($state)
	{
		$this->value = $state;
	}

	/**
	 * Gets a reference to the array of options to show in this flydown
	 *
	 * Subclasses may want to override this method.
	 *
	 * @return array a reference to the array of options to show in this
	 *                flydown.
	 */
	protected function &getOptions()
	{
		return $this->options;
	}
}

?>
