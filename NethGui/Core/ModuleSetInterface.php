<?php
/**
 * NethGui
 *
 * @package NethGuiFramework
 */

/**
 * A ModuleSetInterface implementation contains all known modules.
 * 
 * It allows finding a module and iterating over root modules,
 * arranged in a hierarchical structure.
 *
 * @package NethGuiFramework
 */
interface ModuleSetInterface {

    /**
     * @return RecursiveIterator A RecursiveIterator to iterate over all accessible Modules
     */
    public function getTopModules();

    /**
     * @param string $moduleIdentifier
     * @return ModuleInterface
     */
    public function findModule($moduleIdentifier);

}
