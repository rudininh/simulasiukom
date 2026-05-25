document.addEventListener('DOMContentLoaded', () => {
    const app = document.querySelector('[data-exam-app]');
    if (!app) return;

    const questions = JSON.parse(app.dataset.questions);
    const answerUrl = app.dataset.answerUrl;
    const finishUrl = app.dataset.finishUrl;
    const resultUrl = app.dataset.resultUrl;
    const token = app.dataset.csrf;
    let current = 0;
    let answers = JSON.parse(app.dataset.answers);
    let remaining = Number(app.dataset.remaining);
    let finishing = false;

    const title = document.getElementById('question-title');
    const category = document.getElementById('question-category');
    const text = document.getElementById('question-text');
    const options = document.getElementById('options');
    const answeredLabel = document.getElementById('answered-label');
    const progress = document.getElementById('exam-progress');
    const timer = document.getElementById('timer');
    const nav = document.querySelectorAll('.nav-number');

    function answeredCount() {
        return Object.values(answers).filter(Boolean).length;
    }

    function paintNav() {
        nav.forEach((button, index) => {
            button.classList.remove('answered', 'unanswered', 'active');
            button.classList.add(index === current ? 'active' : (answers[questions[index].id] ? 'answered' : 'unanswered'));
        });
        const answered = answeredCount();
        answeredLabel.textContent = `${answered} / ${questions.length}`;
        progress.style.width = `${answered / questions.length * 100}%`;
    }

    function renderQuestion(index) {
        current = index;
        const q = questions[current];
        title.textContent = `Soal ${current + 1} dari ${questions.length}`;
        category.textContent = q.category;
        text.textContent = q.text;
        options.innerHTML = '';
        ['A', 'B', 'C', 'D', 'E'].forEach(letter => {
            const row = document.createElement('div');
            row.className = 'option-box mb-3' + (answers[q.id] === letter ? ' selected' : '');
            row.dataset.answer = letter;
            row.innerHTML = `<span class="option-letter">${letter}</span><span>${q.options[letter]}</span>`;
            row.addEventListener('click', () => choose(letter));
            options.appendChild(row);
        });
        paintNav();
    }

    function choose(letter) {
        const q = questions[current];
        answers[q.id] = letter;
        document.querySelectorAll('.option-box').forEach(el => el.classList.toggle('selected', el.dataset.answer === letter));
        save(q.id, letter);
        paintNav();
    }

    async function save(questionId, selected) {
        await fetch(answerUrl, {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json'},
            body: JSON.stringify({question_id: questionId, selected_answer: selected})
        });
    }

    async function finish() {
        if (finishing) return;
        finishing = true;
        window.onbeforeunload = null;
        const response = await fetch(finishUrl, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': token, 'Accept': 'application/json'}
        });
        const data = await response.json();
        window.location.href = data.redirect || resultUrl;
    }

    function tick() {
        remaining = Math.max(0, remaining - 1);
        const h = String(Math.floor(remaining / 3600)).padStart(2, '0');
        const m = String(Math.floor((remaining % 3600) / 60)).padStart(2, '0');
        const s = String(remaining % 60).padStart(2, '0');
        timer.textContent = `${h}:${m}:${s}`;
        if (remaining <= 0) finish();
    }

    nav.forEach((button, index) => button.addEventListener('click', () => renderQuestion(index)));
    document.getElementById('save-next').addEventListener('click', () => renderQuestion(Math.min(current + 1, questions.length - 1)));
    document.getElementById('skip-next').addEventListener('click', () => renderQuestion(Math.min(current + 1, questions.length - 1)));
    document.getElementById('finish-confirm').addEventListener('click', () => {
        document.getElementById('modal-answered').textContent = answeredCount();
        document.getElementById('modal-unanswered').textContent = questions.length - answeredCount();
        bootstrap.Modal.getOrCreateInstance(document.getElementById('finishModal')).show();
    });
    document.getElementById('finish-submit').addEventListener('click', finish);
    window.onbeforeunload = () => finishing ? null : 'Ujian sedang berlangsung.';

    renderQuestion(0);
    tick();
    setInterval(tick, 1000);
});
