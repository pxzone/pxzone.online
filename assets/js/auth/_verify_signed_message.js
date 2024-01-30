$("#_verify_message_btn").on('click', () => {
    address = $("#_address").val();
    message = $("#_message").val();
    sig = $("#_signature").val();

    console.log(verify(address,sig,message));
})
function verify(address,sig,message){
    sig=ecdsa.parseSigCompact(sig);
    var pubKey=new ECPubKey(ecdsa.recoverPubKey(sig.r,sig.s,magicHash(message),sig.i));
    var isCompressed=!!(sig.i&4);pubKey.compressed=isCompressed;
    address=new Address(address);
    return pubKey.getAddress(address.version).toString()===address.toString()
}
function verify(e) {
    e.preventDefault();
    var a = t.state,
        n = a.message,
        i = a.address,
        r = a.signature,
        s = !1;
    (s = t.simpleVerification(n, i, r)) || (s = t.fallbackVerification(n, i, r)), t.setState({
      result: s ? "Valid signature" : "Invalid signature"
    })
}