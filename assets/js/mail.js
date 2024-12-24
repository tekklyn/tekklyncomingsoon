function success(message) {
  Toastify({
    text: message,
    duration: 3000,
    close: true,
    gravity: "top",
    positionLeft: false,
    backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
    style: {
      fontSize: "13px",
    },
  }).showToast();
}

function error(message) {
  Toastify({
    text: message,
    duration: 5000,
    close: true,
    gravity: "top",
    positionLeft: false,
    backgroundColor: "#ff4d4d",
    style: {
      fontSize: "13px",
    },
  }).showToast();
}

function subscribe() {
  const submitButton = document.getElementById("subscription-form-submit");
  submitButton.classList.add("disabled-button");

  const emailInput = document.getElementById("emailNews");
  const email = emailInput.value.trim();

  // Validate email (required and valid email format)
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    error("Please enter a valid email address.");
    submitButton.classList.remove("disabled-button");
    return;
  }

  // Email content structure
  const tableData = `
      <div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
        <h2 style="color: #4CAF50; font-size: 18px;">New Subscription</h2>
        <p><strong>Email:</strong> <a href="mailto:${email}" style="color: #2C99D6;">${email}</a></p>
        <br />
        <p style="font-size: 12px; color: #888;">
          This message was sent from the subscription form of the website <a href="https://tekklyn.com" target="_blank">Tekklyn</a>.
        </p>
      </div>
    `;

  // Send email through the backend
  fetch("https://tekklyn.com/mail/checkmail.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      reciever_full_name: "Tekklyn Newsletter",
      reciever_email: "subhamjena0001@gmail.com",
      subject: `New Subscription from`,
      cc: "subhamjena0001@gmail.com",
      msg: tableData,
    }),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.json();
    })
    .then((data) => {
      if (data.status === "success") {
        success("Subscription successful! Thank you for subscribing.");
        emailInput.value = "";
      } else {
        error("Subscription failed. Please try again later.");
      }
    })
    .catch((err) => {
      console.error(err);
      error("Failed to send mail.");
    })
    .finally(() => {
      submitButton.classList.remove("disabled-button");
    });
}



function sendMail() {
  const submitButton = document.getElementById("cfsubmit");
  submitButton.classList.add("disabled-button");

  const name = document.getElementById("name").value.trim();
  const email = document.getElementById("email").value.trim();
  const message = document.getElementById("message").value.trim();

  // Validate name (required and minimum length of 2 characters)
  if (name === "" || name.length < 2) {
    error("Please enter a valid name.");
    submitButton.classList.remove("disabled-button");
    return;
  }

  // Validate email (required and valid email format)
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    error("Please enter a valid email address.");
    submitButton.classList.remove("disabled-button");
    return;
  }

  // Validate message (required)
  if (message === "") {
    error("Please enter a message.");
    submitButton.classList.remove("disabled-button");
    return;
  }

  const tableData = `
          <div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
              <h2 style="color: #4CAF50; font-size: 18px;">New Contact Form Submission</h2>
              <p><strong>Name:</strong> ${name}</p>
              <p><strong>Email:</strong> <a href="mailto:${email}" style="color: #2C99D6;">${email}</a></p>
              <p><strong>Message:</strong></p>
              <div style="border-left: 4px solid #4CAF50; padding-left: 10px; margin-top: 5px;">
                  <p>${message}</p>
              </div>
              <br />
              <p style="font-size: 12px; color: #888;">This message was sent from the contact form of the website <a href="https://tekklyn.com" target="_blank">Tekklyn</a>.</p>
          </div>
      `;

  fetch("https://tekklyn.com/mail/checkmail.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      reciever_full_name: "Tekklyn Contact Form",
      reciever_email: "subhamjena0001@gmail.com",
      subject: `New Contact Form Submission from ${name}`,
      cc: "subhamjena0001@gmail.com",
      msg: tableData,
    }),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.json();
    })
    .then((data) => {
      if (data.status === "success") {
        success(data.message);
        document.getElementById("contactForm").reset(); // Reset the form
      } else {
        error(data.message);
      }
    })
    .catch((err) => {
      console.error(err);
      error("Failed to send mail.");
    })
    .finally(() => {
      submitButton.classList.remove("disabled-button");
    });
}
