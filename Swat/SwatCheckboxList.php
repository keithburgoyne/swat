<?php

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatCheckAll.php';
require_once 'Swat/SwatState.php';

/**
 * A checkbox list widget
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatCheckboxList extends SwatControl implements SwatState
{
	/**
	 * Checkbox list options
	 *
	 * An array of options for the radio list in the form value => title.
	 * @var array
	 */
	public $options = null;

	/**
	 * List values 
	 *
	 * The values of the selected items.
	 * @var array
	 */
	public $values = array();

	/**
	 * On change
	 *
	 * The onchange attribute of the HTML input type=checkbox tags, or null.
	 * @var string
	 */
	public $onchange = null;

	/**
	 * Creates a new checkbox list
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->requires_id = true;

		$this->addJavaScript('swat/javascript/swat-checkbox-list.js');
	}

	public function display()
	{
		if (!$this->visible)
			return;

		$div_tag = new SwatHtmlTag('div');
		$div_tag->id = $this->id.'_div';
		$div_tag->class = 'swat-checkbox-list';
		$div_tag->open();

		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'checkbox';
		$input_tag->name = $this->id.'[]';
		if ($this->onchange !== null)
			$input_tag->onchange = $this->onchange;

		$label_tag = new SwatHtmlTag('label');
		$label_tag->class = 'swat-control';

		if ($this->options !== null) {
			echo '<ul>';

			foreach ($this->options as $value => $title) {

				echo '<li>';

				$input_tag->value = (string)$value;
				$input_tag->removeAttribute('checked');

				if (in_array($value, $this->values))
					$input_tag->checked = 'checked';

				$input_tag->id = $this->id.'_'.$input_tag->value;
				$input_tag->display();

				$label_tag->for = $this->id.'_'.$input_tag->value;
				$label_tag->content = $title;
				$label_tag->display();

				echo '</li>';
			}

			echo '</ul>';

			$this->displayJavascript();

			if (count($this->options) > 1) {
				$chk_all = new SwatCheckAll();
				$chk_all->controller = $this;
				$chk_all->display();
			}
		}

		$div_tag->close();
	}

	/**
	 * Processes this checkbox list widget
	 *
	 * @return array Array of checked values
	 */
	public function process()
	{
		if (isset($_POST[$this->id]))
			$this->values = $_POST[$this->id];
		else
			$this->values = array();
	}

	/**
	 * Reset the checkbox list.
	 *
	 * Reset the list to its default state.  This is useful to call from a 
	 * display() method when persistence is not desired.
	 */
	public function reset()
	{
		reset($this->options);
		$this->values = key($this->options);
	}

	public function setState($state)
	{
		$this->values = $state;
	}

	public function getState()
	{
		return $this->values;
	}

	/**
	 * Displays the javascript for this check-all widget
	 */
	protected function displayJavascript()
	{
		echo '<script type="text/javascript">';
		echo "//<![CDATA[\n";

		echo "var {$this->id} = new SwatCheckboxList('{$this->id}');\n";

		echo "\n//]]>";
		echo '</script>';
	}
}

?>
