<?php
/**
 * Plugin Name: WooCommerce Tax Manager
 * Description: ระบบจัดการภาษีมูลค่าเพิ่ม ทั้งแบบราคารวมแล้ว และบวกเพิ่มในตะกร้าสินค้า
 * Author: Jirakit Pawnsakunrungrot
 * Author URI: https://www.linkedin.com/in/sunny-jirakit
 * Plugin URI: https://github.com/sunny420x/woocommerce-tax-manager
 */

function tax_manager_menu()
{
    add_menu_page(
        'ระบบจัดการภาษีมูลค่าเพิ่ม',    // Page title
        'จัดการภาษีมูลค่าเพิ่ม',                          // Menu title
        'edit_posts',                        // Capability required
        'tax_manager',                             // Menu slug
        'tax_manager_page',            // Callback function to display page content
        'dashicons-dashboard',                 // Icon URL or Dashicon class
        80                                       // Position in the menu (optional)
    );
}

function tax_manager_page() {
?>
<style>
.white-label-zone {
    width: calc(100% + 20px);
    height: auto;
    background: #fff;
    display: flex;
    margin: 0 0 0 -20px;
}
.white-label-zone h1,p {
    padding: 0 20px;
}
.container {
    background: #fff; 
    width: 1200px;
}
.container h1 {
    display: block;
    font-size: 18px;
    padding: 13px 20px;
    margin: 0 0 20px 0;
    background: #555;
    color: #fff;
}
.container p {
    padding: 10px 0;
    margin: 0;
}
a.menu-btn {
    width: 100%; 
    padding: 5px !important; 
    font-size: 14px !important;
}
@media print {
    .no-print {
        display: none !important;
    }
}
</style>
<div class="white-label-zone no-print">
    <span style="padding: 60px 10px 60px 40px;float: left;font-size: 60px;">🏷️</span>
    <div style="padding: 20px 0;">
        <h1>WordPress Onsite Coupon Manager</h1>
        <p>ระบบสร้างแคมเปญพิเศษ จัดการคูปองหน้าร้านสำหรับแคมเปญ
        <br>
        <strong>Github Repository:</strong> <a href="https://github.com/sunny420x/wordpress-onsite-coupon-tracker" target="_blank">https://github.com/sunny420x/wordpress-onsite-coupon-tracker</a>
        </p>
    </div>
</div>
<div class="wrapper" style="display: flex;">
    <div class="container">
        <div style="display: flex;">
            <div style="width: 100%; margin: 0 0 0 10px;">
                <div style="padding: 0px 25px 25px 25px;">
                    <form action="options.php" method="post">
                        <?php
                        settings_fields('tax_manager_settings_group');
                        ?>
                        <label for="tax_percent">ร้อยละจำนวนภาษีมูลค่าเพิ่ม</label>: 
                        <input type="number" name="tax_percent" id="tax_percent" value="<?=get_option("tax_percent");?>"> %

                        <label for="tax_option">รูปแบบการคำนวณภาษีมูลค่าเพิ่ม:</label>
                        <select name="tax_option" id="tax_option">
                            <option value="addition_tax" <?php selected(get_option("tax_option"), 'addition_tax'); ?>>บวกเพิ่มจากราคาสินค้า</option>
                            <option value="in_price" <?php selected(get_option("tax_option"), 'in_price'); ?>>รวมในราคาสินค้าแล้ว</option>
                        </select>
                        <button type="submit" class="button">บันทึกการเปลี่ยนแปลง</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
}

add_action('admin_init', 'tax_manager_settings_init');

function tax_manager_settings_init()
{
    register_setting('tax_manager_settings_group', 'tax_option');
    register_setting('tax_manager_settings_group', 'tax_percent');
}

add_action('admin_menu', 'tax_manager_menu');

if(get_option("tax_option") == "addition_tax") {
    /**
     * เพิ่มค่าธรรมเนียมจากยอดรวมตะกร้าใน WooCommerce
     */
    add_action( 'woocommerce_cart_calculate_fees', 'add_custom_percentage_fee', 20, 1 );

    function add_custom_percentage_fee( $cart ) {
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) return;

        // คำนวณยอดรวมของสินค้าในตะกร้า
        $subtotal = $cart->subtotal;
        
        // คำนวณ 7%
        $fee_amount = $subtotal * ((float) get_option("tax_percent") / 100);

        // เพิ่มค่าธรรมเนียมเข้าไปในตะกร้า
        // พารามิเตอร์: ชื่อค่าธรรมเนียม, จำนวนเงิน, ต้องเสียภาษีเพิ่มไหม (false)
        $cart->add_fee( __( 'ค่าธรรมเนียม '.get_option("tax_percent").'%', 'woocommerce' ), $fee_amount, false );
    }
}

if(get_option("tax_option") == "in_price") {
    /**
     * แสดงราคาก่อนหักภาษี (Pre-tax price) ในหน้าตะกร้า
     */
    add_filter( 'woocommerce_cart_item_name', 'display_pre_tax_price_in_cart', 10, 3 );

    function display_pre_tax_price_in_cart( $product_name, $cart_item, $cart_item_key ) {
        if ( is_cart() || is_checkout() ) {
            $price_with_tax = $cart_item['data']->get_price();
            
            $price_before_tax = $price_with_tax / (((float) get_option("tax_percent") / 100) + 1);
            
            $formatted_price = wc_price( $price_before_tax );
            
            $product_name .= '<br><small style="color: #666; font-weight: normal;">' . 
                            __( 'ราคาก่อนบวกภาษีมูลค่าเพิ่ม: ', 'woocommerce' ) . $formatted_price . 
                            '</small>';
        }
        return $product_name;
    }

    /**
     * แสดงราคาก่อนหักภาษี (รวมจำนวนสินค้า) ในหน้าตะกร้า
     */
    add_filter( 'woocommerce_cart_item_subtotal', 'display_pre_tax_price_right_side', 10, 3 );

    function display_pre_tax_price_right_side( $subtotal, $cart_item, $cart_item_key ) {
        // ตรวจสอบหน้าและเงื่อนไข
        if ( ! ( is_cart() || is_checkout() ) ) return $subtotal;
        if ( get_option("tax_option") !== 'in_price' ) return $subtotal;

        // ดึงราคาต่อหน่วย และ จำนวนสินค้า
        $price_with_tax = $cart_item['data']->get_price();
        $quantity       = $cart_item['quantity'];
        
        // คำนวณราคารวม (ราคาสินค้า * จำนวน) แล้วค่อยนำมาถอดภาษี
        $total_price_with_tax = $price_with_tax * $quantity;
        $tax_percent          = (float) get_option("tax_percent", 7);
        
        $total_price_before_tax = $total_price_with_tax / (($tax_percent / 100) + 1);
        
        // แสดงผล
        $subtotal .= '<br><small style="color: #666; font-size: 0.85em; display: block; white-space: nowrap;">' . 
                    'ก่อนบวกภาษี: ' . wc_price($total_price_before_tax) . 
                    '</small>';

        return $subtotal;
    }

    add_action( 'woocommerce_cart_totals_before_order_total', 'display_cart_totals_before_tax' );

    function display_cart_totals_before_tax() {
        // ตรวจสอบ Option (ต้องเป็นโหมดรวมภาษีแล้ว)
        if ( get_option("tax_option") !== 'in_price' ) return;

        $cart = WC()->cart;
        $tax_percent = (float) get_option("tax_percent", 7);
        $total_before_tax = 0;

        // ลูปผ่านสินค้าทุกชิ้นในตะกร้าเพื่อถอดภาษีทีละรายการ
        foreach ( $cart->get_cart() as $cart_item ) {
            $price_with_tax = $cart_item['data']->get_price();
            $quantity       = $cart_item['quantity'];
            
            // คำนวณราคาต่อชิ้นถอดภาษี แล้วคูณจำนวน
            $price_unit_before_tax = $price_with_tax / (($tax_percent / 100) + 1);
            $total_before_tax     += ($price_unit_before_tax * $quantity);
        }

        ?>
        <tr class="cart-subtotal-before-tax">
            <div class="fee">
                <th><?php _e('ยอดรวมก่อนบวกภาษีมูลค่าเพิ่ม ('.$tax_percent.'%)', 'woocommerce' ); ?></th>
                <td data-title="<?php esc_attr_e( 'ยอดรวมก่อนภาษี', 'woocommerce' ); ?>">
                    <span class="woocommerce-Price-amount amount">
                        <?php echo wc_price( $total_before_tax ); ?>
                    </span>
                </td>
            </div>
        </tr>
        <?php
    }
}
?>