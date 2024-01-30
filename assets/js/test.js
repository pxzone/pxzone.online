var bbc_box_message = $("#bbcBox_message");
if(bbc_box_message){
    $("#bbcBox_message div:nth-child(2)").append('<img onclick="insertBalanceChecker()" src="https://pxzone.online/assets/images/other/converter.png" align="bottom" width="23" height="22" alt="Crypto Balance Checker" title="Crypto Balance Checker" style="cursor: pointer; background-image: url(&quot;https://pxzone.online/assets/images/other/converter.png&quot;);">')
}
function insertBalanceChecker(){
    bbcode = "[img]https://pxzone.online/api/crypto/balance-checker?address=3PeEJ899Ugfqz2vb7PwaKvGFVz9Xc8P4BR&coin=bitcoin&currency=usd[/img]";
    $("#message").val($("#message").val() + bbcode)
}

// {
//     "manifest_version": 2,
//     "name": " Crypto Wallet Address Balance Checker",
//     "version": "1.0",
//     "description": "Check the balance of crypto wallet address",
//     "icons": {
//       "48": "icon.png"
//     },
//     "permissions": ["<specific_permissions>"],
//     "browser_action": {
//       "default_icon": "icon.png",
//       "default_popup": ""
//     },
//     "content_scripts": [
//       {
//         "matches": ["<all_urls>"],
//         "js": ["content.js"]
//       }
//     ]
//   }
  