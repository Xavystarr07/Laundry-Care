// Register ScrollTrigger
gsap.registerPlugin(ScrollTrigger);

// 1. Reveal animations for all sections
const reveals = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-bottom');

reveals.forEach((el) => {
    let x = 0;
    let y = 0;
    
    // Determine direction based on class
    if(el.classList.contains('reveal-left')) x = -100;
    if(el.classList.contains('reveal-right')) x = 100;
    if(el.classList.contains('reveal-bottom')) y = 50;

    gsap.from(el, {
        x: x,
        y: y,
        opacity: 0,
        duration: 1.2,
        ease: "power3.out",
        scrollTrigger: {
            trigger: el,
            start: "top 85%", // Starts animation when element is 85% from top
            toggleActions: "play none none none"
        }
    });
});

// 2. Form Submission with Dynamic Logic
const contactForm = document.getElementById('laundryForm');
const responseDiv = document.getElementById('formResponse');

contactForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const method = document.getElementById('contact_method').value;
    const phone = "27711346294"; // Formatted for WhatsApp
    const name = formData.get('name');

    // Show a loading state
    responseDiv.innerHTML = "Processing your request...";

    // Handle interactive redirection based on choice
    if(method === 'whatsapp') {
        const text = `Hi Laundry Care! My name is ${name}. I'd like to enquire about a pickup.`;
        window.open(`https://wa.me/${phone}?text=${encodeURIComponent(text)}`, '_blank');
        responseDiv.innerHTML = "<p class='success'>Opening WhatsApp...</p>";
    } else if (method === 'call') {
        window.location.href = "tel:0711346294";
        responseDiv.innerHTML = "<p class='success'>Dialing 0711346294...</p>";
    }

    // Always send the data to PHP for the automatic email backup
    fetch('php/contact.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            if(method === 'email') {
                responseDiv.innerHTML = `<p class='success'>${data.message}</p>`;
            }
            contactForm.reset();
        } else {
            responseDiv.innerHTML = `<p class='error'>Oops! ${data.message}</p>`;
        }
    })
    .catch(err => {
        responseDiv.innerHTML = "<p class='error'>Service temporarily unavailable. Please try WhatsApp!</p>";
    });
});