
const operationsContent = document.querySelector(
    "#operationsContent"
);

const sessionMessage = document.querySelector(
    "#sessionMessage"
);

const welcomeMessage = document.querySelector(
    "#welcomeMessage"
);

const operationsForm = document.querySelector(
    "#operationsForm"
);

const logoutForm = document.querySelector(
    "#logoutForm"
);

// Check authentication when the page loads
async function checkSession() {

    try {
        const response = await fetch(
            "../server/sessionStatus.php",
            {
                method: "GET",
                credentials: "same-origin",
                cache: "no-store"
            }
        );

        if (response.status === 401) {
            window.location.replace("login.html");
            return;
        }

        if (!response.ok) {
            throw new Error("Cannot verify session");
        }

        const data = await response.json();

        if (!data.authenticated) {
            window.location.replace("login.html");
            return;
        }

        welcomeMessage.textContent =
            `Welcome, ${data.username}`;

        sessionMessage.textContent = "";
        operationsContent.hidden = false;

    } catch (error) {
        sessionMessage.textContent =
            `Error: ${error.message}`;
    }
}

// Access the selected operation
operationsForm.addEventListener("submit", (event) => {

    event.preventDefault();

    const selectedOperation = document.querySelector(
        'input[name="operation"]:checked'
    ).value;

    if (selectedOperation === "create") {
        window.location.href = "createOrder.html";
    } else {
        window.location.href = "viewOrders.html";
    }
});

// End the session
logoutForm.addEventListener("submit", async (event) => {

    event.preventDefault();

    try {
        const response = await fetch(
            "../server/logout.php",
            {
                method: "POST",
                credentials: "same-origin"
            }
        );

        if (!response.ok) {
            throw new Error("Cannot log out");
        }

        window.location.replace("index.html");

    } catch (error) {
        sessionMessage.textContent =
            `Error: ${error.message}`;
    }
});

checkSession();
