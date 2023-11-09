<div class="wrap form-container">
    <h1 class="form-header">EGOSMS MESSAGING PLUGIN</h1>
    <hr>

    <!-- This section is for displaying message history -->
    <div class="content-body">
        <h3>Set Custom Messages</h3>
        <p>To override the default text message, edit these templates. </p>
        <table class="wp-list-table widefat striped">
            <thead>
            <tr>
                <th width="50%" style="font-weight: 500">Order Status</th>
                <th width="50%" style="font-weight: 500;">Manage</th>
            </tr>
            </thead>
            <tbody id="refreshDivContent">
                <?php
                    global $wpdb;
                    $template_table = $wpdb->prefix . "egosms_template";
                    $result = $wpdb->get_results("SELECT * FROM $template_table ");
                    foreach ($result as $print) {
                       echo "
                            <tr>
                                <td width='50%'>$print->order_status</td>                            
                                <td width='50%'><a href='" .home_url(). '/wp-admin/admin.php?page=template&template='.$print->order_status. "'>Manage</a></td>
                            </tr>
                        ";
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php
    // importing styles.css
    wp_enqueue_style('style', plugin_dir_url(__FILE__) .'../assets/css/style.css');
?>