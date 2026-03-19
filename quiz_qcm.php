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
    $_SESSION['qcm_questions']  = array_slice($toutes, 0, $nb);
    $_SESSION['qcm_index']      = 0;
    $_SESSION['qcm_score']      = 0;
    $_SESSION['qcm_total']      = $nb;
    $_SESSION['qcm_repondu']    = false;
    $_SESSION['qcm_choix_fait'] = null;
}

// Sécurité : si session absente, retour accueil
if (!isset($_SESSION['qcm_questions'])) {
    header("Location: index.php");
    exit;
}

// Traitement réponse
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reponse']) && !$_SESSION['qcm_repondu']) {
    $bonne = $_SESSION['qcm_questions'][$_SESSION['qcm_index']]['mot'];
    $choix = $_POST['reponse'];
    $_SESSION['qcm_repondu']    = true;
    $_SESSION['qcm_choix_fait'] = $choix;
    if (strtolower(trim($choix)) === strtolower(trim($bonne))) {
        $_SESSION['qcm_score']++;
    }
}

// Passage à la question suivante
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['suivant']) && $_SESSION['qcm_repondu']) {
    $_SESSION['qcm_index']++;
    $_SESSION['qcm_repondu']    = false;
    $_SESSION['qcm_choix_fait'] = null;
    unset($_SESSION['qcm_choix']); // force la régénération pour la prochaine question
}

$index    = $_SESSION['qcm_index'];
$total_q  = $_SESSION['qcm_total'];
$score    = $_SESSION['qcm_score'];
$termine  = ($index >= $total_q);
$question = $termine ? null : $_SESSION['qcm_questions'][$index];

// Générer 4 choix
// Générer 4 choix et les stocker en session pour garder le même ordre
$choix4 = [];
if ($question) {
    // On ne regénère les choix que si on est sur une nouvelle question
    if (!isset($_SESSION['qcm_choix']) || !$_SESSION['qcm_repondu'] && !isset($_POST['reponse'])) {
        $tous_mots = $pdo->query("SELECT mot FROM notion")->fetchAll(PDO::FETCH_COLUMN);
        $mauvais   = array_values(array_filter($tous_mots, function($m) use ($question) {
            return strtolower($m) !== strtolower($question['mot']);
        }));
        shuffle($mauvais);
        $mauvais = array_slice($mauvais, 0, 3);
        $choix4  = array_merge($mauvais, [$question['mot']]);
        shuffle($choix4);
        $_SESSION['qcm_choix'] = $choix4;
    } else {
        $choix4 = $_SESSION['qcm_choix'];
    }

}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <title>Quiz QCM – GlossaireProf</title>
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
        .choix-btn { display: block; width: 100%; text-align: left; background: #f8fbff; border: 2px solid #dbeafe; border-radius: 10px; padding: 14px 18px; margin-bottom: 12px; font-size: .97rem; font-weight: 500; color: #1e293b; cursor: pointer; transition: border-color .2s, background .2s; }
        .choix-btn:hover:not(:disabled) { border-color: #2563eb; background: #eff6ff; }
        .choix-btn.correct   { border-color: #16a34a; background: #f0fdf4; color: #15803d; font-weight: 700; }
        .choix-btn.incorrect { border-color: #dc2626; background: #fef2f2; color: #b91c1c; }
        .choix-btn.grise     { border-color: #e2e8f0; background: #f8fafc; color: #94a3b8; }
        .feedback { border-radius: 10px; padding: 14px 18px; margin-top: 4px; font-weight: 600; font-size: .95rem; }
        .feedback.ok { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
        .feedback.ko { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
        .btn-suivant { background: #2563eb; color: #fff; border: none; border-radius: 10px; padding: 11px 32px; font-weight: 700; font-size: 1rem; margin-top: 20px; transition: background .2s; }
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
                if ($pct >= 80)     { $msg = "🎉 Excellent travail !";      $col = "#15803d"; }
                elseif ($pct >= 50) { $msg = "👍 Pas mal, continuez !";     $col = "#d97706"; }
                else                { $msg = "📖 Révisez encore un peu !";  $col = "#dc2626"; }
                ?>
                <p style="font-size:1.1rem;font-weight:700;color:<?= $col ?>"><?= $msg ?></p>
                <p style="color:#64748b"><?= $pct ?>% de bonnes réponses</p>
                <div class="mt-3">
                    <a href="quiz_qcm.php?nb=<?= $total_q ?>" class="btn-retry">🔁 Recommencer</a>
                    <a href="index.php" class="btn-home">🏠 Accueil</a>
                </div>
            </div>

        <?php else: ?>
            <div class="question-label">Question <?= $index + 1 ?> / <?= $total_q ?></div>
            <div class="progress">
                <div class="progress-bar" style="width:<?= round($index / $total_q * 100) ?>%"></div>
            </div>
            <div class="definition-box"><?= nl2br(htmlspecialchars($question['definition'])) ?></div>
            <p style="font-weight:600;color:#1e293b;margin-bottom:14px;">Quel est le mot correspondant à cette définition ?</p>

            <form method="POST" action="quiz_qcm.php">
                <?php foreach ($choix4 as $c):
                    $cls = '';
                    if ($_SESSION['qcm_repondu']) {
                        if (strtolower($c) === strtolower($question['mot']))              $cls = 'correct';
                        elseif ($_SESSION['qcm_choix_fait'] === $c)                      $cls = 'incorrect';
                        else                                                              $cls = 'grise';
                    }
                    ?>
                    <button type="submit" name="reponse" value="<?= htmlspecialchars($c) ?>"
                            class="choix-btn <?= $cls ?>"
                        <?= $_SESSION['qcm_repondu'] ? 'disabled' : '' ?>>
                        <?= htmlspecialchars($c) ?>
                    </button>
                <?php endforeach; ?>

                <?php if ($_SESSION['qcm_repondu']): ?>
                    <?php if (strtolower($_SESSION['qcm_choix_fait']) === strtolower($question['mot'])): ?>
                        <div class="feedback ok">✅ Bonne réponse !</div>
                    <?php else: ?>
                        <div class="feedback ko">❌ Mauvaise réponse. La bonne réponse était : <strong><?= htmlspecialchars($question['mot']) ?></strong></div>
                    <?php endif; ?>
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