class FormValidator {
    constructor(form, fields, errorMessageElement) {
        this.form = form;
        this.fields = fields;
        this.errorMessageElement = errorMessageElement;
        this.form.addEventListener('submit', event => this.handleSubmit(event));
    }

    handleSubmit(event) {
        event.preventDefault();
        this.clearErrors();
        const errors = this.validateFields();

        if (errors.length > 0) {
            this.showErrors(errors);
            return;
        }

        this.onSubmitSuccess();
    }

    validateFields() {
        const errors = [];
        this.fields.forEach(field => {
            const value = field.element.value.trim();
            if (!value) {
                errors.push(field.messageEmpty);
                field.element.classList.add('input-error');
                return;
            }

            if (field.type === 'email' && !this.isValidEmail(value)) {
                errors.push(field.messageEmail);
                field.element.classList.add('input-error');
            }

            if (field.type === 'password' && field.minLength && value.length < field.minLength) {
                errors.push(field.messagePasswordLength);
                field.element.classList.add('input-error');
            }

            if (field.matches) {
                const matchedValue = document.getElementById(field.matches).value.trim();
                if (value !== matchedValue) {
                    errors.push(field.messageMatch);
                    field.element.classList.add('input-error');
                    document.getElementById(field.matches).classList.add('input-error');
                }
            }
        });
        return errors;
    }

    clearErrors() {
        this.errorMessageElement.textContent = '';
        this.fields.forEach(field => field.element.classList.remove('input-error'));
    }

    showErrors(errors) {
        this.errorMessageElement.textContent = errors.join('. ');
    }

    isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    onSubmitSuccess() {
        const page = this.form.id === 'signup-form' ? 'quiz.html' : 'quiz.html';
        window.location.href = page;
    }
}

window.addEventListener('DOMContentLoaded', () => {
    const signupForm = document.getElementById('signup-form');
    const loginForm = document.getElementById('login-form');
    const errorElement = document.getElementById('error_message');

    if (signupForm) {
        new FormValidator(signupForm, [
            {
                element: document.getElementById('username_id'),
                messageEmpty: 'Lietotājvārda lauks ir obligāts.'
            },
            {
                element: document.getElementById('email_input'),
                type: 'email',
                messageEmpty: 'E-pasta lauks ir obligāts.',
                messageEmail: 'Lūdzu ievadi derīgu e-pasta adresi.'
            },
            {
                element: document.getElementById('password_input'),
                type: 'password',
                minLength: 8,
                messageEmpty: 'Paroles lauks ir obligāts.',
                messagePasswordLength: 'Parolei jābūt vismaz 8 simbolus garai.'
            },
            {
                element: document.getElementById('repeat_password_input'),
                type: 'password',
                matches: 'password_input',
                messageEmpty: 'Atkārtotas paroles lauks ir obligāts.',
                messageMatch: 'Paroles nesakrīt.'
            }
        ], errorElement);
    }

    if (loginForm) {
        new FormValidator(loginForm, [
            {
                element: document.getElementById('login_username'),
                messageEmpty: 'Lietotājvārda lauks ir obligāts.'
            },
            {
                element: document.getElementById('login_password'),
                type: 'password',
                minLength: 8,
                messageEmpty: 'Paroles lauks ir obligāts.',
                messagePasswordLength: 'Parolei jābūt vismaz 8 simbolus garai.'
            }
        ], errorElement);
    }
});
