<?php
/**
 * NethGui
 *
 * @package Core
 */

/**
 * Each module has a view attacched to it during prepareView operation. A view
 * contains generic elements such as strings, numbers or other (inner) views.
 *
 * @package Core
 */
interface NethGui_Core_ViewInterface extends ArrayAccess, IteratorAggregate
{

    /**
     * Set the template to be applied to this object.
     *
     * If a string is given, it identifies a PHP Template script
     * (ie. NethGui_View_MyTemplate).
     *
     * If a callback function is given, it is invoked with an array
     * representing the view state as argument and is expected to return
     * a string representing the view.
     *
     * @see render();
     * @param string|callback $template The template converting the view state to a string
     */
    public function setTemplate($template);
    
    /**
     * @see setTemplate()
     */
    public function getTemplate();

    /**
     * Assign data to the View state.
     * @param $data
     */
    public function copyFrom($data);

    /**
     * Create a new view object associated to $module
     * @param NethGui_Core_ModuleInterface $module The associated $module
     * @param boolean Optional If TRUE the returned view is added to the current object with key equal to the module identifier
     * @return NethGui_Core_ViewInterface The new view object, of the same type of the actual.
     */
    public function spawnView(NethGui_Core_ModuleInterface $module, $register = FALSE);

    /**
     * Renders a string representation of the view, performing string translations
     * on view string elements.
     * @see setTemplate();
     * @return string
     */
    public function render();

    /**
     * The module associated to this view.
     * @return NethGui_Core_ModuleInterface
     */
    public function getModule();
    
    /**
     * Gets the array of the current module identifier plus all identifiers of
     * the ancestor modules, starting from the root.   
     *
     * @return array
     */
    public function getModulePath();
    
    /**
     * A (shortcut) method to translate a message in the current language.
     * 
     * @see NethGui_Framework::translate()
     */
    public function translate($message, $args = array());           
      
}

?>