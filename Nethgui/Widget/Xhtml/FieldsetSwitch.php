<?php
namespace Nethgui\Widget\Xhtml;

/*
 * Copyright (C) 2011 Nethesis S.r.l.
 * 
 * This script is part of NethServer.
 * 
 * NethServer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * NethServer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with NethServer.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *
 */
class FieldsetSwitch extends \Nethgui\Widget\XhtmlWidget
{

    protected function renderContent()
    {
        $name = $this->getAttribute('name');
        $value = $this->getAttribute('value');
        $flags = $this->getAttribute('flags');
        $content = '';

        if ($flags & \Nethgui\Renderer\WidgetFactoryInterface::FIELDSETSWITCH_CHECKBOX) {
            $chooser = new CheckBox($this->view);
            if ($this->hasAttribute('uncheckedValue')) {
                $chooser->setAttribute('uncheckedValue', $this->getAttribute('uncheckedValue'));
            }
        } else {
            $chooser = new RadioButton($this->view);
        }

        $chooser
            ->setAttribute('name', $name)
            ->setAttribute('value', $value)
            ->setAttribute('flags', $flags)
        ;

        if ($this->hasAttribute('label')) {
            $chooser->setAttribute('label', $this->getAttribute('label'));
        }

        if ($this->hasAttribute('labelSource')) {
            $chooser->setAttribute('labelSource', $this->getAttribute('labelSource'));
        }

        $className = 'FieldsetSwitch';

        if ($flags & \Nethgui\Renderer\WidgetFactoryInterface::FIELDSETSWITCH_EXPANDABLE) {
            $className .= ' expandable';
        }

        if($this->hasAttribute('class')) {
            $className .= ' ' . trim($this->getAttribute('class'));
        }

        $content .= $this->openTag('div', array('class' => $className));
        $content .= $chooser->renderContent();
        $content .= $this->openTag('fieldset', array('class' => 'FieldsetSwitchPanel'));
        $content .= $this->renderChildren();
        $content .= $this->closeTag('fieldset');
        $content .= $this->closeTag('div');

        return $content;
    }

}
