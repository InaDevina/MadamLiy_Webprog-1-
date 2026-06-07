document.addEventListener("DOMContentLoaded", () => {

    const loginBtn = document.querySelector(".login-btn");

    loginBtn.addEventListener("click", () => {

        const username =
            document.getElementById("username").value.trim();

        const password =
            document.getElementById("password").value.trim();

        if (
            (username === "admin" && password === "admin123") ||
            (username === "cashier" && password === "cashier123")
        ) {

            window.location.href = "transactions.html";

        } else {

            alert("Username atau password salah!");

        }

    });

});
