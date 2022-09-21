<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cayuse Connect</title>

    <script async src="https://cdn.jsdelivr.net/npm/es-module-shims@1/dist/es-module-shims.min.js" crossorigin="anonymous"></script>
    <script type="importmap">
        {
            "imports": {
                "vue": "https://unpkg.com/vue@3/dist/vue.esm-browser.js",
                "@popperjs/core": "https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js",
                "bootstrap": "https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.esm.min.js",
                "@fortawesome/fontawesome-svg-core": "https://ga.jspm.io/npm:@fortawesome/fontawesome-svg-core@6.2.0/index.mjs",
                "@fortawesome/free-brands-svg-icons": "https://ga.jspm.io/npm:@fortawesome/free-brands-svg-icons@6.2.0/index.mjs",
                "@fortawesome/free-regular-svg-icons": "https://ga.jspm.io/npm:@fortawesome/free-regular-svg-icons@6.2.0/index.mjs",
                "@fortawesome/free-solid-svg-icons": "https://ga.jspm.io/npm:@fortawesome/free-solid-svg-icons@6.2.0/index.mjs",
                "@fortawesome/vue-fontawesome": "https://ga.jspm.io/npm:@fortawesome/vue-fontawesome@3.0.1/index.es.js"
            }
        }
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= $request->getBasePath() ?>/css/app.css?v=<?= APP_VERSION ?>">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'><text x='0' y='14'>🐴</text></svg>" type="image/svg+xml">
</head>
<body>

<main id="app" class="app container mt-3">
    <h1 class="display-4 text-center">
        <icon icon="fa-solid fa-building-columns"></icon> <?= getenv('APP_NAME') ?? 'Us' ?> <icon icon="fa-solid fa-left-right"></icon> Cayuse <icon icon="fa-solid fa-horse"></icon>
    </h1>

    <hr>

    <section class="user-search my-5">
        <h2 class="display-5 text-center">Users</h2>

        <h3>Search users</h3>
        <user-search></user-search>
    </section>

    <section class="user-training-search my-5">
        <h2 class="display-5 text-center">Trainings</h2>

        <h3>Search trainings</h3>
        <user-training-search></user-training-search>
        
        <h3>Bulk load trainings</h3>
        <user-training-load></user-training-load>

    </section>

</main>

<script>
    var user_search_url = "<?= $request->getBasePath() ?>/api/v1/user_search";
    var user_training_search_url = "<?= $request->getBasePath() ?>/api/v1/user_training_search";
</script>
<script type="module" src="<?= $request->getBasePath() ?>/js/app.js?v=<?= APP_VERSION ?>"></script>
</body>
</html>