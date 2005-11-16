<?php

require_once 'Swat/SwatInputControl.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatState.php';
require_once 'Swat/SwatFormField.php';

/**
 * A file upload widget
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatFileEntry extends SwatInputControl
{
	/**
	 * The size in characters of the XHTML form input, or null if no width is
	 * specified
	 *
	 * @var integer
	 */
	public $size = 40;

	/**
	 * Array of mime-types to accept as uploads
	 *
	 * @var array
	 */
	public $accept_mime_types = null;

	/**
	 * Display acceptable mime-types as a note in this entry's parent
	 *
	 * @var boolean
	 */
	public $display_mime_types = true;

	/**
	 * Stores the relevant part of the $_FILES array for this widget after
	 * the widget's parent is processed
	 *
	 * @var array
	 */
	private $file = null;

	/**
	 * Displays this entry widget
	 *
	 * Outputs an appropriate XHTML tag.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'file';
		$input_tag->name = $this->id;
		$input_tag->id = $this->id;

		if ($this->size !== null)
			$input_tag->size = $this->size;

		if ($this->accept_mime_types !== null)
			$input_tag->accept = implode(',', $this->accept_mime_types);
			//note: the 'accept' attribute is part of the w3c
			//standard, but ignored by most browsers

		$input_tag->display();

		if ($this->accept_mime_types !== null && $this->display_mime_types
			&& $this->parent instanceof SwatFormField) {

			$note = Swat::ngettext("File type must be '%s'",
				"Valid file types are: %s",
				count($this->accept_mime_types));

			$this->parent->note = sprintf($note, implode(', ', $this->accept_mime_types));
		}
	}

	/**
	 * Processes this file entry widget
	 *
	 * If any validation type errors occur, an error message is attached to
	 * this entry widget.
	 */
	public function process()
	{
		$this->file = SwatApplication::initVar($this->id, null, SwatApplication::VAR_FILES);

		if ($this->file['name'] == null)
			$this->file = null;
			// note: an array is returned even if
			//       no file is uploaded, so check the filename

		if (!$this->required && $this->file === null) {
			return;

		} elseif ($this->file === null) {
			$msg = Swat::_('The %s field is required.');
			$this->addMessage(new SwatMessage($msg, SwatMessage::ERROR));

		} elseif (!in_array($this->getMimeType(), $this->accept_mime_types)) {
			$msg = sprintf(Swat::_('The %s field must be of the following type(s): %s.'),
				'%s',
				implode(', ', $this->accept_mime_types));

			$this->addMessage(new SwatMessage($msg, SwatMessage::ERROR));
		}
	}


	/**
	 * Is file uploaded
	 *
	 * @return boolean whether or not a file was uploaded with this file entry.
	 */
	public function isUploaded()
	{
		return ($this->file !== null);
	}

	/**
	 * Gets the original file name of the uploaded file
	 *
	 * @return mixed the original filename of the uploaded file or null if no
	 *                file was uploaded.
	 *
	 * @see SwatFileEntry::getTempFileName()
	 */
	public function getFileName()
	{
		return ($this->isUploaded()) ? $this->file['name'] : null;
	}

	/**
	 * Gets the temporary name of the uploaded file
	 *
	 * @return mixed the temporary name of the uploaded file or null if no
	 *                file was uploaded.
	 *
	 * @see SwatFileEntry::getFileName()
	 */
	public function getTempFileName()
	{
		return ($this->isUploaded()) ? $this->file['tmp_name'] : null;
	}

	/**
	 * Gets the size of the uploaded file in bytes
	 *
	 * @return mixed the size of the uploaded file in bytes or null if no file
	 *                was uploaded.
	 */
	public function getSize()
	{
		return ($this->isUploaded()) ? $this->file['size'] : null;
	}

	/**
	 * Gets the mime type of the uploaded file
	 *
	 * @return mixed the mime type of the uploaded file or null if no file was
	 *                uploaded.
	 */
	public function getMimeType()
	{
		return ($this->isUploaded()) ? $this->file['type'] : null;
	}

	/**
	 * Saves the uploaded file to the server
	 *
	 * @param string $dst_dir the directory on the server to save the uploaded
	 *                        file in.
	 * @param string $dst_filename an optional filename to save the file under.
	 *                             If no filename is specified, the file is
	 *                             saved with the original filename.
	 *
	 * @return boolean true if the file was saved correctly and false if there
	 *                  was an error or no file was uploaded.
	 *
	 * @throws SwatEexception if the destination directory does not exist.
	 */
	public function saveFile($dst_dir, $dst_filename = null)
	{
		if (!$this->isUploaded())
			return false;

		if ($dst_filename === null)
			$dst_filename = $this->getFileName();

		if (is_dir($dst_dir))
			return move_uploaded_file($this->file['tmp_name'],
				$dst_dir.'/'.$dst_filename);
		else
			throw new SwatException("Destination of '{$dst_dir}' is not a ".
				'directory or does not exist.');
	}
}

?>
