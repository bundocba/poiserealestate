<?php
/**
 * @version 3.3.1 2014-06-06
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2009 - 2014 the Thinkery LLC. All rights reserved.
 * @license GNU/GPL see LICENSE.php
 */

defined('JPATH_BASE') or die;

class JFormFieldHeader extends JFormField
{
	protected $type = 'Header';

	protected function getInput()
    {
		return;
	}

	protected function getLabel()
	{        
        $tag = ($this->element['tag']) ? $this->element['tag'] : '';
        
        $html = array();
		$class = !empty($tag) ? ' class="alert alert-'.$tag.'"' : '';
		$html[] = '<span class="spacer">';
		$html[] = '<span class="before"></span>';
		$html[] = '<div'.$class.'>';

			$label = '';

			// Get the label text from the XML element, defaulting to the element name.
			$text = $this->element['default'] ? (string) $this->element['default'] : (string) $this->element['label'];
			$text = JText::_($text);

			// Build the class for the label.
			$class = !empty($this->description) ? 'hasTooltip' : '';
			$class = $this->required == true ? $class . ' required' : $class;

			// Add the opening label tag and main attributes attributes.
			$label .= '<h3 id="' . $this->id . '-lbl" class="' . $class . '"';

			// If a description is specified, use it to build a tooltip.
			if (!empty($this->description))
			{
				JHtml::_('bootstrap.tooltip');
				$label .= ' title="' . JHtml::tooltipText(trim($text, ':'), JText::_($this->description), 0) . '"';
			}

			// Add the label text and closing tag.
			$label .= '>' . $text . '</h3>';
			$html[] = $label;

		$html[] = '</div>';
		$html[] = '<span class="after"></span>';
		$html[] = '</span>';

		return implode('', $html);
    }
}