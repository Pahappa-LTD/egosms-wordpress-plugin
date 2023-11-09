
<?php
    global $wpdb;
    $template_table = $wpdb->prefix . "egosms_template";
    $custom_template = $_GET['template'];

    /*============== Save template messages to the database ====================*/
    if (isset($_POST['submittemplate']))
    {
      
        $new_custom = $_POST['new_custom'];

        $data = [ 'custom_message' => $new_custom ];
        $where = [ 'order_status' => $custom_template ];

        $wpdb->update( $template_table, $data, $where );

        echo "
            <div class='success-message'>
                Template Saved Successfully
            </div>
        ";
    }
    /*============== End section for saving template messages to the database ====================*/

    // importing styles.css
    wp_enqueue_style('style', plugin_dir_url(__FILE__) .'../assets/css/style.css');

?>

<div class="wrap form-container">
    <h1 class="form-header">EGOSMS MESSAGING PLUGIN</h1>
    <hr>
    <div class="content-body">
        <h3>Enter Custom Message</h3>
        <table class="ttable">
            <th class="tdtable">Placeholder</th>
            <th class="tdtable">Meaning</th>
            <tbody>
                <tr>
                    <td class="tdtable">[last_name]</td>
                    <td class="tdtable">Customer's last name stored while checking out.</td>
                </tr>
                <tr>
                    <td class="tdtable">[order_number]</td>
                    <td class="tdtable">Order Number generated when the order was made.</td>
                </tr>
            </tbody>
        </table><br>
        <?php 
            $result = $wpdb->get_results("SELECT id, order_status, custom_message FROM $template_table WHERE order_status = '$custom_template' ");
            foreach ($result as $print) {
                echo '
                    <p>To override the default text message, edit this template. </p>
                    <form method="post">
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row" style="text-transform:capitalize">'.$print->order_status.'<br /><span style="font-size: x-small;">Order Status</span></th>
                                <td>
                                    <textarea type="text" rows="6" name="new_custom" placeholder="Enter Custom Message." class="regular-text regular-textarea">'.$print->custom_message.'</textarea>
                                </td>
                            </tr>           
                        </table><br>
                        <input type="submit" class="button-primary" name="submittemplate" value="Save Changes" />
                    </form><br>
                ';
            };
        ?>
    </div>
</div>
