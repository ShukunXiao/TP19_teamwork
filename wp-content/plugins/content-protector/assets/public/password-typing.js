function showPassword() {
    var input = document.getElementsByClassName("passster-password");
    var input = input[0];

    if (input.type === "password") {
        input.type = "text";
    } else {
        input.type = "password";
    }
} 