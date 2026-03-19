<?php
require_once 'config.php';
$erreurs = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mot        = trim(isset($_POST['mot']) ? $_POST['mot'] : '');
    $definition = trim(isset($_POST['definition']) ? $_POST['definition'] : '');

    if ($mot === '')        $erreurs[] = "Le mot est obligatoire.";
    if ($definition === '') $erreurs[] = "La définition est obligatoire.";
    if (strlen($definition) > 600) $erreurs[] = "La définition ne doit pas dépasser 600 caractères.";

    if (empty($erreurs)) {
        $stmt = $pdo->prepare("INSERT INTO notion (mot, definition) VALUES (?, ?)");
        $stmt->execute([$mot, $definition]);
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <title>Ajouter une notion – ReviserCEJM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <style>
        body { background: #f0f6ff; font-family: 'Segoe UI', sans-serif; }
        .navbar { background: #1a3a6c !important; }
        .navbar-brand, .nav-link { color: #fff !important; }
        .nav-link:hover { color: #93c5fd !important; }
        .page-header { background: linear-gradient(135deg, #1a3a6c, #2563eb); color: #fff; padding: 40px 0; }
        .page-header h1 { font-size: 2rem; font-weight: 700; }
        .form-card { background: #fff; border: 1px solid #dbeafe; border-radius: 16px; padding: 36px; max-width: 620px; margin: 0 auto; }
        .form-label { font-weight: 600; color: #1e293b; }
        .form-control:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,.15); }
        .char-counter { font-size: .8rem; color: #94a3b8; text-align: right; }
        .btn-submit { background: #2563eb; color: #fff; border: none; border-radius: 8px; padding: 10px 28px; font-weight: 600; font-size: 1rem; width: 100%; transition: background .2s; }
        .btn-submit:hover { background: #1d4ed8; }
        footer { background: #1a3a6c; color: rgba(255,255,255,.5); text-align: center; padding: 20px; font-size: .85rem; margin-top: 60px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">Reviser<span style="color:#93c5fd">CEJM</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav ms-auto gap-2">
                <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                <li class="nav-item"><a class="nav-link" href="notions.php">Toutes les notions</a></li>
                <li class="nav-item"><a class="nav-link active" href="ajouter.php">Ajouter</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="page-header">
    <div class="container">
        <h1>✏️ Ajouter une notion</h1>
        <p class="mb-0" style="opacity:.75">Remplissez le formulaire pour enrichir le glossaire.</p>
    </div>
</div>

<div class="container py-5">
    <div class="form-card">

        <?php if ($success): ?>
            <div class="alert alert-success">✅ Notion ajoutée avec succès ! <a href="notions.php">Voir toutes les notions</a></div>
        <?php endif; ?>

        <?php if (!empty($erreurs)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($erreurs as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="ajouter.php" novalidate>
            <div class="mb-4">
                <label for="mot" class="form-label">Mot / Notion</label>
                <input type="text" id="mot" name="mot" class="form-control"
                       placeholder="Ex : Mondialisation"
                       value="<?= htmlspecialchars(isset($_POST['mot']) ? $_POST['mot'] : '') ?>"
                       maxlength="100" required/>
            </div>

            <div class="mb-4">
                <label for="definition" class="form-label">Définition</label>
                <textarea id="definition" name="definition" class="form-control" rows="6"
                          placeholder="Rédigez ici la définition complète…"
                          maxlength="600"><?= htmlspecialchars(isset($_POST['definition']) ? $_POST['definition'] : '') ?></textarea>
                <div class="char-counter"><span id="count">0</span>/600 caractères</div>
            </div>

            <button type="submit" class="btn-submit">➕ Ajouter la notion</button>
        </form>

        <div class="text-center mt-3">
            <a href="notions.php" style="color:#2563eb;font-size:.9rem;">← Retour à la liste</a>
        </div>
    </div>
</div>

<footer>ReviserCEJM &copy; <?= date('Y') ?> — Base de données CEJM</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const def = document.getElementById('definition');
    const count = document.getElementById('count');
    const update = () => count.textContent = def.value.length;
    def.addEventListener('input', update);
    update();
</script>
</body>
</html>