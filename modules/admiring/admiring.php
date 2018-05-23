<?php
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpUndefinedFieldInspection */

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class Admiring
 *
 * @author François MATHIEU <francois.mathieu@livexp.fr>
 */
class Admiring extends Module
{
    /** @var string $moduleDir */
    private $moduleDir;

    public function __construct()
    {
        $this->name = 'admiring';
        $this->tab = 'advertising_marketing';
        $this->version = '0.1.0';
        $this->author = 'François MATHIEU';
        $this->displayName = 'Admiring';
        $this->description = 'Avec ce module, vos clients pourront faire quelque chose !';
        $this->bootstrap = true;
        parent::__construct();
        $this->moduleDir = _PS_MODULE_DIR_ . $this->name;
    }

    /**
     * @return bool
     */
    public function install()
    {
        parent::install();
        $this->createTable();
        $this->registerHook('displayReassurance');

        Configuration::updateValue('ADMIRING_COMMENTS', 1);
        Configuration::updateValue('ADMIRING_GRADES', 1);

        return true;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        $this->context->smarty->assign('form', $this->renderAdminForm());
        $this->processAdminForm();

        return $this->fetch($this->moduleDir . "/views/templates/hook/getContent.tpl");
    }

    /* Admin HelperForm */

    /**
     * @return string
     */
    public function getAdminUrl()
    {
        return $this->context->link->getAdminLink('AdminModules') .
            '&configure=' . $this->name . '&tab_module=' . $this->tab . '&conf=4&module_name=' . $this->name;
    }

    public function processAdminForm()
    {
        if (Tools::isSubmit('submit_admiring_form')) {
            $enable_comments = Tools::getValue('enable_comments');
            Configuration::updateValue('ADMIRING_COMMENTS', $enable_comments);
            $enable_grades = Tools::getValue('enable_grades');
            Configuration::updateValue('ADMIRING_GRADES', $enable_grades);
            $this->context->smarty->assign('confirmation', 'ok');

            Tools::redirectAdmin($this->getAdminUrl());
        }
    }

    /**
     * @return array
     */
    private function getAdminFormFields()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->l('My Module configuration'),
                    'icon' => 'icon-envelope'
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Enable grades:'),
                        'name' => 'enable_grades',
                        'desc' => $this->l('Enable grades on products.'),
                        'values' => [
                            [
                                'id' => 'enable_grades_1',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ],
                            [
                                'id' => 'enable_grades_0',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            ]
                        ]
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Enable comments:'),
                        'name' => 'enable_comments',
                        'desc' => $this->l('Enable comments on products.'),
                        'values' => [
                            [
                                'id' => 'enable_comments_1',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ],
                            [
                                'id' => 'enable_comments_0',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            ]
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ]
            ],
        ];
    }

    /**
     * @return string
     */
    private function renderAdminForm()
    {
        $helper = new HelperForm();
        $helper->table = 'admiring_comments';
        $helper->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_lang = (int)Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG');
        $helper->submit_action = 'submit_admiring_form';
        $helper->currentIndex = $this->getAdminUrl();
        $helper->tpl_vars = [
            'fields_value' => [
                'enable_grades' => Tools::getValue('enable_grades', Configuration::get('ADMIRING_GRADES')),
                'enable_comments' => Tools::getValue('enable_comments', Configuration::get('ADMIRING_COMMENTS')),
            ],
            'languages' => $this->context->controller->getLanguages()
        ];

        return $helper->generateForm([$this->getAdminFormFields()]);
    }

    /* DisplayReassurance HelperForm */

    public function processForm()
    {
        if (Tools::isSubmit('submit_admiring_comment')) {
            $admiringComment = new AdmiringComment();
            $admiringComment->grade = Tools::getValue('admiring_grade');
            $admiringComment->comment = Tools::getValue('admiring_comment');
            $admiringComment->id_product = Tools::getValue('id_product');

            $admiringComment->save();

            $this->context->smarty->assign('confirmation', 'ok');
        }
    }

    /**
     * @return string
     */
    public function hookDisplayReassurance()
    {
        $this->smarty->assign('module_name', $this->l('Commentaires sur le produit'));
        $this->smarty->assign('enable_grades', Configuration::get('ADMIRING_GRADES'));
        $this->smarty->assign('enable_comments', Configuration::get('ADMIRING_COMMENTS'));

        $this->processForm();

        return $this->fetch($this->moduleDir . "/views/templates/hook/displayReassurance.tpl");
    }

    protected function createTable()
    {
        $requete = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "admiring_comment` (
    `id_admiring_comment` int(11) NOT NULL AUTO_INCREMENT,
    `id_product` int(11) NOT NULL,
    `grade` tinyint(1) NOT NULL,
    `comment` text NOT NULL,
    `date_add` datetime NOT NULL,
    PRIMARY KEY (`id_admiring_comment`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

        Db::getInstance()->execute($requete);
    }

    public function dropTable()
    {
        $query = "`" . _DB_PREFIX_ . "admiring_comment`";

        Db::getInstance()->execute($query);
    }

}
