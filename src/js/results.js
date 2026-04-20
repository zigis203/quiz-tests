class QuizResults {
    constructor(resultTextElement) {
        this.resultTextElement = resultTextElement;
        this.render();
    }

    render() {
        const score = Number(localStorage.getItem('quizScore')) || 0;
        const total = Number(localStorage.getItem('quizTotal')) || 0;
        this.resultTextElement.textContent = `Tev izdevās ${score} no ${total} jautājumiem.`;

        document.getElementById('retry-button').addEventListener('click', () => this.retryQuiz());
        document.getElementById('topic-button').addEventListener('click', () => this.chooseTopic());
    }

    retryQuiz() {
        window.location.href = 'quiz.html';
    }

    chooseTopic() {
        window.location.href = 'topics.html';
    }
}

window.addEventListener('DOMContentLoaded', () => {
    const resultTextElement = document.getElementById('resultText');
    new QuizResults(resultTextElement);
});
