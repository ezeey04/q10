document.addEventListener("DOMContentLoaded", () => {

  const backTop = document.getElementById("backTop");
  const form = document.getElementById("quoteForm");
  const message = document.getElementById("formMessage");

  /* Current year */
  document.getElementById("year").textContent = new Date().getFullYear();

  /* Back to top */
  window.addEventListener("scroll", () => {
    backTop.classList.toggle("show", window.scrollY > 500);
  });

  backTop.addEventListener("click", () => {
    window.scrollTo({ top: 0, behavior: "smooth" });
  });

  /* Contact form -> send-enquiry.php */
  form.addEventListener("submit", async (event) => {

    event.preventDefault();

    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn ? submitBtn.textContent : "";

    message.classList.remove("is-error");
    message.textContent = "Sending your enquiry...";

    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.textContent = "Sending...";
    }

    try {

      const formData = new FormData(form);

      const response = await fetch("send-enquiry.php", {
        method: "POST",
        body: formData
      });

      let result;

      try {
        result = await response.json();
      } catch (parseError) {
        throw new Error("Unexpected server response.");
      }

      if (response.ok && result.success) {
        message.classList.remove("is-error");
        message.textContent = result.message || "Thank you. Your enquiry has been received.";
        form.reset();
      } else {
        message.classList.add("is-error");
        message.textContent = result.message || "Something went wrong. Please try again.";
      }

    } catch (error) {
      message.classList.add("is-error");
      message.textContent = "Could not send your enquiry. Please check your connection and try again, or contact us by phone or email.";
    } finally {
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.textContent = originalBtnText;
      }
    }

  });

});