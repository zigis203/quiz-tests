class Quiz {
    constructor(questions, questionElement, answerButtonsElement, nextButton, progressFill, progressLabel) {
        this.questions = questions;
        this.questionElement = questionElement;
        this.answerButtonsElement = answerButtonsElement;
        this.nextButton = nextButton;
        this.progressFill = progressFill;
        this.progressLabel = progressLabel;
        this.currentQuestionIndex = 0;
        this.selectedAnswers = {};
        this.nextButton.addEventListener('click', () => this.handleNextQuestion());
    }

    start() {
        this.currentQuestionIndex = 0;
        this.selectedAnswers = {};
        this.nextButton.textContent = 'Nākamais jautājums';
        this.showQuestion();
    }

    showQuestion() {
        this.clearAnswers();
        const currentQuestion = this.questions[this.currentQuestionIndex];
        const questionNumber = this.currentQuestionIndex + 1;
        const totalQuestions = this.questions.length;

        // Update progress bar
        const progressPercent = (questionNumber / totalQuestions) * 100;
        this.progressFill.style.width = progressPercent + '%';
        this.progressLabel.textContent = `Jautājums ${questionNumber} no ${totalQuestions}`;

        // Display question
        this.questionElement.textContent = currentQuestion.question_text;

        // Display answers
        if (currentQuestion.answers && Array.isArray(currentQuestion.answers)) {
            currentQuestion.answers.forEach(answer => {
                const button = document.createElement('button');
                button.textContent = answer.answer_text;
                button.className = 'btn answer-btn';
                button.dataset.answerId = answer.id;
                
                // Check if this answer was already selected
                if (this.selectedAnswers[currentQuestion.id] === answer.id) {
                    button.classList.add('selected');
                }
                
                button.addEventListener('click', (event) => this.selectAnswer(event, currentQuestion.id));
                this.answerButtonsElement.appendChild(button);
            });
        }

        // Update button text for last question
        if (this.currentQuestionIndex === this.questions.length - 1) {
            this.nextButton.textContent = 'Pabeigt testu';
        }

        this.nextButton.style.display = 'inline-block';
    }

    clearAnswers() {
        this.nextButton.style.display = 'none';
        this.answerButtonsElement.innerHTML = '';
    }

    selectAnswer(event, questionId) {
        const selectedButton = event.target;
        const answerId = parseInt(selectedButton.dataset.answerId);

        // Store the selected answer
        this.selectedAnswers[questionId] = answerId;

        // Update button styling
        Array.from(this.answerButtonsElement.children).forEach(button => {
            button.classList.remove('selected');
        });
        selectedButton.classList.add('selected');

        this.nextButton.style.display = 'inline-block';
    }

    handleNextQuestion() {
        // Check if user selected an answer
        const currentQuestion = this.questions[this.currentQuestionIndex];
        if (!this.selectedAnswers[currentQuestion.id]) {
            alert('Lūdzu, izvēlieties atbildi pirms turpināt.');
            return;
        }

        this.currentQuestionIndex += 1;
        if (this.currentQuestionIndex < this.questions.length) {
            this.showQuestion();
        } else {
            this.submitQuiz();
        }
    }

    submitQuiz() {
        this.nextButton.disabled = true;
        this.nextButton.textContent = 'Sūta rezultātus...';

        const answers = this.selectedAnswers;

        fetch('./quiz-submit.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ answers: answers }),
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                window.location.href = 'results.php';
            } else {
                alert('Kļūda sūtot rezultātus: ' + (data.message || 'Nezināma kļūda'));
                this.nextButton.disabled = false;
                this.nextButton.textContent = 'Pabeigt testu';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Kļūda sūtot rezultātus. Lūdzu, mēģiniet vēlreiz.');
            this.nextButton.disabled = false;
            this.nextButton.textContent = 'Pabeigt testu';
        });
    }
}

// Initialize quiz when DOM is loaded
window.addEventListener('DOMContentLoaded', () => {
    const questionElement = document.getElementById('question-text');
    const answerButtonsElement = document.getElementById('answer-buttons');
    const nextButton = document.getElementById('next-btn');
    const progressFill = document.getElementById('progress-fill');
    const progressLabel = document.getElementById('progress-label');

    if (window.quizData && Array.isArray(window.quizData)) {
        const quiz = new Quiz(window.quizData, questionElement, answerButtonsElement, nextButton, progressFill, progressLabel);
        quiz.start();
    } else {
        console.error('Quiz data not available');
        document.body.innerHTML = '<main class="page-card"><h1>Kļūda</h1><p>Testa dati netika ielādēti. Lūdzu, mēģiniet vēlreiz.</p><a class="button-link" href="topics.php">Atpakaļ uz tēmu izvēli</a></main>';
    }
});
