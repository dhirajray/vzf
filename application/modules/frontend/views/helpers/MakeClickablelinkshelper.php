<?php
class Zend_View_Helper_MakeClickablelinkshelper{
	
	protected $request;
	
	/* public function __construct($request)
	{
		$this->request = $request;
	} */
	public function MakeClickablelinkshelper($text)
	{
		$text = eregi_replace('(((f|ht){1}tp://)[-a-zA-Z0-9@:%_\+.~#?&//=]+)','<a href="\\1" target="_blank">\\1</a>', $text);
		$text = eregi_replace('([[:space:]()[{}])(www.[-a-zA-Z0-9@:%_\+.~#?&//=]+)','\\1<a href="http://\\2" target="_blank">\\2</a>', $text);
		$text = eregi_replace('([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3})','<a href="mailto:\\1" target="_blank">\\1</a>', $text);	
		return $text;
	
	}
}	
	?>