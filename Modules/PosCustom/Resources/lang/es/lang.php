<?php
return [
    'categories' => 'Categorias',
    'subcategory' => 'SubCategoría',
    'category' => 'Categoría',
    'categories_allowed' => 'Categorias permitidas por usuario',
    'titulo_PosCustom' => 'Custom POS',
    'enabled' => 'Activo',
    'buttonpos' => 'Enable POS button',
    'stock_pos' => 'Stock',
    'next' => 'Próximo',
	'previous' => 'Anterior',
    'PosCustom_module' =>'Module PosCustom',
    'pos_display' => 'Second Display',
    'product' => 'Producto',
    'pos_display_caption' => 'Gracias por comprar con nosotros!',
    'no_items' => 'Vacio',
    'enable_units' => 'Habilitar unidades POS',
    'poscustom_settings' => 'Configuración PosCustom',
    'poscustom_width_totals' => 'POS ancho izquierdo',
    'poscustom_width_totals_ttip' => 'Para calcular el ancho izquierdo y derecho, simplemente introduzca el ancho del lado izquierdo. El ancho del lado derecho será 100 - ancho_izquierdo',
    'poscustom_secondscreen_show' => 'Mostrar segunda pantalla',
    'poscustom_btncate_show' => 'Mostrar botón categorias',
    'poscustom_bar_show' => 'Mostrar barra categorias',
    'poscustom_show_units' => 'Mostrar unidades',
    'poscustom_show_imgproduct' => 'Mostrar IMG para productos en totales',    
    'show_advertisement_s1' => 'Advertisement style 1',
    'show_advertisement_s2' => 'Advertisement style 2',
    'poscustom_width_badge_show' => 'Ancho badge stock',
    'poscustom_width_product' => 'Ancho del producto (Beta)',
    'poscustom_width_product_help' => 'Ancho de cada producto (pixels) para calcular la cantidad de productos que se pueden mostrar en el lado derecho del POS',
    'poscustom_default_category' => 'Categoría default',
    'poscustom_default_category_help' => 'Se usa para mostrar la categoría predeterminada en el POS',

    /**PosCustom Styles */
    'poscustom_styles' => 'PosCustom Styles',

    /** Position of products on POS */
    'plrstyle0' => 'Productos a la derecha',
    'plrstyle1' => 'Productos a la izquierda',
    'poscustom_ppositionlr_ttip' =>'Define la posición de la ventana de productos, a la izquierda o a la derecha en el punto de venta. Cambia el lado izquierdo si es necesario.',    
    'poscustom_ppositionlr_lang' =>'Posición para productos (POS)',      


    /** Style for segment totals button on totals or fixed bottom */
    'totalstyle0' => 'botones acción abajo (fijo)',
    'totalstyle1' => 'botones accion izquierda',
    'poscustom_totalstyle_ttip' =>'Permite habilitar el estilo para los totales en el POS.',    
    'poscustom_totalstyle_lang' =>'estilos para  totales (POS)', 

    /* Styles pos_form_action*/
    'bstyle0' => 'Botones acción default',
    'bstyle1' => 'Botones hover color',
    'bstyle1t' => 'Boton tipo TAB color degrade',
    'bstyle2' => 'Botones color degrade',
    'bstyle2t' => 'Botones tipo TAB color degrade',    
    'poscustom_btnstyle_ttip' =>'Permite habilitar los estilos para los botones de accion en el POS.',    
    'poscustom_btnstyle_lang' =>'Estilos para botones acción POS',


    /* Styles pos_form_action right side*/

    'catestyle0' => 'Categorias arriba',
    'catestyle1' => 'Categorias izquierda',
    'poscustom_catestyle_ttip' =>'Permite habilitar los estilos de la parte derecha para el POS',    
    'poscustom_catestyle_lang' =>'Posición categoria acceso rápido',

    
    /* Left Size POS*/
    'poscustom_left_size' => 'Tamaño ventana izquierda POS',    
    
    /* messages DIAN */
    'poscustom_settings_dian' => 'Configuración DIAN',
    'poscustom_enable_dian' => 'Habilitar DIAN',
    'poscustom_dian_enviroment' => 'DIAN Ambiente para operar',
    /*Form Select */
    'poscustom_env_live' => 'Ambiente productivo',
    'poscustom_env_test' => 'Ambiente Pruebas',
    'poscustom_env_disabled' => 'Deshabilitado',
    'poscustom_enviroment' =>'Permite habilitar los ambientes de produción ó pruebas ó deshabilitar el servicio de facturación electrónica',
    /*Parameter in business settings for eInvoice (DIAN)*/
    'poscustom_settings_live' => 'Configuración producción',
    'poscustom_settings_test' => 'Configuración pruebas',
    'poscustom_settings_common' => 'Configuración Común',
    'poscustom_settings_common_customer' => 'Configuración Común Cliente',
    'poscustom_settings_common_supplier' => 'Configuración Común Proveedor',
    
    'parameter' => 'Parametro (Tag XML)',
    'para_value' => 'Valor',
    'InvoiceAuthorization' => 'InvoiceAuthorization',
    'InvoiceAuthorization_help_text' => 'Autorización de la DIAN para factura electrónica',
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
    'PaymentMeansID_cash' => 'Contado',
    'PaymentMeansID_credit' => 'Crédito',
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
    'AdditionalAccountID_help_text' => 'Tipo de contribuyente',
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
