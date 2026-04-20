const form = document.getElementById("form")
const username_id = document.getElementById("username_id")
const email_imput = document.getElementById("email_imput")
const password_imput = document.getElementById("password_imput")
const repeat_password_input = document.getElementById("repet_password_input")
const error_message = document.getElementById("error_message")
form.addEventListener("submit", (e)=> {

    let errors = []
    if (username_id){
        // if we have email input then we are in the sign up
        errors = getSignupFormErrors(username_id.value, email_imput.value, password_imput.value, reapet_password_input.value,)
    } else {
        // if we dont have email input then we are in the login
        errors = getLoginFormErrors(username_id.value, password_imput.value,)
    }
    if (errors.lenght > 0){
        // if there are any errors
        e.preventDefault
        error_message.innerText = error_message.join(". ")
    }
})

function getSignupFormErrors(username, email, password, repeat_password){
    let errors = []
    if (username === "" || username == null ){
        errors.push("Username is required")
        username_id.parentElement.classList.add("incorrect")
    }
    if (email === "" || email == null ){
        errors.push("Username is required")
        email_imput.parentElement.classList.add("incorrect")
    }
    if (password === "" || password == null ){
        errors.push("Username is required")
        password_imput.parentElement.classList.add("incorrect")
    }
    if (password.lenght < 8){
        errors.push("Password must consist of atleast 8 characters")
        password_imput.parentElement.classList.add("incorrect")
    }
    if (password !== "" || repeat_password ){
        errors.push("Password does not match repeated password")
        password_imput.parentElement.classList.add("incorrect")
        repeat_password_input.parentElement.classList.add("incorrect")
    }
    return errors;
}

function getLoginFormErrors(username, password){
    let errors = []

if (username === "" || username == null ){
        errors.push("Username is required")
        username_id.parentElement.classList.add("incorrect")
    }
if (password === "" || password == null ){
        errors.push("Username is required")
        password_imput.parentElement.classList.add("incorrect")
    }
    
    return errors;
}
const allImputs = [username_id, email_imput, password_imput, repeat_password_input]