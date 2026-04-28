<?php
return [
    'categories' => 'Categories',
    'subcategory' => 'Sub Category',
    'category' => 'Category',
    'categories_allowed' => 'Categories by user (allowed)',
    'titulo_PosCustom' => 'Custom POS',
    'enabled' => 'Enabled',
    'buttonpos' => 'Enabled boton POS',
    'stock_pos' => 'Stock',
    'next' => 'Next',
    'previous' => 'Previous',
    'PosCustom_module' =>'Module PosCustom',
    'pos_display' => 'Second Display',
    'product' => 'Product',
    'pos_display_caption' => 'Thank you for Shopping with Us!',
    'no_items' => 'Car is empty',
    'enable_units' => 'Enabled units POS',
    'poscustom_settings' => 'PosCustom Settings',
    'poscustom_width_totals' => 'POS width left',
    'poscustom_width_totals_ttip' => 'To calculate the width left and right just put the width on the left. The width of the right it will be 100 - width_left',
    'poscustom_secondscreen_show' => 'Show Second Screen',
    'poscustom_btncate_show' => 'Show button categories',
    'poscustom_bar_show' => 'Quick access category',
    'poscustom_show_units' => 'Show units',
    'poscustom_show_imgproduct' => 'Show IMG for products in totals',
    'show_advertisement_s1' => 'Advertisement style 1',
    'show_advertisement_s2' => 'Advertisement style 2',
    'poscustom_width_badge_show' => 'Width badge stock',
    'poscustom_width_product' => 'Width for product (Beta)',
    'poscustom_width_product_help' => 'Width (pixels) of each box product to calculate the # of box products that can be show in the side right POS (Items)',
    'poscustom_default_category' => 'Default category ',
    'poscustom_default_category_help' => 'Use for show the default category in the POS',
    /**PosCustom Styles */
    'poscustom_styles' => 'PosCustom Styles',

    /** Position of products on POS */
    'plrstyle0' => 'Products on right side',
    'plrstyle1' => 'Products on left side',
    'poscustom_ppositionlr_ttip' =>'Define the position of the window of products, on the left or right in the POS. Change the left side if it is necessary',    
    'poscustom_ppositionlr_lang' =>'Position for product window (POS)',  

    /** Style for segment totals button on totals or fixed bottom */
    'totalstyle0' => 'Action buttons down (fixed)',
    'totalstyle1' => 'Action buttons left',
    'poscustom_totalstyle_ttip' =>'Allowed to enable style for totals in the POS',    
    'poscustom_totalstyle_lang' =>'Styles for Totals',    

    /* Styles pos_form_action buttons*/
    'bstyle0' => 'Btn action default',
    'bstyle1' => 'Btn hover color',
    'bstyle1t' => 'Btn TAB hover color',
    'bstyle2' => 'Btn degrade color',
    'bstyle2t' => 'Btn TAB degrade color',
    'poscustom_btnstyle_ttip' =>'Allowed to enable the styles for button actions in the POS',    
    'poscustom_btnstyle_lang' =>'Styles POS button actions',

    /* Styles pos_form_action right side*/
    'catestyle0' => 'Category on Top',
    'catestyle1' => 'Category on Left',
    'poscustom_catestyle_ttip' =>'Allowed to enable the position for categories in products',    
    'poscustom_catestyle_lang' =>'Position for categories',

    /* Left Size POS*/
    'poscustom_left_size' => 'Size for left window in the POS',


    /* messages DIAN */
    'poscustom_settings_dian' => 'Settings DIAN',
    'poscustom_enabled_dian' => 'Enabled DIAN',
    'poscustom_dian_enviroment' => 'DIAN Enviroment to operate',
    /*Form Select */
    'poscustom_env_live' => 'Live Enviroment',
    'poscustom_env_test' => 'Testing Enviroment',
    'poscustom_env_disabled' => 'Disabled',    
    'poscustom_enviroment' =>'Allowed to enable/diasable the production/testing enviroment',
    /*Parameter in business settings for eInvoice (DIAN)*/
    'poscustom_settings_live' => 'Live Settings',
    'poscustom_settings_test' => 'Test Settings',
    'poscustom_settings_common' => 'Common Settings',
    'poscustom_settings_common_customer' => 'Common Settings for Customer',
    'poscustom_settings_common_supplier' => 'Common Settings for Supplier',
    
    'parameter' => 'Parameter (Tag XML)',
    'para_value' => 'Value',
    'InvoiceAuthorization' => 'InvoiceAuthorization',
    'InvoiceAuthorization_help_text' => 'Authorization from regulation for eInvoice',
    'StartDate' => 'StartDate',
    'EndDate' => 'EndDate',
    'Prefix' => 'Prefix',
    'eInvoiceFrom' => 'eInvoiceFrom',
    'eInvoiceTo' => 'eInvoiceTo',
    'ClTec' => 'ClTec',
    'ClTec_help_text' => 'En produccion se debe utilizar SOAP y el metodo GetNumberingRange Se necesita el NIT y la clave del software',
    /*Common settings */
    'DocumentCurrencyCode' => 'DocumentCurrencyCode',
    'companyCity' => 'companyCity',
    'companyDpto' => 'companyDpto',
    'codeDpto' => 'codeDpto',
    'InvoiceTypeCode' => 'InvoiceTypeCode',
    'PaymentMeansID_cash' => 'Cash',
    'PaymentMeansID_credit' => 'Credit',    
    'PaymentMeansID' => 'PaymentMeansID',
    'PaymentMeansCode_cash' => 'PaymentMeansCode Efectivo',
    'PaymentMeansCode_others' => 'PaymentMeansCode Nequi, Daviplata...',
    'CustomerDocType_individual' => 'CustomerDocType individual',
    'CustomerDocType_business' => 'CustomerDocType business',
    'DianNIT' => 'DianNIT',
    /* -----Software------- */
    'poscustom_settings_Software' => 'Software Settings',
    'NameSoftware' => 'NameSoftware',
    'SoftwareID' => 'SoftwareID',
    'Pin' => 'Pin',
    'CertP12' => 'CertP12',
    'keyCertP12' => 'keyCertP12',

    /* -----Supplier------- */
    'poscustom_AdditionalAccountID' => 'AdditionalAccountID',
    'AdditionalAccountID_help_text' => 'Supplier type',
    'poscustom_juridica' => 'Persona Jurídica', 
    'poscustom_natural' => 'Persona Natural',
    'CompanyNIT' => 'CompanyNIT', 
    'companyAddress' => 'companyAddress',
    'IndustryClassificationCode' => 'IndustryClassificationCode',
    'CompanyPostCode' => 'CompanyPostCode',
    'TaxLevelCode' => 'TaxLevelCode',
    'TaxShemeSupplierID' => 'TaxShemeSupplierID',
    'TaxShemeSupplierName' => 'TaxShemeSupplierName',
    /* -----Customer------- */    
    'TaxShemeCustomerID' => 'TaxShemeCustomerID',
    'TaxShemeCustomerName' => 'TaxShemeCustomerName',




];
