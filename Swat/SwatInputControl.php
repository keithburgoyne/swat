<?php

require_once 'Swat/SwatControl.php';

/**
 * Base class for controls that accept user input on forms.
 *
 * @package   Swat
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatInputControl extends SwatControl
{
	/**
	 * Whether this entry widget is required or not
	 *
	 * Must have a non-empty value when processed.
	 *
	 * @var boolean
	 */
	public $required = false;

	/**
	 * Initializes this widget
	 *
	 * Sets required property on the form field that contains this widget.
	 *
	 * @see SwatWidget::init()
	 */
	public function init()
	{
		parent::init();

		if ($this->required && $this->parent instanceof SwatFormField)
			$this->parent->required = true;
	}

	/**
	 * Gets the form that this control is contained in
	 *
	 * You can also get the parent form with the
	 * {@link SwatWidget::getFirstAncestor() method but this method is more
	 * convenient and throws an exception .
	 *
	 * @return SwatForm the form this control is in.
	 *
	 * @throws SwatException
	 */
	public function getForm()
	{
		$form = $this->getFirstAncestor('SwatForm');
		if ($form === null)
			throw SwatException('Input controls must reside inside a SwatForm '.
				'widget.');

		return $form;
	}
}

?>
