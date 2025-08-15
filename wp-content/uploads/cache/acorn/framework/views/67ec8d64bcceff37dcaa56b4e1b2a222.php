<?php $__env->startSection('content'); ?>
  <?php echo $__env->make('partials.page-header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

  <?php if(is_front_page() || is_home()): ?>
    <div id="project-posts">
      <?php
        $args = [
            'post_type' => 'blog',
            'posts_per_page' => 4,
            'paged' => 1,
        ];
        $query = new WP_Query($args);
      ?>

      <?php if($query->have_posts()): ?>
        <?php while($query->have_posts()): ?> 
          <?php $query->the_post(); ?>
          <article>
            <h2><a href="<?php echo e(get_permalink()); ?>"><?php echo e(get_the_title()); ?></a></h2>
            <div class="excerpt"><?php echo get_the_excerpt(); ?></div>
          </article>
        <?php endwhile; ?>
      <?php endif; ?>

      <?php wp_reset_postdata(); ?>
    </div>

    <button id="load-more" data-page="2">Load More</button>
  <?php endif; ?>
  <?php if(! have_posts()): ?>
    <?php if (isset($component)) { $__componentOriginal5194778a3a7b899dcee5619d0610f5cf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5194778a3a7b899dcee5619d0610f5cf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.alert','data' => ['type' => 'warning']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'warning']); ?>
      <?php echo __('Sorry, no results were found.', 'sage'); ?>

     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5194778a3a7b899dcee5619d0610f5cf)): ?>
<?php $attributes = $__attributesOriginal5194778a3a7b899dcee5619d0610f5cf; ?>
<?php unset($__attributesOriginal5194778a3a7b899dcee5619d0610f5cf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5194778a3a7b899dcee5619d0610f5cf)): ?>
<?php $component = $__componentOriginal5194778a3a7b899dcee5619d0610f5cf; ?>
<?php unset($__componentOriginal5194778a3a7b899dcee5619d0610f5cf); ?>
<?php endif; ?>

    <?php echo get_search_form(false); ?>

  <?php endif; ?>

<?php $__env->stopSection(); ?>


<?php $__env->startSection('sidebar'); ?>
  <?php echo $__env->make('sections.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>
<!-- <script>
  document.addEventListener('DOMContentLoaded', () => {
  console.log("app.js loaded");
    const loadMoreBtn = document.getElementById("load-more");
    console.log(loadMoreBtn);
    
              if (!loadMoreBtn) return;
  
              loadMoreBtn.addEventListener("click", function () {
                  const button = this;
                  const page = parseInt(button.dataset.page);
  
                  const data = new FormData();
                  data.append("action", "load_more_projects");
                  data.append("paged", page);
  
                  fetch("<?php echo admin_url('admin-ajax.php'); ?>", {
                      method: "POST",
                      body: data,
                  })
                      .then((res) => res.json())
                      .then((res) => {
                          if (res.success) {
                              const postsContainer = document.getElementById("project-posts");
                              postsContainer.insertAdjacentHTML("beforeend", res.data.html);
                              button.dataset.page = page + 1;
  
                              if (!res.data.has_more) {
                                  button.style.display = "none";
                              }
                          }
                      });
              });
    
  
  });
  
  


</script> -->


 

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/balendra/Local Sites/wordpress/app/public/wp-content/themes/sage/resources/views/index.blade.php ENDPATH**/ ?>