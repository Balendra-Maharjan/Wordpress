<?php $__env->startSection('content'); ?>
  <?php echo $__env->make('partials.page-header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

  <div class="container mx-auto py-8">
    <div id="infinite-posts-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <?php
        $posts_per_page = 2;
        $query = new WP_Query([
          'post_type' => 'blog',
          'posts_per_page' => $posts_per_page,
          'paged' => 1,
        ]);
        $post_index = 0;
      ?>

      <?php if($query->have_posts()): ?>
        <?php while($query->have_posts()): ?> <?php $query->the_post(); ?>
          <?php echo $__env->make('partials.content-blog', ['index' => $post_index, 'paged' => 1], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
          <?php $post_index++; ?>
        <?php endwhile; ?>
        <?php wp_reset_postdata(); ?>
      <?php else: ?>
        <p class="col-span-full text-center">No posts found.</p>
      <?php endif; ?>
    </div>

    
    <div id="infinite-scroll-anchor" class="py-10 flex justify-center" data-page="2" data-max-pages="<?php echo e($query->max_num_pages); ?>">
      <div class="loader-spinner hidden">
        <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </div>
    </div>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/balendra/Local Sites/wordpress/app/public/wp-content/themes/sage/resources/views/template-infinite.blade.php ENDPATH**/ ?>