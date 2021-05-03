<?php
IncludeModuleLangFile(__FILE__);

use Bitrix\Main\Application;
use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bitrix\Sale\Cashbox\Internals\CashboxTable;
use Bitrix\Sale\Cashbox\Manager;

Class thepointllc_ecomkassa extends CModule
{
    var $MODULE_ID = "thepointllc.ecomkassa";
    const CASHBOX_HANDLER_DB = 'Ecomkassa\\\AtolOnline';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $PARTNER_NAME;
    var $PARTNER_URI;
    var $strError = '';

  function __construct()
    {
        $arModuleVersion = array();
        include(dirname(__FILE__)."/version.php");

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

        $this->MODULE_NAME = GetMessage("ecomkassa.atol_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("ecomkassa.atol_MODULE_DESC");
        $this->PARTNER_NAME = GetMessage("ecomkassa.atol_PARTNER_NAME");
        $this->PARTNER_URI = GetMessage("ecomkassa.atol_PARTNER_URI");
    }

    public function InstallEvents()
    {
        EventManager::getInstance()->registerEventHandlerCompatible('sale', "OnGetCustomCashboxHandlers", $this -> MODULE_ID, "CEcomkassaAtolOnline", "registerMainClass");
        return true;
    }
    public function UnInstallEvents()
    {
        EventManager::getInstance()->unRegisterEventHandler('sale', "OnGetCustomCashboxHandlers", $this -> MODULE_ID, "CEcomkassaAtolOnline", "registerMainClass");
        return true;
    }

    private function getLogDirPath() {
        return Application::getDocumentRoot() . '/ecomkassa_atol_online';
    }

    public function InstallFiles()
    {
        CopyDirFiles(
            __DIR__."/files/logs",
            $this->getLogDirPath()
        );

        return true;
    }

    public function UnInstallDB()
    {
        if (Loader::includeModule('sale')) {

            $cashbox_db_off = array('ACTIVE' => 'N');


            $dbRes = CashboxTable::getList(
                array(
                    'select' => array('ID'),
                    'filter' => array('HANDLER' => $this::CASHBOX_HANDLER_DB),
                )
            );

            while ($cashbox = $dbRes->fetch())
            {
                Manager::update($cashbox['ID'], $cashbox_db_off);
            }

            return true;
        }
        return false;
    }

    public function DoInstall()
    {
        $this->InstallFiles();
        $this->InstallEvents();
        RegisterModule($this -> MODULE_ID);
    }

    public function DoUninstall()
    {
        $this->UnInstallDB();
        $this->UnInstallEvents();
        UnRegisterModule($this -> MODULE_ID);
    }
}

