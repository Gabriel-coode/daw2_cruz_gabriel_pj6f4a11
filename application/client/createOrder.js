const form = document.querySelector("#orderForm");
const result = document.querySelector("#result");
const submitButton = document.querySelector("#submitButton");

// Enable quantity inputs for selected products
const productCheckboxes = document.querySelectorAll('input[name="product"]');

productCheckboxes.forEach((checkbox) => {
    checkbox.addEventListener("change", () => {

        const quantityInput = checkbox.closest("div").querySelector(".quantity");
        quantityInput.disabled = !checkbox.checked;

    });
});

// Send the order to PHP
form.addEventListener("submit", async (event) => {

    event.preventDefault();

    result.textContent = "";

    const selectedProducts = document.querySelectorAll('input[name="product"]:checked');

    if (selectedProducts.length === 0) {
        result.textContent = "Select at least one product.";
        return;
    }

    const formData = new FormData(form);

    // Remove the flat checkbox values
    formData.delete("product");

    // Build the products array for PHP
    selectedProducts.forEach((checkbox, index) => {

        const quantityInput = checkbox.closest("div").querySelector(".quantity");

        formData.append(`products[${index}][id]`, checkbox.value);

        formData.append(`products[${index}][quantity]`, quantityInput.value);

    });

    submitButton.disabled = true;

    try {
        const response = await fetch(
            "../server/createOrder.php",
            {
                method: "POST",
                body: formData
            }
        );

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error || "Cannot save order.");
        }

        result.textContent = `Order saved. Total with VAT: €${Number(data.totalWithVat).toFixed(2)}`;

        form.reset();

        document.querySelectorAll(".quantity").forEach((input) => {
            input.disabled = true;
        });

    } catch (error) {
        result.textContent = `Error: ${error.message}`;

    } finally {
        submitButton.disabled = false;
    }

});

