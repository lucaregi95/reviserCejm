<?php
require_once 'config.php';
session_start();

$total = (int) $pdo->query("SELECT COUNT(*) FROM notion")->fetchColumn();
$nb    = isset($_GET['nb']) ? (int)$_GET['nb'] : 5;
$nb    = max(2, min($nb, $total, 20));

// Réinitialisation si nouveau quiz
if (isset($_GET['nb'])) {
    $toutes    = $pdo->query("SELECT * FROM notion")->fetchAll(PDO::FETCH_ASSOC);
    shuffle($toutes);
    $_SESSION['saisie_questions'] = array_slice($toutes, 0, $nb);
    $_SESSION['saisie_index']     = 0;
    $_SESSION['saisie_score']     = 0;
    $_SESSION['saisie_total']     = $nb;
    $_SESSION['saisie_repondu']   = false;
    $_SESSION['saisie_saisie']    = '';
    $_SESSION['saisie_correct']   = false;
}

// Sécurité : si session absente, retour accueil
if (!isset($_SESSION['saisie_questions'])) {
    header("Location: index.php");
    exit;
}

// Traitement réponse
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reponse']) && !$_SESSION['saisie_repondu']) {
    $bonne  = $_SESSION['saisie_questions'][$_SESSION['saisie_index']]['mot'];
    $saisie = trim(isset($_POST['saisie']) ? $_POST['saisie'] : '');
    $ok     = strtolower($saisie) === strtolower($bonne);
    $_SESSION['saisie_repondu'] = true;
    $_SESSION['saisie_saisie']  = $saisie;
    $_SESSION['saisie_correct'] = $ok;
    if ($ok) $_SESSION['saisie_score']++;
}

// Passage à la question suivante
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['suivant']) && $_SESSION['saisie_repondu']) {
    $_SESSION['saisie_index']++;
    $_SESSION['saisie_repondu'] = false;
    $_SESSION['saisie_saisie']  = '';
    $_SESSION['saisie_correct'] = false;
}

$index    = $_SESSION['saisie_index'];
$total_q  = $_SESSION['saisie_total'];
$score    = $_SESSION['saisie_score'];
$termine  = ($index >= $total_q);
$question = $termine ? null : $_SESSION['saisie_questions'][$index];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <title>Quiz Saisie – GlossaireProf</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <style>
        body { background: #f0f6ff; font-family: 'Segoe UI', sans-serif; }
        .navbar { background: #1a3a6c !important; }
        .navbar-brand, .nav-link { color: #fff !important; }
        .nav-link:hover { color: #93c5fd !important; }
        .quiz-wrap { max-width: 680px; margin: 50px auto; }
        .quiz-card {
            background: #fff;
            border: 1px solid #dbeafe;
            border-radius: 18px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(37,99,235,.08);
            overflow: hidden;
            word-wrap: break-word;
        }
        .progress { height: 8px; border-radius: 999px; background: #dbeafe; margin-bottom: 28px; }
        .progress-bar { background: #2563eb; border-radius: 999px; transition: width .4s; }
        .question-label { font-size: .8rem; font-weight: 600; color: #2563eb; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 10px; }
        .definition-box {
            background: #f0f6ff;
            border-left: 4px solid #2563eb;
            border-radius: 8px;
            padding: 18px 20px;
            font-size: 1rem;
            color: #1e293b;
            line-height: 1.7;
            margin-bottom: 28px;
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: pre-wrap;
            overflow: hidden;
        }
        .saisie-input { border: 2px solid #dbeafe; border-radius: 10px; padding: 13px 16px; font-size: 1rem; width: 100%; transition: border-color .2s; outline: none; }
        .saisie-input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,.12); }
        .saisie-input.ok { border-color: #16a34a; background: #f0fdf4; color: #15803d; font-weight: 700; }
        .saisie-input.ko { border-color: #dc2626; background: #fef2f2; color: #b91c1c; }
        .btn-valider { background: #2563eb; color: #fff; border: none; border-radius: 10px; padding: 11px 28px; font-weight: 700; font-size: 1rem; margin-top: 16px; width: 100%; transition: background .2s; }
        .btn-valider:hover { background: #1d4ed8; }
        .feedback { border-radius: 10px; padding: 14px 18px; margin-top: 16px; font-weight: 600; font-size: .95rem; }
        .feedback.ok { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
        .feedback.ko { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
        .btn-suivant { background: #2563eb; color: #fff; border: none; border-radius: 10px; padding: 11px 32px; font-weight: 700; font-size: 1rem; margin-top: 14px; transition: background .2s; }
        .btn-suivant:hover { background: #1d4ed8; }
        .score-card { text-align: center; padding: 20px 0; }
        .score-circle { width: 110px; height: 110px; border-radius: 50%; background: #eff6ff; border: 4px solid #2563eb; display: flex; flex-direction: column; align-items: center; justify-content: center; margin: 0 auto 24px; }
        .score-circle .num { font-size: 2rem; font-weight: 700; color: #2563eb; line-height: 1; }
        .score-circle .denom { font-size: .85rem; color: #64748b; }
        .btn-retry { background: #2563eb; color: #fff; border: none; border-radius: 10px; padding: 11px 28px; font-weight: 700; text-decoration: none; display: inline-block; margin: 6px; }
        .btn-retry:hover { background: #1d4ed8; color: #fff; }
        .btn-home { background: #f0f6ff; color: #2563eb; border: 2px solid #dbeafe; border-radius: 10px; padding: 11px 28px; font-weight: 700; text-decoration: none; display: inline-block; margin: 6px; }
        .btn-home:hover { background: #dbeafe; color: #1d4ed8; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">Glossaire<span style="color:#93c5fd">Prof</span></a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto gap-2">
                <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                <li class="nav-item"><a class="nav-link" href="notions.php">Toutes les notions</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="quiz-wrap px-3">
    <div class="quiz-card">

        <?php if ($termine): ?>
            <div class="score-card">
                <h4 class="fw-bold mb-4" style="color:#1a3a6c">Quiz terminé !</h4>
                <div class="score-circle">
                    <span class="num"><?= $score ?></span>
                    <span class="denom">/ <?= $total_q ?></span>
                </div>
                <?php
                $pct = round($score / $total_q * 100);
                if ($pct >= 80)     { $msg = "🎉 Excellent travail !";     $col = "#15803d"; }
                elseif ($pct >= 50) { $msg = "👍 Pas mal, continuez !";    $col = "#d97706"; }
                else                { $msg = "📖 Révisez encore un peu !"; $col = "#dc2626"; }
                ?>
                <p style="font-size:1.1rem;font-weight:700;color:<?= $col ?>"><?= $msg ?></p>
                <p style="color:#64748b"><?= $pct ?>% de bonnes réponses</p>
                <div class="mt-3">
                    <a href="quiz_saisie.php?nb=<?= $total_q ?>" class="btn-retry">🔁 Recommencer</a>
                    <a href="index.php" class="btn-home">🏠 Accueil</a>
                </div>
            </div>

        <?php else: ?>
            <div class="question-label">Question <?= $index + 1 ?> / <?= $total_q ?></div>
            <div class="progress">
                <div class="progress-bar" style="width:<?= round($index / $total_q * 100) ?>%"></div>
            </div>
            <div class="definition-box"><?= nl2br(htmlspecialchars($question['definition'])) ?></div>
            <p style="font-weight:600;color:#1e293b;margin-bottom:12px;">Quel est le mot correspondant à cette définition ?</p>

            <form method="POST" action="quiz_saisie.php">
                <input type="text"
                       name="saisie"
                       class="saisie-input <?= $_SESSION['saisie_repondu'] ? ($_SESSION['saisie_correct'] ? 'ok' : 'ko') : '' ?>"
                       placeholder="Tapez votre réponse…"
                       value="<?= htmlspecialchars($_SESSION['saisie_repondu'] ? $_SESSION['saisie_saisie'] : '') ?>"
                    <?= $_SESSION['saisie_repondu'] ? 'readonly' : 'autofocus' ?>/>

                <?php if (!$_SESSION['saisie_repondu']): ?>
                    <button type="submit" name="reponse" value="1" class="btn-valider">✅ Valider</button>
                <?php else: ?>
                    <?php if ($_SESSION['saisie_correct']): ?>
                        <div class="feedback ok">✅ Bonne réponse !</div>
                    <?php else: ?>
                        <div class="feedback ko">❌ Mauvaise réponse. La bonne réponse était : <strong><?= htmlspecialchars($question['mot']) ?></strong></div>
                    <?php endif; ?>
                    <br>
                    <button type="submit" name="suivant" value="1" class="btn-suivant">
                        <?= ($index + 1 >= $total_q) ? 'Voir les résultats →' : 'Question suivante →' ?>
                    </button>
                <?php endif; ?>
            </form>
        <?php endif; ?>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>