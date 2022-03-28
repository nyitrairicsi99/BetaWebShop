function generatePassword() {
    let str = '';
    let chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
    for(let i = 0; i < 16;i++) {
        str += chars[(Math.random() * 60) | 0]
    } 
    document.getElementById("password").value = str;
    document.getElementById("password2").value = str;
    document.getElementById("generatedpassword").innerHTML = str;
}