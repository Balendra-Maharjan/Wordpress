<article <?php post_class('mb-8') ?>>
  <div class="post-thumbnail mb-4">
    <?php if(has_post_thumbnail()): ?>
      <?php echo get_the_post_thumbnail(null, 'medium', [
        'class' => 'w-full h-auto rounded-lg shadow-sm',
        'loading' => (isset($index) && $index === 0 && isset($paged) && $paged === 1) ? 'eager' : 'lazy',
        'fetchpriority' => (isset($index) && $index === 0 && isset($paged) && $paged === 1) ? 'high' : 'auto'
      ]); ?>

    <?php else: ?>
      <img src="<?php echo e(get_template_directory_uri()); ?>/images/default.jpg" alt="Default Image" class="w-full h-auto rounded-lg shadow-sm">
    <?php endif; ?>
  </div>
  
  <h2 class="text-2xl font-bold mb-2">
    <a href="<?php echo e(get_permalink()); ?>" class="hover:text-blue-600 transition-colors">
      <?php echo e(get_the_title()); ?>

    </a>
  </h2>
  
  <div class="excerpt text-gray-600">
    <?php the_excerpt() ?>
  </div>
</article>
<?php /**PATH /Users/balendra/Local Sites/wordpress/app/public/wp-content/themes/sage/resources/views/partials/content-blog.blade.php ENDPATH**/ ?>