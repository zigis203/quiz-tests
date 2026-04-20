class TopicSelector {
    constructor(selectElement, startButton) {
        this.selectElement = selectElement;
        this.startButton = startButton;
        this.startButton.addEventListener('click', () => this.startQuiz());
    }

    startQuiz() {
        const topic = this.selectElement.value;
        if (!topic) {
            alert('Lūdzu izvēlies tematu!');
            return;
        }

        localStorage.setItem('quizTopic', topic);
        window.location.href = 'quiz.html';
    }
}

window.addEventListener('DOMContentLoaded', () => {
    const selectElement = document.getElementById('topic');
    const startButton = document.getElementById('start-quiz');
    new TopicSelector(selectElement, startButton);
});
