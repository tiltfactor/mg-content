<?php
class XUploadForm extends CFormModel
{
	public $file;
	public $mime_type;
	public $size;
	public $name;
	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			array('file', 'file', 'minSize'=>1000, 'maxSize'=>10000000, 'types'=>'jpg,jpeg,png,tiff,avi,mp4,ogv,webm,mp3,ogg,wav,wmv'),
		);
	}
    //acceptFileTypes :/^(image\\/(jpg|jpeg|gif|png|tiff)|video\\/(mp4|ogv|webm)|audio\\/(mp3|ogg|wav))\$/,
	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'file'=>'Upload file(s)',
		);
	}

	public function getReadableFileSize($retstring = null) {
		// adapted from code at http://aidanlister.com/repos/v/function.size_readable.php
		$sizes = array('bytes', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');

		if ($retstring === null) { $retstring = '%01.2f %s'; }

		$lastsizestring = end($sizes);

		foreach ($sizes as $sizestring) {
			if ($this->size < 1024) { break; }
			if ($sizestring != $lastsizestring) { $this->size /= 1024; }
		}
		if ($sizestring == $sizes[0]) { $retstring = '%01d %s'; } // Bytes aren't normally fractional
		return sprintf($retstring, $this->size, $sizestring);
	}
}
