<?php
/**
 * @package Core
 */

/**
 * Request handlers executes Module logics.
 * 
 * A request handler is delegated to
 * - receive input parameters (parameter binding),
 * - validate,
 * - perform process()-ing.
 *
 * @see Nethgui_Core_ModuleInterface
 * @package Core
 */
interface Nethgui_Core_RequestHandlerInterface {

    /**
     * Put the request into the object internal state.
     * @param Nethgui_Core_RequestInterface $request
     */
    public function bind(Nethgui_Core_RequestInterface $request);

    /**
     * Validate object state. Errors are sent to $report.
     * @return void
     */
    public function validate(Nethgui_Core_ValidationReportInterface $report);

    /**
     * Module behaviour implementation.
     *
     * @return void
     */
    public function process();
    
}