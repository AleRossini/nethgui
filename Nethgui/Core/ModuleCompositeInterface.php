<?php
/**
 * @package Core
 */

/**
 * A complex module, composed by other modules, must implement this interface.
 *
 * @package Core
 */
interface Nethgui_Core_ModuleCompositeInterface
{

    /**
     * @return array An array of Nethgui_Core_ModuleInterface implementing objects.
     */
    public function getChildren();

    /**
     * Adds a child to this Composite. Implementations must send a setParent()
     * message to $module.
     * @param Nethgui_Core_ModuleInterface $module The child module.
     */
    public function addChild(Nethgui_Core_ModuleInterface $module);
}
