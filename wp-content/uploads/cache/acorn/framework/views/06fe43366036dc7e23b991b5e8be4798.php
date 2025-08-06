<!doctype html>
<html <?php (language_attributes()); ?>>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <?php (do_action('get_header')); ?>
    <?php (wp_head()); ?>

    <?php if( is_singular('post') ): ?>
      <?php echo app('Illuminate\Foundation\Vite')([
        'resources/js/single.js'
      ]); ?>
    <?php endif; ?>

    <?php echo app('Illuminate\Foundation\Vite')(['resources/styles/app.scss', 'resources/js/app.js']); ?>
  </head>

  <body <?php (body_class()); ?>>
    <?php (wp_body_open()); ?>

    <div id="app">
      <a class="skip-link" href="#main">
        <?php echo e(__('Skip to content', 'sage')); ?>

      </a>

      <?php echo $__env->make('sections.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

      <main id="main" class="main">
        <?php echo $__env->yieldContent('content'); ?>
      </main>

      <?php if (! empty(trim($__env->yieldContent('sidebar')))): ?>
        <aside class="sidebar">
          <?php echo $__env->yieldContent('sidebar'); ?>
        </aside>
      <?php endif; ?>

      <?php echo $__env->make('sections.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>

    <?php (do_action('get_footer')); ?>
    <?php (wp_footer()); ?>
  </body>
</html>
<?php /**PATH /Users/balendra/Local Sites/wordpress/app/public/wp-content/themes/sage/resources/views/layouts/app.blade.php ENDPATH**/ ?>