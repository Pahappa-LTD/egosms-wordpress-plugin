<div class="wrap form-container">
    <h1 class="form-header">EGOSMS MESSAGING PLUGIN</h1>
    <hr>

    <div class="tab">
        <button class="tablinks" onclick="openTab(event, 'send')" id="defaultOpen">Message</button>
        <!-- <button class="tablinks" onclick="openTab(event, 'balance')">Balance</button> -->
        <button class="tablinks" onclick="openTab(event, 'settings')">Settings</button>
        <button class="tablinks" onclick="openTab(event, 'history')">History</button>
    </div>

    <div id="settings" class="tabcontent">
        <h3>Enter EgoSMS Acount Details</h3>
        <form method="post" action="">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Account Username<br /><span style="font-size: x-small;">Available after creating egosms account</span></th>
                    <td>
                        <input size="50" type="text" name="username" placeholder="Enter Account Username" class="regular-text" required/>
                        <br />
                        <small>To create an account, visit <a href="https://www.egosms.co/" target="_blank">https://www.egosms.co/</a></small>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Password<br /><span style="font-size: x-small;">Your egosms account password</span></th>
                    <td>
                        <input size="50" type="password" name="password" placeholder="Enter Account Password" class="regular-text" required/>
                        <br />
                        <small>To create an account, visit <a href="https://www.egosms.co/" target="_blank">https://www.egosms.co/</a></small>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Sender ID<br /><span style="font-size: x-small;">EgoSMS SenderID.</span></th>
                    <td>
                        <input size="50" type="text" name="sender_id" placeholder="Enter Sender ID" class="regular-text" required/>
                        <br />
                        <small>This is your sender ID</small>
                    </td>
                </tr>
                    <tr valign="top">
                    <th scope="row">Message<br /><span style="font-size: x-small;">Enter Custom Message.</span></th>
                    <td>
                        <textarea size="50" type="text" name="message" placeholder="Enter Message" class="regular-text" style="resize:none" required></textarea>
                        <br />
                        <small>Message will be added to order No.</small>
                    </td>
                </tr>
            </table><br>
            <input type="submit" class="button-primary" name="submitaccount" value="Save Changes" />
        </form><br>
    </div>

    <div id="balance" class="tabcontent">
        <h3>EgoSMS Account Balance</h3>

    </div>

    <div id="send" class="tabcontent">
        <h3>Enter Message to Send</h3>
        <form method="post" action="" >
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Receipient Phone<br /><span style="font-size: x-small;">Recipient's Phone Number</span></th>
                    <td>
                        <input size="50" type="text" name="recipient" placeholder="Enter Phone Number" class="regular-text" required/>
                        <br />
                        <small>Enter Phone Number (256#########)</small>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Type Message<br /><span style="font-size: x-small;">Type Your Message</span></th>
                    <td>
                        <textarea size="50" type="text" name="message" placeholder="Enter Your Message" class="regular-text" style="resize:none" required></textarea>
                        <br />
                        <small>Standard Character Limit</small>
                    </td>
                </tr>
            </table><br>
            <input type="submit" class="button-primary" name="sendmessage" value="Send Message" />
        </form><br>
    </div>

    <div class="tabcontent" id='history'>
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

<script>
          
    // JS for handling tab behaviour
    function openTab(evt, menuItem)
    {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }

        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++){
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }

        document.getElementById(menuItem).style.display = "block";
        evt.currentTarget.className += " active";
    }

    // Get the element with id="defaultOpen" and click on it
    document.getElementById("defaultOpen").click();

</script>

<?php
    global $wpdb;
    $user_table = $wpdb->prefix . "egosms_user";
    $message_table = $wpdb->prefix . "egosms_messages";
    // Get account user details from table
    $result = $wpdb->get_row ( "SELECT id, username, password, sender_id, message FROM $user_table " ); 

    /*============== Enter egosms user details into the database ====================*/
    if (isset($_POST['submitaccount']))
    {
        $user_username = $_POST['username'];
        $user_password = $_POST['password'];
        $user_sender_id = $_POST['sender_id'];
        $user_message = $_POST['message'];

        $password = $user_password;

        // Check if result is true
        if($result)
        {
            // update existing user account
            $current_username = $result->username;
            $current_password = $result->password;
            $current_sender_id = $result->sender_id;
            $current_message = $result->message;
     
            $wpdb->query( $wpdb->prepare("UPDATE $user_table
            SET username = %s, password = %s, sender_id = %s, message = %s 
            WHERE username = %s AND password = %s AND sender_id = %s AND message = %s  ",
            $user_username, $password, $user_sender_id, $user_message, $current_username, $current_password, $current_sender_id, $current_message )
        );

        }else{
            // insert user account details into the table
            $wpdb->query("INSERT INTO $user_table(username, password, sender_id, message) VALUES('$user_username', '$user_password', '$user_sender_id', '$user_message')");
        };

        echo "
            <div class='success-message'>
                Details Saved Successfully
            </div>
        ";
    }
    /*============== End section for entering user details into the database ====================*/

    /*============== Sending message to recipient ====================*/
    if (isset($_POST['sendmessage']))
    {
        global $wpdb;
        $user_table = $wpdb->prefix . "egosms_user";
        $message_table = $wpdb->prefix . "egosms_messages";
        
        // Required parameters for EgoSMS
        $phone_number = $_POST['recipient'];
        $message = $_POST['message'];

        // If number starting with 0 is submitted, convert the first number to 256
        if($phone_number[0] == '0'){
            $sent_phone = substr_replace(substr($phone_number, 1), '256', 0, 0);
        }else{
            $sent_phone = $phone_number;
        }

        $number = $sent_phone;

        $result = $wpdb->get_row ( "SELECT username, password, sender_id FROM $user_table " ); 

        if($result){
            $username = $result->username;
            $password = $result->password;
            $sender = $result->sender_id;
    
            require_once plugin_dir_path( __FILE__ ) . '../includes/API.php';
    
            if(SendSMS($username, $password, $sender, $number, $message) == 'OK')
            {
                $message_status = 1;
                $wpdb->query("INSERT INTO $message_table(recipient, message, message_status) VALUES('$number', '$message', '$message_status')");
                echo "
                    <div class='success-message'>
                        Message Sent Successfully
                    </div>
                ";
    
            }else{
                $message_status = 0;
                $wpdb->query("INSERT INTO $message_table(recipient, message, message_status) VALUES('$number', '$message', '$message_status')");
                echo "
                    <div class='failure-message'>
                        Message Not Sent
                    </div>
                ";
               
            }
        }else{
            echo "
                <div class='failure-message'>
                    Setup EgoSMS Account First.
                </div>
            ";
        }
        
    }
    /*============== End Sending message to recipient ====================*/

    // importing styles.css
    wp_enqueue_style('style', plugin_dir_url(__FILE__) .'../assets/css/style.css');

?>