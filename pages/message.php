<?php
    global $wpdb;
    $user_table = $wpdb->prefix . "egosms_user";
    $message_table = $wpdb->prefix . "egosms_messages";

    /*============== Sending message to recipient ====================*/
    if (isset($_POST['sendmessage']))
    {
        
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

<div class="wrap form-container">
    <h1 class="form-header">EGOSMS MESSAGING PLUGIN</h1>
    <hr>
    <!-- This section is for sending messages to a recipient -->
    <div class="content-body">
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
</div>