<?php
    global $wpdb;
    $user_table = $wpdb->prefix . "egosms_user";

    /*============== Enter egosms user details into the database ====================*/
    if (isset($_POST['submitaccount']))
    {

        $user_username = $_POST['username'];
        $user_password = $_POST['password'];
        $user_sender_id = $_POST['sender_id'];

        $password = $user_password;

        // Get account user details from table
        $result = $wpdb->get_row ( "SELECT id, username, password, sender_id FROM $user_table " ); 

        // Check if result is true
        if($result)
        {
            // update existing user account
            $current_username = $result->username;
            $current_password = $result->password;
            $current_sender_id = $result->sender_id;
     
            $wpdb->query( $wpdb->prepare("UPDATE $user_table
            SET username = %s, password = %s, sender_id = %s 
            WHERE username = %s AND password = %s AND sender_id = %s ",
            $user_username, $password, $user_sender_id, $current_username, $current_password, $current_sender_id )
        );

        }else{
            // insert user account details into the table
            $wpdb->query("INSERT INTO $user_table(username, password, sender_id) VALUES('$user_username', '$user_password', '$user_sender_id')");
        };

        echo "
            <div class='success-message'>
                Details Saved Successfully
            </div>
        ";
    }
    /*============== End section for entering user details into the database ====================*/

    // importing styles.css
    wp_enqueue_style('style', plugin_dir_url(__FILE__) .'../assets/css/style.css');

?>

<div class="wrap form-container">
    <h1 class="form-header">EGOSMS MESSAGING PLUGIN</h1>
    <hr>

    <!-- This section is for setting up the EgoSMS account details -->
    <div id="settings" class="content-body">
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
              
            </table><br>
            <input type="submit" class="button-primary" name="submitaccount" value="Save Changes" />
        </form><br>
    </div>
</div>