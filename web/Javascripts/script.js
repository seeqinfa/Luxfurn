const wrapper = document.querySelector('.wrapper');
const loginLink = document.querySelector('.login-link');
const registerLink = document.querySelector('.register-link');
const btnPopup = document.querySelector('.btnLogin');
const iconClose=document.querySelector('.icon-close');

registerLink.addEventListener('click',()=>{
	wrapper.classList.add('active');
});

loginLink.addEventListener('click',()=>{
	wrapper.classList.remove('active');
});

btnPopup.addEventListener('click',()=>{
	wrapper.classList.add('active-popup');
});

iconClose.addEventListener('click',()=>{
	wrapper.classList.remove('active-popup');
});



    function validateRegistrationForm() {
        var username = document.getElementById('username').value;
        var email = document.getElementById('email').value;
        var password = document.getElementById('password').value;
        var userProfileType = document.getElementById('userProfileType').value;
        var termsChecked = document.getElementById('terms').checked;

        // Validate username (non-empty)
        if (username.trim() === "") {
            alert("Please enter a username.");
            return false;
        }

        // Validate email format
        var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        if (!emailPattern.test(email)) {
            alert("Please enter a valid email address.");
            return false;
        }

        // Validate password strength (at least 8 characters, one uppercase, one number, one special character)
        var passwordPattern = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/;
        if (!passwordPattern.test(password)) {
            alert("Password must be at least 8 characters long, contain one uppercase letter, one lowercase letter, one number, and one special character.");
            return false;
        }

        // Check if a user profile is selected
        if (userProfileType === "") {
            alert("Please select a role.");
            return false;
        }

        // Check if terms and conditions are agreed to
        if (!termsChecked) {
            alert("You must agree to the terms and conditions.");
            return false;
        }

        // If all validations pass, return true
        return true;
    }
	
	




