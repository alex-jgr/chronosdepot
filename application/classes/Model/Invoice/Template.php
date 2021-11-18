<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Description of Template
 *
 * @author admin
 */
class Model_Invoice_Template extends ORM
{
    protected $_table_name      = 'invoice_templates';
    protected $_table_columns   = array(
        'id'                => array('type' => 'int'),        
        'name'              => array('type' => 'string'),
        'series'            => array('type' => 'string'),
        'number'            => array('type' => 'int'),
        
        'supplier_id'       => array('type' => 'int'),
        'supplier_name'     => array('type' => 'string'),
        'supplier_misc'     => array('type' => 'string'),
        'supplier_rc_no'    => array('type' => 'string'),
        'supplier_vat_no'   => array('type' => 'string'),
        'supplier_address_1'=> array('type' => 'string'),
        'supplier_address_1'=> array('type' => 'string'),
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
      
        'currency_1'        => array('type' => 'int'),
        'currency_2'        => array('type' => 'int'),
        
        'due_time'          => array('type' => 'int'),
    );
    
    public static function get_user_templates($user_id, $with_json)
    {
        $templates = DB::select()
                ->from('invoice_templates')
                ->where('supplier_id', '=', intval($user_id))
                ->execute()->as_array();
        
        if ($with_json) {
            foreach ($templates as $key => $template) {
                $templates[$key]['json'] = json_encode($template);
            }
        }
        
        return $templates;
    }
}
