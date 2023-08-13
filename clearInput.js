document.addEventListener("DOMContentLoaded", function () {
    // Clear file input on page load
    const fileInput = document.querySelector("input[type='file']");
    fileInput.value = null;
    this.location.reload(true);
});