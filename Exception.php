<?php
namespace Dfe\Facebook;
class Exception extends \Df\Core\Exception {
	/**
	 * 2015-10-10
	 * @override
	 * @see \Df\Core\Exception::getMessageRm()
	 * @return string
	 */
	public function getMessageRm() {
		return "Facebook API error of type {$this->fbType()}: «{$this->fbMessage()}».";
	}

	/** @return string */
	private function fbMessage() {return $this['message'];}
	/** @return string */
	private function fbType() {return $this['type'];}
}