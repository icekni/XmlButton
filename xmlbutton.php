<?php
/**
* 2007-2023 PrestaShop
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
*  @copyright 2007-2023 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Xmlbutton extends Module
{
    public function __construct()
    {
        $this->name = 'xmlbutton';
        $this->tab = 'administration';
        $this->version = '0.1.0';
        $this->author = 'Cédric Josso';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('XML Button', [], 'Modules.Xmlbutton.Xmlbutton');
        $this->description = $this->trans('Add an \"XML export\" action in Product Catalog page', [], 'Modules.Xmlbutton.Xmlbutton');

        $this->confirmUninstall = $this->trans('U Sure?', [], 'Modules.Xmlbutton.Xmlbutton');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }
    
    public function isUsingNewTranslationSystem()
    {
        return true;
    }

    /**
     * Module installation.
     *
     * @return bool Success of the installation
     */
    public function install()
    {
        return parent::install()
            && $this->registerHook('displayDashboardToolbarIcons');
    }

    public function uninstall()
    {
        return parent::uninstall()
            && $this->unregisterHook('displayDashboardToolbarIcons');
    }
    
    /**
    * Add an "XML export" action in Product Catalog page.
    *
    * @return bool Success of the installation
    */
    public function hookDisplayDashboardToolbarIcons($hookParams)
    {
        if ($this->isSymfonyContext() && $hookParams['route'] === 'admin_product_catalog') {
            $products = $this->get('product_repository')->findAllByLangId(1);
            
            $productsXml = $this->get('serializer')->serialize(
                $products,
                'xml',
                [
                    'xml_root_node_name' => 'products',
                    'xml_format_output' => true,
                ]
            );
            $this->get('filesystem')->dumpFile(_PS_UPLOAD_DIR_ . 'products.xml', $productsXml);

            return $this->get('twig')->render('@Modules/xmlbutton/views/xmlbutton.twig', [
                'filepath' => __PS_BASE_URI__  . 'upload/' . 'products.xml',
            ]);
        }
    }
}
