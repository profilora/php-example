<?php
// Replace with Your API Client Key
$clientKey = 'xxxxxx';
// Replace with Your API Client Secret
$clientSecret = 'yyyyyy';
// compiled client with base64
$clientToken = base64_encode($clientKey . ':' . $clientSecret);

function callAPI($token, $method, $url, $data){
   $curl = curl_init();
   
   switch ($method){
      case "POST":
         curl_setopt($curl, CURLOPT_POST, 1);
         if ($data)
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
         break;
      case "PUT":
         curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
         if ($data)
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
         break;
      default:
         if ($data)
            $url = sprintf("%s?%s", $url, http_build_query($data));
   }

   // OPTIONS:
   curl_setopt($curl, CURLOPT_URL, $url);
   curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      'Authorization: Basic ' . $token,
      'Content-Type: application/json',
   ));
   curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

   $result = curl_exec($curl);

   if(!$result){die("Connection Failure");}

   curl_close($curl);

   return $result;
};

$url = 'https://api.profilora.com/whatsapp/send-message';

// this must be matched with the number on the registered agent
$senderNo = '9100000001@s.whatsapp.net';
?>

<html>
   <body>
      <div style="width:400px;margin:0 auto;padding:10px;">
         <form style="display:flex;flex-direction:column" method="post">
            <label style="margin-bottom:20px">
               <span style="display:block;margin-bottom:10px;font-weight:bold">Phone Number</span>
               <input type="text" name="phone" placeholder="+178377..." />
            </label>
            <label style="margin-bottom:20px">
               <span style="display:block;margin-bottom:10px;font-weight:bold">Message</span>
               <textarea style="width:100%;min-height:200px" name="message"></textarea>
            </label>
            <button type="submit">Send</button>
         </form>
      </div>
      <div style="width:400px;margin:20px auto 0 auto;padding:10px;">
         <?php
            
            if ($_POST && ($_POST["phone"] && $_POST["message"])) {
               $data_array = array(
                  'sender' => $senderNo,
                  'to' => str_replace('+', '', $_POST["phone"]) . '@s.whatsapp.net',
                  'message' => $_POST["message"]
               );
               
               $make_call = callAPI($clientToken, 'POST',  $url, json_encode($data_array));
               $response = json_decode($make_call, true);
               echo "<strong>Server Response</strong><br>";
               echo "<strong>eventId:</strong> " . $response['eventId'] . '<br>';
               echo "<strong>message:</strong> " . $response['message'];   
            }
         ?>
      </div>
   </body>
</html>