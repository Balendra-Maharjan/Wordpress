{{--
  Template Name: Filter
--}}

@extends('layouts.app')

@section('content')
  @while(have_posts()) @php(the_post())
    @include('partials.page-header')
    <div class="container mx-auto py-8">
  <div class="bg-white p-6 rounded-lg shadow-lg mb-8">
    <form id="post-filter-form" class="grid grid-cols-1 md:grid-cols-3 gap-6" method="GET" action="">
      <input type="hidden" name="action" value="filter_and_paginate">
      <input type="hidden" name="nonce" value="{{ wp_create_nonce('filter_nonce') }}">
      <input type="hidden" name="paged" value="1">
      
      <!-- Search -->
      <div class="form-group">
        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
        <input type="text" name="search" id="search"
          class="form-input w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
          value="{{ $search_query ?? '' }}">
      </div>
      
      <!-- Category -->
      <div class="form-group">
        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
        <select name="category" id="category"
          class="form-select w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
          <option value="">All</option>
          @foreach(get_categories() as $category)
            <option value="{{ $category->slug }}" @if(isset($category_slug) && $category_slug == $category->slug) selected @endif>
              {{ $category->name }}
            </option>
          @endforeach
        </select>
      </div>
      
      <!-- Submit -->
      <div class="form-group md:col-span-3 flex justify-end">
        <button type="submit" class="btn bg-blue-600 text-white hover:bg-blue-700 rounded-md px-4 py-2">Filter</button>
      </div>
    </form>
  </div>

  <!-- Results -->
  <div id="filter-results-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 min-h-[400px]">
    <?php
    $posts_per_page = get_field('no_of_post_per_page', 'option');

      $args = [
        'post_type' => 'blog',
        'posts_per_page' => $posts_per_page,
        'paged' => 1,
      ];
      $query = new WP_Query($args);

      if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post(); ?>
          <article>
            <h2><a href="<?= esc_url(get_permalink()); ?>"><?= get_the_title(); ?></a></h2>
            <div class="entry-summary"><?= get_the_excerpt(); ?></div>
          </article>
        <?php endwhile;
      else :
        echo '<p class="col-span-full text-center">No posts found.</p>';
      endif;
    ?>
  </div>

  <!-- Pagination -->
  <div id="pagination-container" class="mt-8 flex justify-center">
    <?php
      // Example: your custom pagination function
      if (function_exists('sage_custom_pagination')) {
        echo sage_custom_pagination($query, 1);
      }
    ?>
  </div>
</div>

  @endwhile
@endsection