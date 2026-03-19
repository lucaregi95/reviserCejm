<?php
require_once 'config.php';
$total  = (int) $pdo->query("SELECT COUNT(*) FROM notion")->fetchColumn();
$max    = min(20, $total);
$min    = min(5, $total);
$defaut = $min;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <title>GlossaireProf</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <style>
        body { background: #f0f6ff; font-family: 'Segoe UI', sans-serif; }
        .navbar { background: #1a3a6c !important; }
        .navbar-brand, .nav-link { color: #fff !important; }
        .nav-link:hover { color: #93c5fd !important; }

        .hero {
            background: linear-gradient(135deg, #1a3a6c, #2563eb);
            color: white; padding: 70px 0; text-align: center;
        }
        .hero h1 { font-size: 2.8rem; font-weight: 700; }
        .hero p { font-size: 1.1rem; opacity: .8; }
        .btn-hero { background: #fff; color: #2563eb; font-weight: 600; border-radius: 8px; padding: 10px 28px; text-decoration: none; }
        .btn-hero:hover { background: #dbeafe; color: #1d4ed8; }
        .btn-hero-outline { border: 2px solid rgba(255,255,255,.5); color: #fff; background: transparent; font-weight: 600; padding: 10px 28px; border-radius: 8px; font-size: 1rem; text-decoration: none; display: inline-block; }
        .btn-hero-outline:hover { border-color: #fff; color: #fff; }

        .card-feature { border: 1px solid #dbeafe; border-radius: 14px; padding: 28px; background: #fff; height: 100%; transition: box-shadow .2s; }
        .card-feature:hover { box-shadow: 0 6px 24px rgba(37,99,235,.12); }
        .icon-box { width: 48px; height: 48px; background: #dbeafe; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; margin-bottom: 16px; }

        .quiz-section { background: #fff; border-top: 3px solid #dbeafe; padding: 60px 0; }
        .quiz-section h2 { font-size: 1.8rem; font-weight: 700; color: #1a3a6c; margin-bottom: 8px; }
        .quiz-section p.sub { color: #64748b; margin-bottom: 36px; }

        .quiz-mode-card {
            border: 2px solid #dbeafe; border-radius: 16px; padding: 28px 24px;
            background: #f8fbff; cursor: pointer; transition: border-color .2s, box-shadow .2s, transform .2s;
            height: 100%;
        }
        .quiz-mode-card:hover { border-color: #2563eb; box-shadow: 0 6px 20px rgba(37,99,235,.13); transform: translateY(-2px); }
        .quiz-mode-card.selected { border-color: #2563eb; background: #eff6ff; box-shadow: 0 0 0 3px rgba(37,99,235,.15); }
        .quiz-mode-card .mode-icon { font-size: 2.2rem; margin-bottom: 14px; }
        .quiz-mode-card h5 { font-weight: 700; color: #1a3a6c; margin-bottom: 8px; }
        .quiz-mode-card p { color: #64748b; font-size: .9rem; margin: 0; }

        .quiz-config { background: #f0f6ff; border: 1px solid #dbeafe; border-radius: 14px; padding: 28px; margin-top: 32px; }
        .quiz-config label { font-weight: 600; color: #1e293b; }
        .range-value { font-size: 1.4rem; font-weight: 700; color: #2563eb; }
        input[type=range] { accent-color: #2563eb; }

        .btn-launch { background: #2563eb; color: #fff; border: none; border-radius: 10px; padding: 12px 36px; font-weight: 700; font-size: 1.05rem; transition: background .2s, transform .15s; }
        .btn-launch:hover { background: #1d4ed8; transform: translateY(-1px); }
        .btn-launch:disabled { background: #93c5fd; cursor: not-allowed; transform: none; }

        footer { background: #1a3a6c; color: rgba(255,255,255,.5); text-align: center; padding: 20px; font-size: .85rem; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">Glossaire<span style="color:#93c5fd">Prof</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav ms-auto gap-2">
                <li class="nav-item"><a class="nav-link active" href="index.php">Accueil</a></li>
                <li class="nav-item"><a class="nav-link" href="notions.php">Toutes les notions</a></li>
                <li class="nav-item"><a class="nav-link" href="ajouter.php">Ajouter</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- HERO -->
<div class="hero">
    <div class="container">
    <span class="badge mb-3" style="background:rgba(255,255,255,.15);padding:6px 16px;border-radius:999px;">
      📚 <?= $total ?> notion<?= $total > 1 ? 's' : '' ?> disponible<?= $total > 1 ? 's' : '' ?>
    </span>
        <h1>Bienvenue sur GlossaireProf</h1>
        <p class="mb-4">La plateforme pour gérer et retrouver toutes vos notions CEJM en un coup d'œil.</p>
        <a href="notions.php" class="btn-hero me-3">Voir les notions</a>
        <a href="ajouter.php" class="btn-hero-outline">+ Ajouter une notion</a>
    </div>
</div>



<!-- QUIZ SECTION -->
<?php if ($total >= 2): ?>
    <div class="quiz-section">
        <div class="container">
            <div class="text-center">
                <h2>🎯 Lancer un quiz</h2>
                <p class="sub">Choisissez un mode et le nombre de notions, puis c'est parti !</p>
            </div>

            <div class="row g-4 justify-content-center mb-2">
                <div class="col-md-5">
                    <div class="quiz-mode-card" id="card-qcm" onclick="selectMode('qcm')">
                        <div class="mode-icon">🔤</div>
                        <h5>QCM — 4 choix</h5>
                        <p>Une définition s'affiche, vous choisissez le bon mot parmi 4 propositions.</p>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="quiz-mode-card" id="card-saisie" onclick="selectMode('saisie')">
                        <div class="mode-icon">⌨️</div>
                        <h5>Saisie libre</h5>
                        <p>Une définition s'affiche, vous tapez vous-même le mot correspondant.</p>
                    </div>
                </div>
            </div>

            <div class="quiz-config" id="quiz-config" style="display:none; max-width: 560px; margin: 32px auto 0;">
                <label class="form-label mb-3">Nombre de notions à réviser</label>
                <div class="d-flex align-items-center gap-4">
                    <input type="range" class="form-range flex-grow-1"
                           id="nb-notions"
                           min="<?= $min ?>" max="<?= $max ?>" value="<?= $defaut ?>"
                           oninput="document.getElementById('range-val').textContent = this.value"/>
                    <span class="range-value" id="range-val"><?= $defaut ?></span>
                </div>
                <div class="d-flex justify-content-between" style="font-size:.8rem;color:#94a3b8;">
                    <span><?= $min ?> min</span>
                    <span><?= $max ?> max (<?= $total ?> notion<?= $total > 1 ? 's' : '' ?> disponible<?= $total > 1 ? 's' : '' ?>)</span>
                </div>
                <div class="text-center mt-4">
                    <button class="btn-launch" id="btn-launch" onclick="lancerQuiz()">🚀 Lancer le quiz</button>
                </div>
            </div>

        </div>
    </div>
<?php else: ?>
    <div class="container py-4">
        <div class="alert alert-info text-center">Ajoutez au moins 2 notions pour pouvoir lancer un quiz.</div>
    </div>
<?php endif; ?>

<footer>GlossaireProf &copy; <?= date('Y') ?> — Base de données CEJM</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    var modeSelectionne = null;

    function selectMode(mode) {
        modeSelectionne = mode;
        document.getElementById('card-qcm').classList.remove('selected');
        document.getElementById('card-saisie').classList.remove('selected');
        document.getElementById('card-' + mode).classList.add('selected');
        document.getElementById('quiz-config').style.display = 'block';
        document.getElementById('quiz-config').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function lancerQuiz() {
        if (!modeSelectionne) return;
        var nb   = document.getElementById('nb-notions').value;
        var page = modeSelectionne === 'qcm' ? 'quiz_qcm.php' : 'quiz_saisie.php';
        window.location.href = page + '?nb=' + nb;
    }
</script>
</body>
</html>