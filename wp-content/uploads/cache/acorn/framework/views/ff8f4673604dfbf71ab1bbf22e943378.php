<?php $__env->startSection('content'); ?>
  <?php echo $__env->make('partials.page-header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

  <div class="container mx-auto py-8">
    <div class="bg-white p-6 rounded-lg shadow-lg mb-8">
      <form id="post-filter-form" class="grid grid-cols-1 md:grid-cols-3 gap-6" method="GET" action="">
        <input type="hidden" name="action" value="filter_and_paginate">
        <input type="hidden" name="nonce" value="<?php echo e(wp_create_nonce('filter_nonce')); ?>">
        <input type="hidden" name="paged" value="1">

        <!-- Search -->
        <div class="form-group">
          <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
          <input type="text" name="search" id="search"
            class="form-input w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            value="<?php echo e(request('search')); ?>">
        </div>

        <!-- Category -->
        <div class="form-group">
          <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
          <select name="category" id="category"
            class="form-select w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            <option value="">All</option>
            <?php $__currentLoopData = get_categories(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($category->slug); ?>" <?php echo e(request('category') === $category->slug ? 'selected' : ''); ?>>
                <?php echo e($category->name); ?>

              </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>

        <!-- Submit -->
        <div class="form-group md:col-span-3 flex justify-end">
          <button type="submit" class="btn btn-primary">Filter</button>
        </div>
      </form>
    </div>

    <!-- Results -->
    <div id="filter-results-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 min-h-[400px]">
      <?php
        $posts_per_page = get_field('no_of_post_per_page', 'option') ?: 6;
        $query = new WP_Query([
          'post_type' => 'blog',
          'posts_per_page' => $posts_per_page,
          'paged' => 1,
        ]);
        $post_index = 0;
      ?>

      <?php if($query->have_posts()): ?>
        <?php while($query->have_posts()): ?> <?php $query->the_post(); ?>
          <article>
            <?php if(has_post_thumbnail()): ?>
              <div class="post-thumbnail">
                <?php echo get_the_post_thumbnail(
                  get_the_ID(),
                  'medium',
                  $post_index === 0 ? ['loading' => 'eager', 'fetchpriority' => 'high'] : []
                ); ?>

              </div>
            <?php endif; ?>
            <h2><a href="<?php echo e(get_permalink()); ?>"><?php echo e(get_the_title()); ?></a></h2>
            <div class="entry-summary"><?php echo get_the_excerpt(); ?></div>
          </article>
          <?php $post_index++; ?>
        <?php endwhile; ?>
        <?php wp_reset_postdata(); ?>
      <?php else: ?>
        <p class="col-span-full text-center">No posts found.</p>
      <?php endif; ?>
    </div>

    <!-- Pagination -->
    <div id="pagination-container" class="mt-8 flex justify-center">
      <?php if(function_exists('sage_custom_pagination')): ?>
        <?php echo sage_custom_pagination($query, 1); ?>

      <?php endif; ?>
    </div>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/balendra/Local Sites/wordpress/app/public/wp-content/themes/sage/resources/views/template-filter.blade.php ENDPATH**/ ?>