
const loginForm = document.querySelector("#loginForm");
const loginMessage = document.querySelector("#loginMessage");
const loginButton = document.querySelector("#loginButton");

loginForm.addEventListener("submit", async (event) => {

    event.preventDefault();

    loginMessage.textContent = "";

    // recoge el usuario y la contraseña del formulario
    const formData = new FormData(loginForm);

    loginButton.disabled = true;

    try {

        // fetch envía el usuario y la contraseña mediante POST a login.php
        const response = await fetch(
            "../server/login.php",
            {
                method: "POST",
                body: formData,
                credentials: "same-origin"
            }
        );

        const data = await response.json();

        /* si el envío hecho por fetch mediante POST a login.php falla por alguna razón se
        envía un mensaje de error al usuario */
        if (!response.ok) {
            throw new Error(
                data.error || "Authentication failed"
            );
        }

        window.location.href = "operations.html";

    } catch (error) {

        const message = error.message;

        loginMessage.textContent = message;

        // Inform the user and return to Home
        alert(message);

        window.location.href = "index.html";

    } finally {

        loginButton.disabled = false;
    }
});
