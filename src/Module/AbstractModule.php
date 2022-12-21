<?php

namespace CustomProductImages\Module;

use CustomProductImages\Helper\DbManager;
use Module, Tab, Language, Validate;

class AbstractModule extends Module
{
    protected $tabs = [];

    /**
     * List of Prestashop hooks used by the module.
     */
    protected $hooks = [];

    /**
     * List of Database tables used by the module.
     */
    protected $tables = [];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Module installation function
     */
    public function install()
    {
        if (!$this->createDbTables()) {
            $this->_errors[] = $this->l('Failed to create tables.');
            return false;
        }

        if(!parent::install())
        {
            $this->_errors[] = $this->l('Failed to install the module.');
            return false;
        }

        foreach ($this->hooks as $hook) {
            if (!$this->registerHook($hook)) {
                $this->_errors[] = $this->l('Failed to install hook') . ' ' . $hook . '.';
                return false;
            }
        }

        if(!$this->registerTabs())
        {
            $this->_errors[] = $this->l('Failed to install tabs for the module.');
            return false;
        }
        return true;
    }

    /**
     * Registers module Admin tabs (controllers)
     */
    private function registerTabs()
    {
        foreach ($this->tabs as $controller => $tabData) {
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = $controller;
            $tab->name = [];
            $languages = Language::getLanguages(false);

            foreach ($languages as $language) {
                $tab->name[$language['id_lang']] = $tabData['title'];
            }

            $tab->id_parent = Tab::getIdFromClassName($tabData['parent_tab']);
            $tab->module = $this->name;
            if (!$tab->save()) {
                $this->displayError($this->l('Error while creating tab ') . $tabData['title']);
                return false;
            }
        }
        return true;
    }

    /**
     * Deletes module Admin controllers
     * Used for module uninstall
     *
     * @return bool Module Admin controllers deleted successfully
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function deleteTabs()
    {
        foreach (array_keys($this->tabs) as $controller) {
            $idTab = (int) Tab::getIdFromClassName($controller);
            $tab = new Tab((int) $idTab);

            if (!Validate::isLoadedObject($tab)) {
                continue; // Nothing to remove
            }

            if (!$tab->delete()) {
                $this->displayError($this->l('Error while uninstalling tab') . ' ' . $tab->name);
                return false;
            }
        }

        return true;
    }

    /**
     * Module uninstall function
     */
    public function uninstall()
    {
        $db = new DbManager($this->tables, $this);

        $res = true;
        $res &= $db->deleteTables();
        $res &= $this->deleteTabs();

        return $res && parent::uninstall();
    }

    /**
     * Create module database tables
     */
    public function createDbTables()
    {
        $db = new DbManager($this->tables, $this);
        $result = $db->createTables();
        return $result;
    }
}