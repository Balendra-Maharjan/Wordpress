@extends('layouts.app')

@section('content')
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
            value="{{ request('search') }}">
        </div>

        <!-- Category -->
        <div class="form-group">
          <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
          <select name="category" id="category"
            class="form-select w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            <option value="">All</option>
            @foreach(get_categories() as $category)
              <option value="{{ $category->slug }}" {{ request('category') === $category->slug ? 'selected' : '' }}>
                {{ $category->name }}
              </option>
            @endforeach
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
      @php
        $posts_per_page = get_field('no_of_post_per_page', 'option') ?: 6;
        $query = new WP_Query([
          'post_type' => 'blog',
          'posts_per_page' => $posts_per_page,
          'paged' => 1,
        ]);
        $post_index = 0;
      @endphp

      @if ($query->have_posts())
        @while ($query->have_posts()) @php $query->the_post(); @endphp
          <article>
            @if (has_post_thumbnail())
              <div class="post-thumbnail">
                {!! get_the_post_thumbnail(
                  get_the_ID(),
                  'medium',
                  $post_index === 0 ? ['loading' => 'eager', 'fetchpriority' => 'high'] : []
                ) !!}
              </div>
            @endif
            <h2><a href="{{ get_permalink() }}">{{ get_the_title() }}</a></h2>
            <div class="entry-summary">{!! get_the_excerpt() !!}</div>
          </article>
          @php $post_index++; @endphp
        @endwhile
        @php wp_reset_postdata(); @endphp
      @else
        <p class="col-span-full text-center">No posts found.</p>
      @endif
    </div>

    <!-- Pagination -->
    <div id="pagination-container" class="mt-8 flex justify-center">
      @if (function_exists('sage_custom_pagination'))
        {!! sage_custom_pagination($query, 1) !!}
      @endif
    </div>
  </div>
@endsection
