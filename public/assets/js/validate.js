document.addEventListener("submit", function (e) {

    let form = e.target;

    let requiredFields = form.querySelectorAll(
        ".required, input[required], select[required], textarea[required]"
    );

    if (!requiredFields.length) return;

    let isValid = true;

    // Remove old error messages first
    form.querySelectorAll(".error-msg").forEach(function (msg) {
        msg.remove();
    });

    requiredFields.forEach(function (field) {

        if (field.disabled) return;

        let value = (field.value || "").trim();

        if (value === "") {
            isValid = false;

            // Red border
            field.style.border = "1px solid red";

            // Create error message
            let error = document.createElement("small");
            error.className = "error-msg";
            error.style.color = "red";
            error.innerText = "This field is empty";

            // Insert error AFTER input
            field.parentNode.appendChild(error);

        } else {
            field.style.border = "";
        }
    });

    if (!isValid) {
        e.preventDefault();
    }
});