<?php
require __DIR__ . '/../functions/app.php';


try {
   $categories = getCategories();
} catch (Exception $e) {
   header("Location: /src/500.php");
   exit();
}

// Set page title for header template
$page_title = 'Категории';
include __DIR__ . '/../templates/header.php';
?>

<h2>Категории</h2>
<?php if (isset($categories) && count($categories) > 0): ?>
   <?php foreach ($categories as $category): ?>
       <div>
           <h3>
               <a href="/src/posts-category.php?category=<?= htmlspecialchars($category['slug']) ?>">
                   <?= htmlspecialchars($category['name']) ?>
               </a>
           </h3>
           <h4><?= htmlspecialchars($category['description']) ?></h4>
       </div>
   <?php endforeach; ?>
<?php else: ?>
   <p>Ошибка загрузки категорий.</p>
<?php endif; ?>

<?php include __DIR__ . '/../templates/footer.php'; ?>