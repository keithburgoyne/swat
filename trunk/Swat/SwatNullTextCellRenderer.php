<?php

require_once 'Swat/SwatTextCellRenderer.php';

/**
 * A cell renderer that displays a message if it is asked to display
 * null text
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 */
class SwatNullTextCellRenderer extends SwatTextCellRenderer
{
	// {{{ public properties

	/**
	 * The text to display in this cell if the
	 * {@link SwatTextCellRenderer::$text} proeprty is null when the render()
	 * method is called
	 *
	 * @var string
	 */
	public $null_text = '&lt;none&gt;';

	/**
	 * Whether to test the {@link SwatTextCellRenderer::$text} property for
	 * null using strict equality.
	 *
	 * @var boolean
	 */
	public $strict = false;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a null text cell renderer
	 */
	public function __construct()
	{
		parent::__construct();

		$this->addStyleSheet(
			'packages/swat/styles/swat-null-text-cell-renderer.css',
			Swat::PACKAGE_ID);
	}

	// }}}
	// {{{ public function render()

	/**
	 * Renders this cell renderer
	 */
	public function render()
	{
		if (!$this->visible)
			return;

		if (($this->strict && $this->text === null) ||
			(!$this->strict && $this->text == null)) {

			$this->text = $this->null_text;

			echo '<span class="swat-null-text-cell-renderer">';
			parent::render();
			echo '</span>';

		} else {
			parent::render();
		}
	}

	// }}}
}

?>
