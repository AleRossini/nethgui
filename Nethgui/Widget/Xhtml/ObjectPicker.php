<?php
/**
 * @package Widget
 * @subpackage Xhtml
 * @author Davide Principi <davide.principi@nethesis.it>
 * @internal
 * @ignore
 */

/**
 *
 * @package Widget
 * @subpackage Xhtml
 * @internal
 * @ignore
 */
class Nethgui_Widget_Xhtml_ObjectPicker extends Nethgui_Widget_Xhtml
{

    private $metadata;
    private $values = array();

    private function initializeRendering()
    {
        $this->metadata = array(
            'value' => $this->getAttribute('objectValue', 0),
            'label' => $this->getAttribute('objectLabel', $this->getAttribute('objectValue', 0)),
            'url' => $this->getAttribute('objectUrl', FALSE),
            'listenToEvents' => array(),
            'selector' => FALSE,
        );

        $name = $this->getAttribute('name', FALSE);
        if ( ! empty($name)) {
            $this->insert($this->view->checkBox($name, '', 0));
            $this->metadata['selector'] = $name;
        }

        foreach ($this->getChildren() as $child) {
            $childName = $child->getAttribute('name');

            if ($this->view[$childName] instanceof Traversable) {
                $value = iterator_to_array($this->view[$childName]);
            } elseif (is_array($this->view[$childName])) {
                $value = $this->view[$childName];
            } elseif (empty($this->view[$childName])) {
                $value = array();
            } else {
                throw new Nethgui_Exception_View(sprintf('Invalid value type for %s: %s', $childName, var_export($this->view[$childName], TRUE)));
            }

            $this->values[$childName] = $value;
            $this->metadata['listenToEvents'][] = $this->view->getClientEventTarget($childName);
        }
    }

    public function insert(Nethgui_Renderer_WidgetInterface $child)
    {
        if ( ! $child instanceof Nethgui_Widget_Xhtml_CheckBox) {
            throw new Nethgui_Exception_View(sprintf('Unsupported widget class: %s', get_class($child)));
        }

        $childFlags = $child->getAttribute('flags', 0);

        // Mask STATE_DISABLED and LABEL_* flags:
        $childFlags &= ~ (Nethgui_Renderer_Abstract::LABEL_ABOVE | Nethgui_Renderer_Abstract::LABEL_RIGHT | Nethgui_Renderer_Abstract::LABEL_LEFT | Nethgui_Renderer_Abstract::STATE_CHECKED);

        // Force to STATE_DISABLED & LABEL_RIGHT
        $childFlags |= Nethgui_Renderer_Abstract::LABEL_RIGHT | Nethgui_Renderer_Abstract::STATE_DISABLED;

        // Fix the flags:
        $child->setAttribute('flags', $childFlags);

        return parent::insert($child);
    }

    public function render()
    {
        $this->initializeRendering();

        $content = '';
        $content .= $this->openTag('div', array('class' => 'ObjectPicker ' . implode(' ', $this->metadata['listenToEvents'])));
        $content .= $this->controlTag('input', 'meta', 0, '', array('type' => 'hidden', 'disabled' => 'disabled', 'value' => json_encode($this->metadata), 'class' => 'metadata'));
        $content .= $this->openTag('div', array('class' => 'schema'));
        $content .= $this->renderChildren();
        $content .= $this->closeTag('div');
        $content .= $this->openTag('div', array('class' => 'searchbox'));
        $content .= $this->selfClosingTag('input', array('type' => 'text', 'class' => 'TextInput', 'disabled' => 'disabled', 'value' => '', 'placeholder' => $this->view->translate('Search...')));
        $content .= ' ' . $this->openTag('button', array('type' => 'button', 'class' => 'Button custom', 'disabled' => 'disabled')) . htmlspecialchars($this->view->translate('Add')) . $this->closeTag('button');
        $content .= $this->closeTag('div');
        $content .= $this->renderObjects();
        $content .= $this->closeTag('div');

        if ($this->hasAttribute('template')) {
            $fieldsetWidget = new Nethgui_Widget_Xhtml_Fieldset($this->view);
            $fieldsetWidget
                ->setAttribute('template', $this->getAttribute('template'))
                ->setAttribute('icon-before', $this->getAttribute('icon-before'))
                ->insert($this->view->literal($content))
            ;

            return $fieldsetWidget;
        }

        return $content;
    }

    protected function renderChildren()
    {
        $output = '';

        foreach ($this->getChildren() as $child) {
            $child->setAttribute('flags', Nethgui_Renderer_Abstract::STATE_DISABLED | intval($child->getAttribute('flags')));
            $child->setAttribute('uncheckedValue', FALSE);
            $child->setAttribute('value', '');
            $output .= $this->wrapChild($child->render());
        }

        return $output;
    }

    private function renderObjects()
    {
        $objects = $this->getAttribute('objects', array());

        $attributes = array();

        if (is_string($objects)) {
            $attributes['class'] = 'Objects ' . $this->view->getClientEventTarget($objects);
            $attributes['id'] = $this->view->getUniqueId($objects);
            $objects = $this->view[$objects];
        } else {
            $attributes['class'] = 'Objects ' . $this->view->getClientEventTarget('Datasource');
            $attributes['id'] = $this->view->getUniqueId('Datasource');
        }

        $content = $this->openTag('div', $attributes);

        if ((is_array($objects) || $objects instanceof Countable) && count($objects) > 0) {
            $content .= '<ul>';

            foreach ($objects as $index => $object) {
                $content .= '<li>';
                $content .= $this->renderObjectWidget($index, $object);
                $content .= '</li>';
            }

            $content .= '</ul>';
        }

        $content .= $this->closeTag('div');

        return $content;
    }

    private function renderObjectWidget($index, $object)
    {
        $flags = $this->getAttribute('flags', 0);

        $content = '';

        $contentSelectionFragment = '';
        $contentProperties = '';

        if ($this->metadata['url'] && isset($object[$this->metadata['url']])) {
            $content .= $this->openTag('a', array('class' => 'label', 'href' => $object[$this->metadata['url']])) . htmlspecialchars($this->view->translate($object[$this->metadata['label']], array())) . $this->closeTag('a');
        } else {
            $content .= $this->openTag('span', array('class' => 'label')) . htmlspecialchars($this->view->translate($object[$this->metadata['label']], array())) . $this->closeTag('span');
        }

        $content .= '<span class="checkboxset">';
        foreach ($this->getChildren() as $child) {
            $childClone = clone $child;

            $childFlags = $child->getAttribute('flags', 0);

            // Mask STATE_DISABLED
            $childFlags &= ~Nethgui_Renderer_Abstract::STATE_DISABLED;

            if (in_array($object[$this->metadata['value']], $this->values[$child->getAttribute('name')])) {
                $childFlags |= Nethgui_Renderer_Abstract::STATE_CHECKED;
            } else {
                $childFlags &= ~Nethgui_Renderer_Abstract::STATE_CHECKED;
            }

            $childClone->setAttribute('flags', $childFlags);
            $childClone->setAttribute('name', $child->getAttribute('name') . '/' . $index);
            $childClone->setAttribute('label', $child->getAttribute('label', $child->getAttribute('name') . '_label'));
            $childClone->setAttribute('value', $object[$this->metadata['value']]);

            if ( ! $child->hasAttribute('uncheckedValue')) {
                $childClone->setAttribute('uncheckedValue', FALSE);
            }

            $content .= $childClone->render();
            //$content .= $childClone->label($childClone->getAttribute('label'), $this->view->getUniqueId($childClone->getAttribute('name')));
        }
        $content .= '</span>';

        return $content;
    }

}
