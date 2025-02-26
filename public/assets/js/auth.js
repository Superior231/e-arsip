function showPass() {
    const passwordInput = document.getElementById("password");
    const passwordType = passwordInput.type;

    if (passwordType === "password") {
        passwordInput.type = "text";
        document.getElementById("showPass").innerHTML = '<i class="fa-regular fa-eye"></i>';
    } else {
        passwordInput.type = "password";
        document.getElementById("showPass").innerHTML = '<i class="fa-regular fa-eye-slash"></i>';
    }
}