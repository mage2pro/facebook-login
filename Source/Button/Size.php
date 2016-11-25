<?php
// 2016-11-25
namespace Dfe\FacebookLogin\Source\Button;
class Size extends \Df\Config\SourceT {
	/**
	 * 2016-11-25
	 * @override
	 * @see \Df\Config\Source::map()
	 * @used-by \Df\Config\Source::toOptionArray()  
	 * @see \Dfe\FacebookLogin\Settings\Button::nativeSize()
	 * @return array(string => string)
	 */
	protected function map() {return dfa_combine_self(['small', 'medium', 'large', 'xlarge']);}
}