<?php
/**
 * @package Core
 */

/**
 * TopModuleDepot
 *
 * TODO: rename this class
 * 
 * Responsibility:
 *    - creating instances of "Top" Modules
 *    - accessing  "Top" Modules
 *    - filtering access to "Top" Modules depending on User's credentials
 *
 * @package Core
 */
class NethGui_Core_TopModuleDepot implements NethGui_Core_ModuleSetInterface, NethGui_Authorization_PolicyEnforcementPointInterface
{

    /**
     * @var array
     */
    private $modules = array();
    private $menu = array();
    /**
     * Policy Decision Point is applied to all attached modules and panels
     * that implement PolicyEnforcementPointInterface.
     *
     * @var PolicyDecisionPointInterface
     */
    private $policyDecisionPoint;
    /**
     * @var NethGui_Core_UserInterface
     */
    private $user;
    /**
     * @var HostConfigurationInterface
     */
    private $hostConfiguration;

    public function __construct(NethGui_Core_HostConfigurationInterface $hostConfiguration, NethGui_Core_UserInterface $user)
    {
        $this->hostConfiguration = $hostConfiguration;
        $this->user = $user;
        $this->createTopModules();
        // XXX: add Notification module here?
        $notificationModule = $this->createModule('NethGui_Module_Notification');
        $this->registerModule($notificationModule);        
    }

    /**
     * Include all files under `libraries/module/` directory
     * ending with `Module.php` and create an instance
     * for each Module class.
     */
    private function createTopModules()
    {
        // TODO: set a configuration parameter for modules directory
        $directoryIterator = new DirectoryIterator(dirname(__FILE__) . '/../../NethService/Module/');
        foreach ($directoryIterator as $element) {
            if (substr($element->getFilename(), -4) == '.php') {

                $className = 'NethService_Module_' . substr($element->getFileName(), 0, -4);

                $classReflector = new ReflectionClass($className);

                if ($classReflector->isInstantiable()
                    && $classReflector->implementsInterface("NethGui_Core_TopModuleInterface")
                ) {
                    $module = $this->createModule($className);
                    $this->registerModule($module);
                } else {
                    // TODO: log a warning
                }
            }
        }
    }

    /**
     * Create an instance of $className, checking for valid identifier.
     *
     * @param string $className
     * @return ModuleInterface
     */
    private function createModule($className)
    {
        $module = new $className();

        if ( ! $module instanceof NethGui_Core_ModuleInterface) {
            throw new Exception("Class `{$className}` must implement NethGui_Core_ModuleInterface");
        }

        if (is_null($module->getIdentifier())) {
            throw new Exception("Each module must provide an unique identifier.");
        }

        $module->setHostConfiguration($this->hostConfiguration);

        if(ENVIRONMENT == 'development') {
            NethGui_Framework::getInstance()->logMessage("Created `" . $module->getIdentifier() . "`, as `{$className}` instance.", 'debug');
        }

        return $module;
    }

    /**
     * Adds $module to this set. Each member of this set
     * shares the same Policy Decision Point.
     * @param NethGui_Core_ModuleInterface $module The module to be attached to this set.
     */
    public function registerModule(NethGui_Core_ModuleInterface $module)
    {
        if (isset($this->modules[$module->getIdentifier()])) {
            throw new Exception("Module id `" . $module->getIdentifier() . "` is already registered.");
        }

        $this->modules[$module->getIdentifier()] = $module;

        if ($module instanceof NethGui_Core_TopModuleInterface) {
            $parentId = $module->getParentMenuIdentifier();
            if (is_null($parentId)) {
                $parentId = '__ROOT__';
            }
            $this->menu[$parentId][] = $module->getIdentifier();
        }
    }

    /**
     * Use $pdp as Policy Decision Point for each member of the set
     * that implements PolicyEnforcementPointInterface.
     * @param NethGui_Authorization_PolicyDecisionPointInterface $pdp
     * @return void
     */
    public function setPolicyDecisionPoint(NethGui_Authorization_PolicyDecisionPointInterface $pdp)
    {
        $this->policyDecisionPoint = $pdp;

        foreach ($this->modules as $module) {
            if ($module instanceof NethGui_Authorization_PolicyEnforcementPointInterface) {
                $module->setPolicyDecisionPoint($pdp);
            }
        }
    }

    public function getPolicyDecisionPoint()
    {
        return $this->policyDecisionPoint;
    }


    /**
     *
     * @return RecursiveIterator
     */
    public function getModules()
    {
        // TODO: authorize access
        return new NethGui_Core_ModuleMenuIterator($this, '__ROOT__', $this->menu);
    }

    /**
     * @return NethGui_Core_ModuleInterface
     */
    public function findModule($moduleIdentifier)
    {
        if (is_null($moduleIdentifier)
            OR ! isset($this->modules[$moduleIdentifier])) {
            return NULL;
        }
        return $this->modules[$moduleIdentifier];
    }

}
