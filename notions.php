<?php
require_once 'config.php';

if (isset($_GET['supprimer'])) {
    $id = (int) $_GET['supprimer'];
    $pdo->prepare("DELETE FROM notion WHERE id_notion = ?")->execute([$id]);
    header("Location: notions.php?success=supprime");
    exit;
}

$notions = $pdo->query("SELECT * FROM notion ORDER BY mot ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <title>Toutes les notions – GlossaireProf</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <style>
        body { background: #f0f6ff; font-family: 'Segoe UI', sans-serif; }
        .navbar { background: #1a3a6c !important; }
        .navbar-brand, .nav-link { color: #fff !important; }
        .nav-link:hover { color: #93c5fd !important; }
        .page-header { background: linear-gradient(135deg, #1a3a6c, #2563eb); color: #fff; padding: 40px 0; }
        .page-header h1 { font-size: 2rem; font-weight: 700; }

        .notion-card {
            background: #fff;
            border: 1px solid #dbeafe;
            border-radius: 14px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            transition: box-shadow .2s, transform .2s;
        }
        .notion-card:hover { box-shadow: 0 8px 28px rgba(37,99,235,.13); transform: translateY(-2px); }
        .notion-mot { font-size: 1.1rem; font-weight: 700; color: #2563eb; margin-bottom: 10px; }
        .notion-def {
            color: #475569;
            font-size: .93rem;
            line-height: 1.65;
            flex-grow: 1;
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: pre-wrap;
        }
        .badge-id { background: #dbeafe; color: #1d4ed8; font-size: .75rem; border-radius: 999px; padding: 3px 10px; white-space: nowrap; }
        .btn-modifier { background: #2563eb; color: #fff; border: none; border-radius: 7px; padding: 5px 14px; font-size: .85rem; text-decoration: none; }
        .btn-modifier:hover { background: #1d4ed8; color: #fff; }
        .btn-supprimer { background: #fee2e2; color: #dc2626; border: none; border-radius: 7px; padding: 5px 14px; font-size: .85rem; text-decoration: none; }
        .btn-supprimer:hover { background: #fecaca; color: #b91c1c; }
        .empty-state { text-align: center; padding: 80px 0; color: #94a3b8; }
        footer { background: #1a3a6c; color: rgba(255,255,255,.5); text-align: center; padding: 20px; font-size: .85rem; margin-top: 60px; }
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
                <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                <li class="nav-item"><a class="nav-link active" href="notions.php">Toutes les notions</a></li>
                <li class="nav-item"><a class="nav-link" href="ajouter.php">Ajouter</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="page-header">
    <div class="container d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h1>📚 Toutes les notions</h1>
            <p class="mb-0" style="opacity:.75"><?= count($notions) ?> notion<?= count($notions) > 1 ? 's' : '' ?> au total</p>
        </div>
        <a href="ajouter.php" class="btn btn-light fw-bold" style="border-radius:8px;color:#2563eb;">+ Ajouter une notion</a>
    </div>
</div>

<div class="container py-4">

    <?php if (isset($_GET['success'])): ?>
        <?php if ($_GET['success'] === 'supprime'): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                🗑️ Notion supprimée avec succès.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_GET['success'] === 'modifie'): ?>
            <div class="alert alert-success alert-dismissible fade show">
                ✅ Notion modifiée avec succès.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (empty($notions)): ?>
        <div class="empty-state">
            <p style="font-size:3rem">📭</p>
            <h5>Aucune notion pour l'instant</h5>
            <a href="ajouter.php" class="btn btn-primary mt-2">Ajouter la première</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($notions as $n): ?>
                <div class="col-sm-6 col-lg-4">
                    <div class="notion-card">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="notion-mot"><?= htmlspecialchars($n['mot']) ?></div>
                            <span class="badge-id">#<?= $n['id_notion'] ?></span>
                        </div>
                        <p class="notion-def"><?= htmlspecialchars($n['definition']) ?></p>
                        <div class="d-flex gap-2 mt-3 pt-3" style="border-top:1px solid #e2e8f0;">
                            <a href="modifier.php?id=<?= $n['id_notion'] ?>" class="btn-modifier">✏️ Modifier</a>
                            <a href="notions.php?supprimer=<?= $n['id_notion'] ?>"
                               class="btn-supprimer"
                               onclick="return confirm('Supprimer la notion « <?= htmlspecialchars($n['mot'], ENT_QUOTES) ?> » ?')">
                                🗑️ Supprimer
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<footer>GlossaireProf &copy; <?= date('Y') ?> — Base de données CEJM</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>