{{--
  Template Name: Advanced Pagination
--}}

@extends('layouts.app')

@section('content')
  @include('partials.page-header')

  <div class="container mx-auto py-8 px-4">
    <!-- Main Header -->
    <div class="text-center mb-10">
      <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl">
        Advanced Sliding Pagination
      </h1>
      <p class="mt-3 max-w-2xl mx-auto text-xl text-gray-500 sm:mt-4">
        Showing 1 post per page to demonstrate dynamic sliding page numbers.
      </p>
    </div>

    <!-- Results Container -->
    <div id="pagination-results-container" class="max-w-2xl mx-auto min-h-[300px]">
      @php
        $paged = request('paged') ? intval(request('paged')) : 1;
        $posts_per_page = 1; // 1 post per page to force 15 pages

        $query = new WP_Query([
          'post_type' => 'blog',
          'posts_per_page' => $posts_per_page,
          'paged' => $paged,
          'post_status' => 'publish',
        ]);
        $post_index = 0;
      @endphp

      @if ($query->have_posts())
        @while ($query->have_posts()) @php $query->the_post(); @endphp
          @include('partials.content-blog', ['index' => $post_index, 'paged' => $paged])
          @php $post_index++; @endphp
        @endwhile
        @php wp_reset_postdata(); @endphp
      @else
        <p class="text-center py-10 text-gray-500">No blog posts found.</p>
      @endif
    </div>

    <!-- Pagination Links -->
    <div id="dynamic-pagination-container" class="mt-12 flex justify-center">
      @if (function_exists('sage_custom_pagination'))
        {!! sage_custom_pagination($query, $paged) !!}
      @endif
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const resultsContainer = document.getElementById('pagination-results-container');
      const paginationContainer = document.getElementById('dynamic-pagination-container');

      if (!resultsContainer || !paginationContainer) return;

      const postsPerPage = 1; // Match template posts_per_page limit

      // Helper to extract page number from URL
      const getPageFromUrl = (url) => {
        const match = url.match(/[?&]paged=(\d+)/);
        return match ? parseInt(match[1], 10) : 1;
      };

      // Update browser URL
      const updateUrl = (page) => {
        const newUrl = page > 1 ? `?paged=${page}` : window.location.pathname;
        window.history.pushState({ paged: page }, '', newUrl);
      };

      // Fetch and render posts
      const fetchPage = async (page, skipPushState = false) => {
        // Show loading transition
        resultsContainer.style.opacity = '0.5';

        const formData = new FormData();
        formData.append('action', 'ajax_pagination');
        formData.append('paged', page);
        formData.append('posts_per_page', postsPerPage);

        const ajaxUrl = window.filter_params ? filter_params.ajax_url : '/wp-admin/admin-ajax.php';

        try {
          const response = await fetch(ajaxUrl, {
            method: 'POST',
            body: formData,
          });

          const result = await response.json();

          if (result.success) {
            resultsContainer.innerHTML = result.data.html;
            paginationContainer.innerHTML = result.data.pagination;

            if (!skipPushState) {
              updateUrl(page);
            }
          }
        } catch (error) {
          console.error('AJAX Pagination Error:', error);
        } finally {
          resultsContainer.style.opacity = '1';
        }
      };

      // Listen to pagination link clicks
      paginationContainer.addEventListener('click', (e) => {
        const link = e.target.closest('a');
        if (link && link.classList.contains('pagination-link')) {
          e.preventDefault();
          const targetPage = getPageFromUrl(link.href);
          fetchPage(targetPage);
        }
      });

      // Handle back/forward navigation
      window.addEventListener('popstate', (e) => {
        const page = e.state && e.state.paged ? e.state.paged : getPageFromUrl(window.location.href);
        fetchPage(page, true);
      });

      // Initialize state
      window.history.replaceState({ paged: getPageFromUrl(window.location.href) }, '');
    });
  </script>
@endsection
