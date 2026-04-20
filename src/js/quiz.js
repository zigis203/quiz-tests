class Quiz {
    constructor(questions, questionElement, answerButtonsElement, nextButton) {
        this.questions = questions;
        this.questionElement = questionElement;
        this.answerButtonsElement = answerButtonsElement;
        this.nextButton = nextButton;
        this.currentQuestionIndex = 0;
        this.score = 0;
        this.nextButton.addEventListener('click', () => this.handleNextQuestion());
    }

    start() {
        this.currentQuestionIndex = 0;
        this.score = 0;
        this.nextButton.textContent = 'Next';
        this.showQuestion();
    }

    showQuestion() {
        this.clearAnswers();
        const currentQuestion = this.questions[this.currentQuestionIndex];
        document.getElementById('question-number').textContent = `Jautājums ${this.currentQuestionIndex + 1} / ${this.questions.length}`;
        this.questionElement.textContent = currentQuestion.question;

        currentQuestion.answers.forEach(answer => {
            const button = document.createElement('button');
            button.textContent = answer.text;
            button.className = 'btn answer-btn';
            if (answer.correct) {
                button.dataset.correct = 'true';
            }
            button.addEventListener('click', event => this.selectAnswer(event));
            this.answerButtonsElement.appendChild(button);
        });
    }

    clearAnswers() {
        this.nextButton.style.display = 'none';
        this.answerButtonsElement.innerHTML = '';
    }

    selectAnswer(event) {
        const selectedButton = event.target;
        const isCorrect = selectedButton.dataset.correct === 'true';
        if (isCorrect) {
            selectedButton.classList.add('correct');
            this.score += 1;
        } else {
            selectedButton.classList.add('incorrect');
        }

        Array.from(this.answerButtonsElement.children).forEach(button => {
            button.disabled = true;
            if (button.dataset.correct === 'true') {
                button.classList.add('correct');
            }
        });

        this.nextButton.style.display = 'inline-block';
    }

    handleNextQuestion() {
        this.currentQuestionIndex += 1;
        if (this.currentQuestionIndex < this.questions.length) {
            this.showQuestion();
        } else {
            this.saveResult();
            window.location.href = 'results.html';
        }
    }

    saveResult() {
        localStorage.setItem('quizScore', this.score.toString());
        localStorage.setItem('quizTotal', this.questions.length.toString());
    }
}

const questions = [
    {
        question: 'Kurš ir lielākais zīdītājs uz Zemes?',
        answers: [
            { text: 'Bumba', correct: false },
            { text: 'Zilais valis', correct: true },
            { text: 'Zilonis', correct: false },
            { text: 'Žirafe', correct: false }
        ]
    },
    {
        question: 'Kura zvaigzne ir redzama mūsu dienvidu puslodē?',
        answers: [
            { text: 'Polārzvaigzne', correct: false },
            { text: 'Saule', correct: false },
            { text: 'Crux (Kasiopeja)', correct: true },
            { text: 'Oriona josta', correct: false }
        ]
    },
    {
        question: 'Kurā gadā cilvēks pirmo reizi nolaidās uz Mēness?',
        answers: [
            { text: '1969', correct: true },
            { text: '1958', correct: false },
            { text: '1975', correct: false },
            { text: '1983', correct: false }
        ]
    }
];

window.addEventListener('DOMContentLoaded', () => {
    const quiz = new Quiz(
        questions,
        document.getElementById('question'),
        document.getElementById('answer-buttons'),
        document.getElementById('next-btn')
    );

    quiz.start();
});
