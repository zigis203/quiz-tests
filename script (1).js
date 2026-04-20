const questions = [
    {
        question: "Which is largest animal in the world?",
        answers:[
            {text: "shark", correct:false},
            {text: "elephant", correct:false},
            {text: "blue whale", correct:true},
            {text: "giraffe", correct:false},
        ]
    },
    {
        question: "Which is largest animal in the world?",
        answers:[
            {text: "shark", correct:false},
            {text: "elephant", correct:false},
            {text: "blue whale", correct:true},
            {text: "giraffe", correct:false},
        ]
    },
];
const questionElement = document.getElementById("question");
const answerBottons = document.getElementById("answer-buttons");
const nextButton = document.getElementById("next-btn");

let currentQuestionIndex = 0;
let score = 0;
function startQuiz(){
    currentQuestionIndex = 0;
    score = 0;
    nextButton.innerHTML = "Next";
    showQuestion();
}
function showQuestion(){
    resetState();
    let currentQuestion = questions[currentQuestionIndex];
    let questionNo = currentQuestionIndex + 1;
    questionElement.innerHTML = questionNo + ". " + currentQuestion.question;

    currentQuestion.answers.forEach(answer =>{
        const button = document.createElement("button");
        button.innerHTML = answer.text;
        button.classList.add("btn");
        answerBottons.appendChild(button)
        if (answer.correct){
            button.dataset.correct = answer.correct;
        }
        button.addEventListener("click", selectAnswer);
    });
}
function resetState(){
    nextButton.style.display = "none";
    while(answerBottons.firstChild){
        answerBottons.removeChild(answerBottons.firstChild);
    }
}
function selectAnswer(e){
    const selectBtn = e.target;
    const isCorrect = selectBtn.dataset.correct === "true";
    if(isCorrect){
        selectBtn.classList.add("correct");
        } else {
            selectBtn.classList.add("incorrect")
        }
        Array.from(answerBottons.children).forEach(button =>{})
    }
startQuiz();