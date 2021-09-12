<?php
/**
* 2007-2021 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2021 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class WhatsappS extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'whatsappS';
        $this->tab = 'front_office_features';
        $this->version = '1.7.7';
        $this->author = 'luis';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('whatsappS');
        $this->description = $this->l('whatsappSwhatsappSwhatsappS');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('WHATSAPPS_LIVE_MODE', false);

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('displayFooter');
    }

    public function uninstall()
    {
        Configuration::deleteByName('WHATSAPPS_LIVE_MODE');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitWhatsappSModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitWhatsappSModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                  
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-phone"></i>',
                        'desc' => $this->l('Enter a valid Phone number'),
                        'name' => 'WHATSAPPS_ACCOUNT_EMAIL',
                        'label' => $this->l('Whats App Phone Number'),
                    ),
                    
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'WHATSAPPS_LIVE_MODE' => Configuration::get('WHATSAPPS_LIVE_MODE', true),
            'WHATSAPPS_ACCOUNT_EMAIL' => Configuration::get('WHATSAPPS_ACCOUNT_EMAIL', ''),
            'WHATSAPPS_ACCOUNT_PASSWORD' => Configuration::get('WHATSAPPS_ACCOUNT_PASSWORD', null),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

    public function hookDisplayFooter()
    {
        return"<div class='whatapp'>

        <a href=' https://wa.me/22966001393/?text=Bonjour Mme.Je suis intéressée par un de vos articles.' target='_blanc'>
          <img src='data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjAgMCA1MTIgNTEyIiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA1MTIgNTEyOyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjI0cHgiIGhlaWdodD0iMjRweCI+CjxwYXRoIHN0eWxlPSJmaWxsOiNFREVERUQ7IiBkPSJNMCw1MTJsMzUuMzEtMTI4QzEyLjM1OSwzNDQuMjc2LDAsMzAwLjEzOCwwLDI1NC4yMzRDMCwxMTQuNzU5LDExNC43NTksMCwyNTUuMTE3LDAgIFM1MTIsMTE0Ljc1OSw1MTIsMjU0LjIzNFMzOTUuNDc2LDUxMiwyNTUuMTE3LDUxMmMtNDQuMTM4LDAtODYuNTEtMTQuMTI0LTEyNC40NjktMzUuMzFMMCw1MTJ6Ii8+CjxwYXRoIHN0eWxlPSJmaWxsOiM1NUNENkM7IiBkPSJNMTM3LjcxLDQzMC43ODZsNy45NDUsNC40MTRjMzIuNjYyLDIwLjMwMyw3MC42MjEsMzIuNjYyLDExMC4zNDUsMzIuNjYyICBjMTE1LjY0MSwwLDIxMS44NjItOTYuMjIxLDIxMS44NjItMjEzLjYyOFMzNzEuNjQxLDQ0LjEzOCwyNTUuMTE3LDQ0LjEzOFM0NC4xMzgsMTM3LjcxLDQ0LjEzOCwyNTQuMjM0ICBjMCw0MC42MDcsMTEuNDc2LDgwLjMzMSwzMi42NjIsMTEzLjg3Nmw1LjI5Nyw3Ljk0NWwtMjAuMzAzLDc0LjE1MkwxMzcuNzEsNDMwLjc4NnoiLz4KPHBhdGggc3R5bGU9ImZpbGw6I0ZFRkVGRTsiIGQ9Ik0xODcuMTQ1LDEzNS45NDVsLTE2Ljc3Mi0wLjg4M2MtNS4yOTcsMC0xMC41OTMsMS43NjYtMTQuMTI0LDUuMjk3ICBjLTcuOTQ1LDcuMDYyLTIxLjE4NiwyMC4zMDMtMjQuNzE3LDM3Ljk1OWMtNi4xNzksMjYuNDgzLDMuNTMxLDU4LjI2MiwyNi40ODMsOTAuMDQxczY3LjA5LDgyLjk3OSwxNDQuNzcyLDEwNS4wNDggIGMyNC43MTcsNy4wNjIsNDQuMTM4LDIuNjQ4LDYwLjAyOC03LjA2MmMxMi4zNTktNy45NDUsMjAuMzAzLTIwLjMwMywyMi45NTItMzMuNTQ1bDIuNjQ4LTEyLjM1OSAgYzAuODgzLTMuNTMxLTAuODgzLTcuOTQ1LTQuNDE0LTkuNzFsLTU1LjYxNC0yNS42Yy0zLjUzMS0xLjc2Ni03Ljk0NS0wLjg4My0xMC41OTMsMi42NDhsLTIyLjA2OSwyOC4yNDggIGMtMS43NjYsMS43NjYtNC40MTQsMi42NDgtNy4wNjIsMS43NjZjLTE1LjAwNy01LjI5Ny02NS4zMjQtMjYuNDgzLTkyLjY5LTc5LjQ0OGMtMC44ODMtMi42NDgtMC44ODMtNS4yOTcsMC44ODMtNy4wNjIgIGwyMS4xODYtMjMuODM0YzEuNzY2LTIuNjQ4LDIuNjQ4LTYuMTc5LDEuNzY2LTguODI4bC0yNS42LTU3LjM3OUMxOTMuMzI0LDEzOC41OTMsMTkwLjY3NiwxMzUuOTQ1LDE4Ny4xNDUsMTM1Ljk0NSIvPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K'>
         </a>
        
        </div>";
       
    }
}
