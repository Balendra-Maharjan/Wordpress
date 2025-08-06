{{--
  Template Name: Filter
--}}

@extends('layouts.app')

@section('content')
  @while(have_posts()) @php(the_post())
    @include('partials.page-header')
    <div class="container">
      <div class="filter-form">
        <form id="post-filter-form" method="GET" action="">
          <input type="hidden" name="action" value="filter_and_paginate">
          <input type="hidden" name="nonce" value="{{ wp_create_nonce('filter_and_paginate') }}">
          <input type="hidden" name="paged" value="1">
          <div class="form-group">
            <label for="search">Search</label>
            <input type="text" name="search" id="search" class="form-control" value="{{ $search_query ?? '' }}">
          </div>
          <div class="form-group">
            <label for="category">Category</label>
            <select name="category" id="category" class="form-control">
              <option value="">All</option>
              @foreach(get_categories() as $category)
                <option value="{{ $category->slug }}" @if(isset($category_slug) && $category_slug == $category->slug) selected @endif>{{ $category->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <button type="submit" class="btn btn-primary">Filter</button>
          </div>
        </form>
      </div>

      <div id="filter-results-container">
        {{-- Posts will be loaded here via AJAX --}}
      </div>

      <div id="pagination-container">
        {{-- Pagination will be loaded here via AJAX --}}
      </div>
    </div>
  @endwhile
@endsection