<div class="wrap form-container">
    <h1 class="form-header">EGOSMS MESSAGING PLUGIN</h1>
    <hr>

    <!-- This section is for displaying message history -->
    <div class="content-body">
        <h2>Sent Messages</h2>
        <table class="wp-list-table widefat striped">
            <thead>
            <tr>
                <th width="20%" style="font-weight: 500">Date/Time</th>
                <th width="20%" style="font-weight: 500">Recipient</th>
                <th width="20%" style="font-weight: 500">Message</th>
                <th width="20%" style="font-weight: 500">Status</th>
            </tr>
            </thead>
            <tbody id="refreshDivContent">
                <?php
                    global $wpdb;
                    $message_table = $wpdb->prefix . "egosms_messages";
                    $result = $wpdb->get_results("SELECT * FROM $message_table ORDER BY id DESC");
                    foreach ($result as $print) {
                        $status = $print->message_status == 1 ? '<span style="color:green"><i>Sent</i></span>' : '<span style="color:red"><i>Failed</i></span>';
                        echo "
                            <tr>
                                <td width='20%'>$print->send_date</td>
                                <td width='20%'>$print->recipient</td>
                                <td width='20%'>$print->message</td>
                                <td width='20%'>$status</td>
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