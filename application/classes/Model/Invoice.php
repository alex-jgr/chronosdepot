<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Description of Invoice
 *
 * @author admin
 */
class Model_Invoice extends ORM
{
    protected $_table_name      = 'invoices';
    protected $_table_columns   = array(
        'id'                => array('type' => 'int'),
        'project_id'        => array('type' => 'int'),
        'template_id'       => array('type' => 'int'),
        'series'            => array('type' => 'string'),
        'number'            => array('type' => 'int'),
        'logo_image_id'     => array('type' => 'int'),
        
        'supplier_id'       => array('type' => 'int'),
        'supplier_name'     => array('type' => 'string'),
        'supplier_misc'     => array('type' => 'string'),
        'supplier_rc_no'    => array('type' => 'string'),
        'supplier_vat_no'   => array('type' => 'string'),
        'supplier_address_1'=> array('type' => 'string'),
        'supplier_address_2'=> array('type' => 'string'),
        'supplier_phone'    => array('type' => 'string'),
        'supplier_bank'     => array('type' => 'string'),
        'supplier_iban'     => array('type' => 'string'),
        'supplier_city'     => array('type' => 'string'),
        'supplier_postcode' => array('type' => 'string'),
        'supplier_country'  => array('type' => 'string'),
        
        'customer_id'       => array('type' => 'int'),
        'customer_name'     => array('type' => 'string'),
        'customer_misc'     => array('type' => 'string'),
        'customer_rc_no'    => array('type' => 'string'),
        'customer_vat_no'   => array('type' => 'string'),
        'customer_address_1'=> array('type' => 'string'),
        'customer_address_2'=> array('type' => 'string'),
        'customer_phone'    => array('type' => 'string'),
        'customer_bank'     => array('type' => 'string'),
        'customer_iban'     => array('type' => 'string'),
        'customer_city'     => array('type' => 'string'),
        'customer_postcode' => array('type' => 'string'),
        'customer_country'  => array('type' => 'string'),
        
        'discount'          => array('type' => 'decimal'),
        
        'sub_total_1'       => array('type' => 'decimal'),
        'sub_total_2'       => array('type' => 'decimal'),
        
        'total_1'           => array('type' => 'decimal'),
        'total_2'           => array('type' => 'decimal'),
        
        'currency_1'        => array('type' => 'int'),
        'currency_2'        => array('type' => 'int'),
        
        'created'           => array('type' => 'int'),
        'due_time'          => array('type' => 'int'),
        
        'payment_link'      => array('type' => 'string'),
        'status'            => array('type' => 'string')
    );
    
    protected $_belongs_to = array(
        'supplier'  => array('model' => 'User',             'foreign_key' => 'supplier_id'),
        'customer'  => array('model' => 'User',             'foreign_key' => 'customer_id'),
        'template'  => array('model' => 'Invoice_Template', 'foreign_key' => 'template_id'),
        'currency1' => array('model' => 'Currency',         'foreign_key' => 'currency_1'),
        'currency2' => array('model' => 'Currency',         'foreign_key' => 'currency_2'),
        'logo_image'=> array('model' => 'Image',            'foreign_key' => 'logo_image_id')
    );
    
    protected $_has_many = array(
        'lines' => array('model' => 'Invoice_Line', 'foreign_key' => 'invoice_id')
    );
    
    public function generate() {
        
    }
}
