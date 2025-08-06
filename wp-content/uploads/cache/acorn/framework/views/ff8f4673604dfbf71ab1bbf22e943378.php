<?php $__env->startSection('content'); ?>
  <?php while(have_posts()): ?> <?php (the_post()); ?>
    <?php echo $__env->make('partials.page-header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="container">
      <div class="filter-form">
        <form id="post-filter-form" method="GET" action="">
          <input type="hidden" name="action" value="filter_and_paginate">
          <input type="hidden" name="nonce" value="<?php echo e(wp_create_nonce('filter_and_paginate')); ?>">
          <input type="hidden" name="paged" value="1">
          <div class="form-group">
            <label for="search">Search</label>
            <input type="text" name="search" id="search" class="form-control" value="<?php echo e($search_query ?? ''); ?>">
          </div>
          <div class="form-group">
            <label for="category">Category</label>
            <select name="category" id="category" class="form-control">
              <option value="">All</option>
              <?php $__currentLoopData = get_categories(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($category->slug); ?>" <?php if(isset($category_slug) && $category_slug == $category->slug): ?> selected <?php endif; ?>><?php echo e($category->name); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          <div class="form-group">
            <button type="submit" class="btn btn-primary">Filter</button>
          </div>
        </form>
      </div>

      <div id="filter-results-container">
        
      </div>

      <div id="pagination-container">
        
      </div>
    </div>
  <?php endwhile; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/balendra/Local Sites/wordpress/app/public/wp-content/themes/sage/resources/views/template-filter.blade.php ENDPATH**/ ?>