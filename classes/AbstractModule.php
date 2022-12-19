<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

spl_autoload_register(function ($class_name) {
    $filePath = dirname(__FILE__) . '/ObjectModel/' . $class_name . '.php';
    if(file_exists($filePath))
        include $filePath;
});

$autoloadPath = dirname(__FILE__) . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

require_once "DbManager.php";

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
        if (!parent::install()) {
            return false;
        }

        foreach ($this->hooks as $hook) {
            if (!$this->registerHook($hook)) {
                $this->_errors[] = $this->l('Failed to install hook') . ' ' . $hook . '.';
                return false;
            }
        }

        if (!$this->createDbTables()) {
            $this->_errors[] = $this->l('Failed to create tables.');
            return false;
        }

        return $this->registerTabs();
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
        $db = new DbManager($this->tables);

        $db->deleteTables();
        $this->deleteTabs();

        if (!parent::uninstall()) {
            return false;
        }

        return true;
    }

    /**
     * Create module database tables
     */
    public function createDbTables()
    {
        try {
            $db = new DbManager($this->tables);
            $result = $db->createTables();
        } catch (Exception $e) {
            $result = false;
        }
        return $result;
    }
}