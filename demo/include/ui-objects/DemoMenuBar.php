<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

/**
 * The menu for the Swat Demo Application
 *
 * This is a simple menu that takes a flat array of titles and links and
 * displays them in an unordered list.
 *
 * @package   SwatDemo
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class DemoMenuBar extends SwatControl
{
	// {{{ protected properties

	protected $entries = array();
	protected $selected_entry;

	// }}}
	// {{{ public function display()

	public function display()
	{
		$div_tag = new SwatHtmlTag('div');
		$div_tag->id = $this->id;
		$div_tag->class = 'demo-menu-bar';
		$div_tag->open();

		$a_tag = new SwatHtmlTag('a');
		$span_tag = new SwatHtmlTag('span');
		$span_tag->class = 'demo-menu-bar-selected';

		echo '<h3>', Swat::_('Demos:'), '</h3><ul>';

		foreach ($this->entries as $demo => $title) {
			echo '<li>';
			if ($this->selected_entry == $demo) {
				$span_tag->setContent($title);
				$span_tag->display();
			} else {
				$a_tag->href = 'index.php?demo='.$demo;
				$a_tag->setContent($title);
				$a_tag->display();
			}
			echo '</li>';
		}


		echo '</ul>';
		$div_tag->close();
	}

	// }}}
	// {{{ public function setEntries()

	public function setEntries(array $entries)
	{
		$this->entries = $entries;
	}

	// }}}
	// {{{ public function setSelectedEntry()

	public function setSelectedEntry($entry)
	{
		$this->selected_entry = $entry;
	}

	// }}}
}

?>
