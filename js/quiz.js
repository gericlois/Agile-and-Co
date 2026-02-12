// ============================================
// Agile & Co Smart Service Recommender Quiz
// ============================================

(function () {
    'use strict';

    var API_URL = 'api/quiz-recommend.php';

    var QUESTIONS = [
        {
            id: 'industry',
            title: 'What type of business do you run?',
            subtitle: 'We tailor our strategies to your industry.',
            options: [
                { key: 'hvac',        label: 'HVAC / Heating & Cooling', icon: '&#x1f321;' },
                { key: 'plumbing',    label: 'Plumbing',                 icon: '&#x1f527;' },
                { key: 'electrical',  label: 'Electrical',               icon: '&#x26a1;' },
                { key: 'remodeling',  label: 'Remodeling / Construction',icon: '&#x1f528;' },
                { key: 'landscaping', label: 'Landscaping / Lawn Care',  icon: '&#x1f33f;' },
                { key: 'automotive',  label: 'Automotive',               icon: '&#x1f697;' },
                { key: 'medspa',      label: 'Med Spa / Healthcare',     icon: '&#x1f486;' },
                { key: 'other',       label: 'Other Service Business',   icon: '&#x1f4cb;' }
            ]
        },
        {
            id: 'current_marketing',
            title: 'What marketing are you currently doing?',
            subtitle: 'This helps us understand your starting point.',
            options: [
                { key: 'nothing',  label: 'Not much \u2014 mostly word-of-mouth' },
                { key: 'basic',    label: 'Basic website, maybe some social media' },
                { key: 'some_ads', label: 'Running some ads (Google or Facebook)' },
                { key: 'full',     label: 'Full setup (SEO, ads, website, social)' }
            ]
        },
        {
            id: 'goal',
            title: "What's your #1 growth goal right now?",
            subtitle: "We'll focus our recommendation on what matters most.",
            options: [
                { key: 'more_leads',      label: 'Get more leads & phone calls' },
                { key: 'online_presence',  label: 'Build a stronger online presence' },
                { key: 'beat_competitors', label: 'Outrank competitors in my area' },
                { key: 'new_website',      label: 'Get a professional new website' },
                { key: 'brand_awareness',  label: 'Increase brand awareness on social' }
            ]
        },
        {
            id: 'budget',
            title: "What's your monthly marketing budget?",
            subtitle: "We'll recommend a plan that fits your investment level.",
            options: [
                { key: 'under_1k', label: 'Under $1,000/mo' },
                { key: '1k_3k',    label: '$1,000 \u2013 $3,000/mo' },
                { key: '3k_5k',    label: '$3,000 \u2013 $5,000/mo' },
                { key: '5k_plus',  label: '$5,000+/mo' },
                { key: 'not_sure', label: 'Not sure yet' }
            ]
        },
        {
            id: 'timeline',
            title: 'How soon do you want to see results?',
            subtitle: 'Different strategies work on different timelines.',
            options: [
                { key: 'asap',       label: 'ASAP \u2014 I need leads now' },
                { key: '1_3_months', label: 'Within 1\u20133 months' },
                { key: '3_6_months', label: '3\u20136 months is fine' },
                { key: 'long_term',  label: "I'm thinking long-term growth" }
            ]
        }
    ];

    var SERVICE_ICONS = {
        seo: '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>',
        gads: '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" /></svg>',
        meta: '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" /></svg>',
        webdesign: '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>'
    };

    var currentStep = 0;
    var answers = {};
    var isSubmitting = false;

    // Mutable DOM references (re-queried on init)
    var container, resultsEl, stepsEl, progressBar, progressText, backBtn, nextBtn;

    function init() {
        container = document.getElementById('quiz-container');
        resultsEl = document.getElementById('quiz-results');
        if (!container) return;

        stepsEl = document.getElementById('quiz-steps');
        progressBar = document.getElementById('quiz-progress-bar');
        progressText = document.getElementById('quiz-progress-text');
        backBtn = document.getElementById('quiz-back');
        nextBtn = document.getElementById('quiz-next');

        renderStep(currentStep);
        updateProgress();

        backBtn.addEventListener('click', goBack);
        nextBtn.addEventListener('click', goNext);
    }

    function renderStep(index) {
        var q = QUESTIONS[index];
        var selected = answers[q.id] || null;

        var html = '<div class="quiz-step active">';
        html += '<h2>' + q.title + '</h2>';
        html += '<p class="quiz-step-sub">' + q.subtitle + '</p>';
        html += '<div class="quiz-options">';

        q.options.forEach(function (opt) {
            var sel = selected === opt.key ? ' selected' : '';
            var iconHtml = opt.icon
                ? '<div class="quiz-option-icon">' + opt.icon + '</div>'
                : '';
            html += '<div class="quiz-option' + sel + '" data-key="' + opt.key + '">';
            html += iconHtml;
            html += '<span>' + opt.label + '</span>';
            html += '</div>';
        });

        html += '</div></div>';
        stepsEl.innerHTML = html;

        // Click listeners
        stepsEl.querySelectorAll('.quiz-option').forEach(function (el) {
            el.addEventListener('click', function () {
                stepsEl.querySelectorAll('.quiz-option').forEach(function (o) {
                    o.classList.remove('selected');
                });
                this.classList.add('selected');
                answers[q.id] = this.dataset.key;
                nextBtn.disabled = false;
            });
        });

        // Button states
        backBtn.style.display = index === 0 ? 'none' : '';
        nextBtn.disabled = !answers[q.id];
        nextBtn.innerHTML = index === QUESTIONS.length - 1
            ? 'Get My Recommendations &rarr;'
            : 'Next &rarr;';
    }

    function goNext() {
        if (nextBtn.disabled || isSubmitting) return;
        if (currentStep < QUESTIONS.length - 1) {
            currentStep++;
            renderStep(currentStep);
            updateProgress();
        } else {
            submitQuiz();
        }
    }

    function goBack() {
        if (currentStep > 0) {
            currentStep--;
            renderStep(currentStep);
            updateProgress();
        }
    }

    function updateProgress() {
        var pct = ((currentStep + 1) / QUESTIONS.length) * 100;
        progressBar.style.setProperty('--progress', pct + '%');
        progressText.textContent = 'Question ' + (currentStep + 1) + ' of ' + QUESTIONS.length;
    }

    function submitQuiz() {
        isSubmitting = true;
        container.innerHTML =
            '<div class="quiz-loading">' +
            '<div class="quiz-loading-spinner"></div>' +
            '<p>Our AI is analyzing your answers...</p>' +
            '</div>';

        fetch(API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ answers: answers })
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (data.error) {
                showError(data.error);
            } else {
                showResults(data);
            }
        })
        .catch(function () {
            showError('Could not connect to the server. Please try again.');
        })
        .finally(function () {
            isSubmitting = false;
        });
    }

    function showResults(data) {
        container.style.display = 'none';
        resultsEl.style.display = 'block';
        resultsEl.scrollIntoView({ behavior: 'smooth', block: 'start' });

        var cardsHtml = '';
        data.services.forEach(function (svc) {
            var icon = SERVICE_ICONS[svc.slug] || SERVICE_ICONS.seo;
            cardsHtml +=
                '<div class="quiz-result-card">' +
                '<div class="quiz-result-card-icon">' + icon + '</div>' +
                '<h3>' + escapeHtml(svc.label) + '</h3>' +
                '<p>' + escapeHtml(svc.reason) + '</p>' +
                '</div>';
        });

        var industrySlug = answers.industry || '';
        var contactHref = industrySlug && industrySlug !== 'other'
            ? 'contact.php?industry=' + encodeURIComponent(industrySlug)
            : 'contact.php';

        resultsEl.innerHTML =
            '<div class="quiz-results-header">' +
            '<p class="pre-headline" style="justify-content: center;">Your Custom Recommendation</p>' +
            '<h2>Here\'s Your Ideal <span class="text-accent">Marketing Plan</span></h2>' +
            '</div>' +
            '<div class="quiz-results-cards">' + cardsHtml + '</div>' +
            '<div class="quiz-explanation">' +
            '<h3>' +
            '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg>' +
            ' Why This Fits You' +
            '</h3>' +
            '<p>' + escapeHtml(data.explanation) + '</p>' +
            '</div>' +
            '<div class="quiz-cta">' +
            '<p>' + escapeHtml(data.cta_text) + '</p>' +
            '<a href="' + contactHref + '" class="btn btn-primary">Let\'s Get Started <span class="btn-arrow">&rarr;</span></a>' +
            '<br>' +
            '<button class="quiz-retake" id="quiz-retake">Retake Quiz</button>' +
            '</div>';

        document.getElementById('quiz-retake').addEventListener('click', retakeQuiz);
    }

    function showError(msg) {
        container.innerHTML =
            '<div style="text-align: center; padding: 60px 20px;">' +
            '<p style="color: #ff6b6b; font-size: 16px; margin-bottom: 24px;">' + escapeHtml(msg) + '</p>' +
            '<button class="btn btn-secondary" id="quiz-retry">Try Again</button>' +
            '</div>';
        document.getElementById('quiz-retry').addEventListener('click', retakeQuiz);
    }

    function retakeQuiz() {
        answers = {};
        currentStep = 0;
        isSubmitting = false;

        container.innerHTML =
            '<div class="quiz-progress">' +
            '<div class="quiz-progress-bar" id="quiz-progress-bar"></div>' +
            '<span class="quiz-progress-text" id="quiz-progress-text"></span>' +
            '</div>' +
            '<div class="quiz-steps" id="quiz-steps"></div>' +
            '<div class="quiz-nav" id="quiz-nav">' +
            '<button class="btn btn-secondary quiz-back" id="quiz-back" style="display: none;">&larr; Back</button>' +
            '<button class="btn btn-primary quiz-next" id="quiz-next" disabled>Next &rarr;</button>' +
            '</div>';

        container.style.display = '';
        resultsEl.style.display = 'none';
        init();
    }

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
