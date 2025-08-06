<article <?php (post_class()); ?>>
  <header>
    <h1 class="entry-title">
      <?php echo $title; ?>

    </h1>

    <?php echo $__env->make('partials.entry-meta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  </header>

  <div class="entry-content">
    <?php (the_content()); ?>
  </div>

  <footer>
    <?php echo wp_link_pages(['echo' => 0, 'before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'), 'after' => '</p></nav>']); ?>

  </footer>

  <?php (comments_template()); ?>
</article>
<?php /**PATH /Users/balendra/Local Sites/wordpress/app/public/wp-content/themes/sage/resources/views/partials/content-single.blade.php ENDPATH**/ ?>