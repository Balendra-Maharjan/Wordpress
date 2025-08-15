
@extends('layouts.app')

@section('content')
  @include('partials.page-header')

  @if (is_front_page() || is_home())
    <div id="project-posts">
      @php
        $args = [
            'post_type' => 'blog',
            'posts_per_page' => 4,
            'paged' => 1,
        ];
        $query = new WP_Query($args);
      @endphp

      @if ($query->have_posts())
        @while ($query->have_posts()) 
          @php $query->the_post(); @endphp
          <article>
            <h2><a href="{{ get_permalink() }}">{{ get_the_title() }}</a></h2>
            <div class="excerpt">{!! get_the_excerpt() !!}</div>
          </article>
        @endwhile
      @endif

      @php wp_reset_postdata(); @endphp
    </div>

    <button id="load-more" data-page="2">Load More</button>
  @endif
  @if (! have_posts())
    <x-alert type="warning">
      {!! __('Sorry, no results were found.', 'sage') !!}
    </x-alert>

    {!! get_search_form(false) !!}
  @endif

@endsection


@section('sidebar')
  @include('sections.sidebar')
@endsection
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


 
