document.addEventListener("DOMContentLoaded", () => {

  /* ==========================================================
     ELEMENTS
     ========================================================== */

  const backTop = document.getElementById("backTop");
  const form = document.getElementById("quoteForm");
  const message = document.getElementById("formMessage");


  /* ==========================================================
     CURRENT YEAR
     ========================================================== */

  const year = document.getElementById("year");

  if (year) {
    year.textContent = new Date().getFullYear();
  }


  /* ==========================================================
     BACK TO TOP
     Optimized scroll event
     ========================================================== */

  if (backTop) {

    let ticking = false;

    const updateBackTop = () => {

      backTop.classList.toggle(
        "show",
        window.scrollY > 500
      );

      ticking = false;
    };


    window.addEventListener(
      "scroll",
      () => {

        if (!ticking) {

          window.requestAnimationFrame(
            updateBackTop
          );

          ticking = true;
        }

      },
      {
        passive: true
      }
    );


    backTop.addEventListener(
      "click",
      () => {

        window.scrollTo({
          top: 0,
          behavior: "smooth"
        });

      }
    );

  }


  /* ==========================================================
     CONTACT / ENQUIRY FORM
     send-enquiry.php
     ========================================================== */

  if (form && message) {

    form.addEventListener(
      "submit",
      async (event) => {

        event.preventDefault();


        /* ------------------------------------------------------
           SUBMIT BUTTON
           ------------------------------------------------------ */

        const submitBtn =
          form.querySelector(
            'button[type="submit"]'
          );


        const originalBtnText =
          submitBtn
            ? submitBtn.textContent
            : "";


        /* ------------------------------------------------------
           MESSAGE
           ------------------------------------------------------ */

        message.classList.remove(
          "is-error",
          "success",
          "error",
          "loading"
        );


        message.classList.add(
          "show",
          "loading"
        );


        message.textContent =
          "Sending your enquiry...";


        /* ------------------------------------------------------
           DISABLE BUTTON
           ------------------------------------------------------ */

        if (submitBtn) {

          submitBtn.disabled = true;

          submitBtn.setAttribute(
            "aria-busy",
            "true"
          );

          submitBtn.textContent =
            "Sending...";

        }


        try {

          /* ----------------------------------------------------
             FORM DATA
             ---------------------------------------------------- */

          const formData =
            new FormData(form);


          /* ----------------------------------------------------
             SEND REQUEST
             ---------------------------------------------------- */

          const response =
            await fetch(
              "send-enquiry.php",
              {
                method: "POST",
                body: formData,

                headers: {
                  "X-Requested-With":
                    "XMLHttpRequest"
                }
              }
            );


          /* ----------------------------------------------------
             SERVER RESPONSE
             ---------------------------------------------------- */

          let result;


          try {

            result =
              await response.json();

          } catch (parseError) {

            throw new Error(
              "Unexpected server response."
            );

          }


          /* ----------------------------------------------------
             REMOVE LOADING STATE
             ---------------------------------------------------- */

          message.classList.remove(
            "loading",
            "error",
            "is-error"
          );


          /* ----------------------------------------------------
             SUCCESS
             ---------------------------------------------------- */

          if (
            response.ok &&
            result.success
          ) {

            message.classList.add(
              "success"
            );


            message.textContent =
              result.message ||
              "Thank you. Your enquiry has been received.";


            /* Reset form */

            form.reset();

          }


          /* ----------------------------------------------------
             SERVER ERROR
             ---------------------------------------------------- */

          else {

            message.classList.add(
              "error",
              "is-error"
            );


            message.textContent =
              result.message ||
              "Something went wrong. Please try again.";

          }


        }


        /* ------------------------------------------------------
           NETWORK / JAVASCRIPT ERROR
           ------------------------------------------------------ */

        catch (error) {

          message.classList.remove(
            "loading",
            "success"
          );


          message.classList.add(
            "error",
            "is-error"
          );


          message.textContent =
            error.message ||
            "Unable to send your enquiry. Please try again.";


          console.error(
            "Enquiry form error:",
            error
          );

        }


        /* ------------------------------------------------------
           RESTORE SUBMIT BUTTON
           ------------------------------------------------------ */

        finally {

          if (submitBtn) {

            submitBtn.disabled = false;

            submitBtn.removeAttribute(
              "aria-busy"
            );

            submitBtn.textContent =
              originalBtnText;

          }

        }

      }
    );

  }

});