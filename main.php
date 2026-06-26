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
    font-size: 16px;
    padding: 10px 20px;
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
.leftside {
    width: 350px;
    background: #f8f8f8;
    height: max-content;
}
.leftside h1 {
    background: #009FE3;
    color: #fff;
    font-size: 16px;
    padding: 10px 20px;
    margin: 0;
}
.leftside a {
    padding: 10px 20px;
    font-size: 14px;
    background: #f8f8f8;
    color: #000;
    transition: .2s ease-in-out;
    display: block;
    width: 100%;
    text-decoration: none;
}
.leftside a:hover {
    background: #fff;
    cursor: pointer;
}
@media print {
    .no-print {
        display: none !important;
    }
}
</style>
<div class="white-label-zone no-print">
    <span style="padding: 60px 10px 60px 40px;float: left;font-size: 60px;">🧾</span>
    <div style="padding: 20px 0;">
        <h1>WordPress Tax & Fee Manager</h1>
        <p>
        จัดการภาษีมูลค่าเพิ่ม และค่าธรรมเนียมต่าง ๆ<br>
        <strong>Github Repository:</strong> <a href="https://github.com/sunny420x/woocommerce-tax-manager" target="_blank">https://github.com/sunny420x/woocommerce-tax-manager</a>
        </p>
    </div>
</div>
<div class="wrapper" style="display: flex;">
    <div style="display: flex;">
        <div class="leftside">
            <h1>WordPress Tax & Fee Manager</h1>
            <a href="/wp-admin/admin.php?page=tax_manager">🔧 ตั้งค่า</a>
        </div>
        <div class="container">
            <h1>จัดการภาษีมูลค่าเพิ่ม และค่าธรรมเนียมต่าง ๆ</h1>
            <div style="padding: 0px 25px 25px 25px;">
                <form action="options.php" method="post">
                    <?php
                    settings_fields('tax_manager_settings_group');
                    ?>

                    <h2>ภาษีมูลค่าเพิ่มทั่วไป</h2>

                    <label for="tax_percent">ร้อยละจำนวนภาษีมูลค่าเพิ่ม</label>: 
                    <input type="number" name="tax_percent" id="tax_percent" value="<?=get_option("tax_percent");?>"> %
                    <br><br>

                    <label for="tax_option">รูปแบบการคำนวณภาษีมูลค่าเพิ่ม:</label>
                    <select name="tax_option" id="tax_option">
                        <option value="addition_tax" <?php selected(get_option("tax_option"), 'addition_tax'); ?>>บวกเพิ่มจากราคาสินค้า</option>
                        <option value="in_price" <?php selected(get_option("tax_option"), 'in_price'); ?>>รวมในราคาสินค้าแล้ว</option>
                    </select>
                    <br><br>

                    <label for="tax_print_logo">ที่อยู่ URL รูปภาพ Logo (ใช้แสดงในหน้าออกใบเสร็จ)</label>: 
                    <input type="text" name="tax_print_logo" id="tax_print_logo" value="<?=get_option("tax_print_logo");?>" style="width: 100%">

                    <h2>ค่าธรรมเนียมบัตรเครดิต</h2>
                    <input type="checkbox" value="yes" name="credit_card_fee_enable" <?php if(get_option("credit_card_fee_enable") == "yes") { echo "checked"; } ?> > บวกค่าธรรมเนียมบัตรเครดิต 3% 
                    <br><br>
                    
                    <label for="tax_address">ที่อยู่ในใบเสร็จ</label>
                    <input type="text" name="tax_address" id="tax_address" value="<?=get_option("tax_address");?>" style="width: 100%">
                    <br><br>

                    <label for="tax_phone_number">โทรศัพท์</label>
                    <input type="text" name="tax_phone_number" id="tax_phone_number" value="<?=get_option("tax_phone_number");?>" style="width: 100%">
                    <br><br>

                    <button type="submit" class="button">บันทึกการเปลี่ยนแปลง</button>
                </form>
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
    register_setting('tax_manager_settings_group', 'credit_card_fee_enable');
    register_setting('tax_manager_settings_group', 'tax_print_logo');
    register_setting('tax_manager_settings_group', 'tax_address' );
    register_setting('tax_manager_settings_group', 'tax_phone_number');
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

if(get_option("credit_card_fee_enable") == "yes") {
    /**
     * บวกค่าธรรมเนียม 3% เมื่อเลือกชำระเงินผ่านบัตรเครดิต
     */
    add_action('woocommerce_cart_calculate_fees', 'add_credit_card_surcharge', 25);
    
    function add_credit_card_surcharge($cart) {
        if (is_admin() && !defined('DOING_AJAX')) return;
    
        $chosen_payment_method = WC()->session->get('chosen_payment_method');
    
        $target_methods = array('kasikorn_kpgw'); 
    
        if (in_array($chosen_payment_method, $target_methods)) {
            
            $percentage = 0.03; // 3%
            
            // คำนวณจากยอดรวมสินค้า (Subtotal) หรือจะใช้ Total ก็ได้แล้วแต่ตกลงกับเจ้านายครับ
            $surcharge = $cart->get_subtotal() * $percentage;
    
            // บวกค่าธรรมเนียมเข้าไปในตะกร้า
            $cart->add_fee(__('ค่าธรรมเนียมชำระผ่านบัตรเครดิต (3%)', 'woocommerce'), $surcharge);
        }
    }

    add_action( 'woocommerce_order_details_after_order_table', 'add_custom_payment_fee_button', 10, 1 );

    function add_custom_payment_fee_button( $order ) {
        // 1. กำหนดชื่อค่าธรรมเนียมที่ต้องการตรวจสอบ (ต้องตรงกับที่ตั้งไว้ในระบบ)
        $target_fee_name = 'ค่าธรรมเนียมชำระผ่านบัตรเครดิต (3%)';
        $has_fee = false;

        // 2. วนลูปเช็ครายการค่าธรรมเนียมในคำสั่งซื้อ
        foreach ( $order->get_fees() as $fee ) {
            if ( $fee->get_name() === $target_fee_name ) {
                $has_fee = true;
                break;
            }
        }

        // 3. ถ้าพบค่าธรรมเนียม ให้แสดงปุ่ม
        if ( $has_fee ) {
        ?>
            <div class="custom-action-wrapper" style="margin: 20px 0;">
                <a href="/my-account/print-tax/<?=$order->get_id();?>" class="button button-primary" id="tax-print-btn">ออกใบเสร็จเฉพาะค่าธรรมเนียม</a>
            </div>
        <?php
        }
    }

    add_action( 'init', 'custom_register_print_tax_endpoint' );
    function custom_register_print_tax_endpoint() {
        add_rewrite_endpoint( 'print-tax', EP_ROOT | EP_PAGES );
    }

    add_filter( 'query_vars', 'custom_print_tax_query_vars', 0 );
    function custom_print_tax_query_vars( $vars ) {
        $vars[] = 'print-tax';
        return $vars;
    }

    add_action( 'template_redirect', 'custom_isolated_print_tax_page' );
    function custom_isolated_print_tax_page() {
        $order_id = get_query_var( 'print-tax' );
        
        if ( ! empty( $order_id ) ) {
            
            $order = wc_get_order( $order_id );
            if ( ! $order ) {
                wp_die( 'ไม่พบข้อมูลคำสั่งซื้อในระบบ', 'Error', array( 'response' => 404 ) );
            }

            $target_fee = null; 
            foreach ( $order->get_fees() as $fee ) {
                if ( $fee->get_name() === "ค่าธรรมเนียมชำระผ่านบัตรเครดิต (3%)" ) {
                    $target_fee = $fee;
                    break;
                }
            }

            $fee_amount = $target_fee->get_total(); 
            $formatted_price = round($fee_amount) . " บาท"; 
            ?>
            <!DOCTYPE html>
            <html lang="th">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>ใบเสร็จเฉพาะค่าธรรมเนียม - Order #<?php echo esc_html( $order_id ); ?></title>
                <style>
                    body { 
                        background: #ffffff; 
                        margin: 0;
                        padding: 40px; 
                        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                        color: #333;
                    }
                    .print-container { 
                        max-width: 650px; 
                        margin: 0 auto; 
                        border: 1px solid #e1e1e1; 
                        padding: 30px; 
                        border-radius: 8px;
                        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
                    }
                    #logo_watermark {
                        display: none;
                    }
                    h2 { margin-top: 0; color: #111; border-bottom: 1.5px solid #333; padding-bottom: 10px; }
                    .meta-info { margin-bottom: 20px; line-height: 1.6; font-size: 14px; color: #555; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th { background: #f9f9f9; text-align: left; padding: 12px; border-bottom: 1.5px solid #ddd; font-weight: bold; }
                    td { padding: 15px 12px; border-bottom: 1px solid #eee; }
                    td:last-child, th:last-child { text-align: right; }
                    .price-amount { font-size: 18px; font-weight: bold; color: #000; }
                    .no-print-btn {
                        display: inline-block;
                        margin-top: 30px;
                        padding: 12px 25px; 
                        background: #0073aa; 
                        color: #fff; 
                        border: none; 
                        border-radius: 4px;
                        cursor: pointer;
                        font-size: 15px;
                        text-decoration: none;
                    }
                    /* สไตล์สั่งซ่อนสิ่งที่ไม่ต้องการตอนพ่นออกทางเครื่องปริ้นท์ */
                    @media print {
                        body { padding: 0; }
                        .print-container { border: none; box-shadow: none; max-width: 100%; padding: 0; }
                        .no-print-btn { display: none; }
                        #logo_watermark {
                            display: block;
                        }
                    }
                </style>
            </head>
            <body>

                <div class="print-container">
                    <img src="<?=get_option("tax_print_logo")?>" width="50%" id="logo_watermark" alt="logo_watermark" style="position: absolute; opacity: 0.1; top: 35%; left: 25%;">
                    <div style="display: flex;">
                        <div class="col">
                            <img src="<?=get_option("tax_print_logo")?>" width="150" alt="logo">
                        </div>
                        <div style="text-align: right; width: 100%;">
                            <p style="line-height: 30px;">
                                (ต้นฉบับ / Original)<br>
                                <?=get_option("tax_address")?><br>
                                โทรศัพท์: <?=get_option("tax_phone_number")?>
                            </p>
                        </div>
                    </div>
                    <br>
                    <h2>ใบเสร็จรับเงิน (เฉพาะค่าธรรมเนียม)</h2>
                    
                    <div class="meta-info">
                        <strong>เลขที่ใบสั่งซื้อ:</strong> #<?php echo esc_html( $order_id ); ?><br>
                        <strong>วันที่ออกเอกสาร:</strong> <?php echo date_i18n( get_option( 'date_format' ), strtotime( $order->get_date_created() ) ); ?> 
                        <p>เอกสารนี้มีผลตั้งแต่วันที่ออกเอกสารภายใน 7 วัน นับตั้งแต่วันที่ออกเอกสาร<br>
                            This document valid until 7 days after <strong>(<?php echo explode("T", $order->get_date_created())[0]; ?> until <?php
                            $date_created = $order->get_date_created();
                            if ( $date_created ) {
                                $new_date = clone $date_created; 
                                $new_date->modify( '+6 days' );
                                echo $new_date->date( 'Y-m-d' ); 
                            }
                        ?>)</strong></p> 
                    </div>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>รายการ</th>
                                <th>จำนวนเงิน</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>ค่าธรรมเนียมชำระผ่านบัตรเครดิต (3%)</td>
                                <td class="price-amount"><?php echo $formatted_price; ?></td>
                            </tr>
                            <tr>
                                <td><strong>จำนวนเงินรวมทั้งสิ้น (บาท) / Grand Total</strong></td>
                                <td><strong><?php echo $formatted_price; ?></strong><br></td>
                            </tr>
                            <tr>
                                <th>จำนวนเงินรวมทั้งสิ้น</th>
                                <th>
                                <?php
                                $formatter = new NumberFormatter('th_TH', NumberFormatter::SPELLOUT);
                                $thaiWords = $formatter->format(round($fee_amount));
                                echo $thaiWords . 'บาทถ้วน'; 
                                ?>
                                </th>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div style="text-align: center;">
                        <button class="no-print-btn" onclick="window.print();">สั่งพิมพ์ใบเสร็จนี้</button>
                    </div>
                </div>

                <script>
                    // สั่ง Print อัตโนมัติเมื่อหน้าเว็บพร้อม
                    window.onload = function() {
                        window.print();
                    };
                </script>
            </body>
            </html>
            <?php
            exit;
        }
    }
}
?>