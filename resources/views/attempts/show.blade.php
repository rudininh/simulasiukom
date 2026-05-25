<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $attempt->exam->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('assets/cat.css') }}" rel="stylesheet">
</head>
<body>
@php
    $payload = $questions->map(fn($q) => [
        'id' => $q->id,
        'text' => $q->question_text,
        'category' => $q->category->name.' ('.$q->category->code.')',
        'options' => ['A'=>$q->option_a,'B'=>$q->option_b,'C'=>$q->option_c,'D'=>$q->option_d,'E'=>$q->option_e],
    ])->values();
    $answerPayload = $answers->mapWithKeys(fn($a, $qid) => [$qid => $a->selected_answer])->toArray();
@endphp
<div data-exam-app
     data-questions='@json($payload)'
     data-answers='@json($answerPayload)'
     data-answer-url="{{ route('attempts.answer', $attempt) }}"
     data-finish-url="{{ route('attempts.finish', $attempt) }}"
     data-result-url="{{ route('results.show', $attempt) }}"
     data-csrf="{{ csrf_token() }}"
     data-remaining="{{ $remainingSeconds }}">
    <header class="exam-header">
        <div class="d-flex align-items-center gap-4">
            <div style="width:450px">
                <h1 class="h5 fw-bold mb-2">{{ $attempt->exam->title }}</h1>
                <div class="small">Peserta: {{ auth()->user()->name }}</div>
            </div>
            <div class="flex-grow-1 progress-pill">
                <div class="d-flex justify-content-end fw-bold small mb-2"><span id="answered-label">0 / {{ $questions->count() }}</span></div>
                <div class="progress" style="height:8px"><div id="exam-progress" class="progress-bar bg-primary" style="width:0%"></div></div>
            </div>
            <div class="timer-pill"><i class="fa-solid fa-clock me-2"></i><span id="timer">00:00:00</span></div>
        </div>
    </header>
    <div class="exam-shell">
        <div class="d-flex gap-4 align-items-start flex-wrap flex-lg-nowrap">
            <aside class="exam-sidebar">
                <div class="cat-card p-3 mb-3">
                    <button id="finish-confirm" class="btn btn-danger w-100 fw-bold"><i class="fa-solid fa-check me-1"></i>Selesaikan Ujian</button>
                </div>
                <div class="cat-card p-4">
                    <h6 class="fw-bold mb-3"><i class="fa-solid fa-list me-1"></i>Navigasi Soal</h6>
                    <div class="question-nav mb-4">
                        @foreach ($questions as $index => $question)
                            <button class="nav-number unanswered" type="button">{{ $index + 1 }}</button>
                        @endforeach
                    </div>
                    <div class="small d-grid gap-3">
                        <div><span class="legend-dot" style="background:#1fa33a"></span>Sudah Dijawab</div>
                        <div><span class="legend-dot" style="background:#ffd95a"></span>Soal Aktif</div>
                        <div><span class="legend-dot" style="background:#f00000"></span>Belum Dijawab</div>
                    </div>
                </div>
            </aside>
            <section class="flex-grow-1">
                <div class="cat-card question-card">
                    <h2 id="question-title" class="h4 fw-bold"></h2>
                    <span id="question-category" class="category-badge"></span>
                    <hr class="my-4">
                    <div id="question-text" class="question-box mb-4"></div>
                    <div id="options"></div>
                    <hr class="my-4">
                    <div class="d-flex justify-content-between">
                        <button id="save-next" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Simpan dan Lanjutkan</button>
                        <button id="skip-next" class="btn btn-outline-danger"><i class="fa-solid fa-forward me-1"></i>Lewatkan</button>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<footer class="app-footer">
    <div>Dibuat oleh Rudini</div>
    <div>Open Source: <a href="https://github.com/rudininh/antrian-bkpsdm" target="_blank" rel="noopener">github.com/rudininh/antrian-bkpsdm</a></div>
</footer>
<div class="modal fade" id="finishModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Apakah Anda yakin ingin menyelesaikan ujian?</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <p>Sudah dijawab: <strong id="modal-answered">0</strong></p>
                <p>Belum dijawab: <strong id="modal-unanswered">0</strong></p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button id="finish-submit" class="btn btn-danger">Selesaikan Ujian</button>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets/exam.js') }}"></script>
</body>
</html>
