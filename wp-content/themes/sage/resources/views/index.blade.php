
@extends('layouts.app')

@section('content')
  @include('partials.page-header')

  @if (is_front_page() || is_home())
    <div id="project-posts" class="container py-5">
      @php
        $args = [
            'post_type' => 'blog',
            'posts_per_page' => 4,
            'paged' => 1,
        ];
        $query = new WP_Query($args);
      @endphp

      @if ($query->have_posts())
        <div class="row-cols-1">
          @php $post_index = 0; @endphp
          @while ($query->have_posts()) 
            @php $query->the_post(); @endphp
            @include('partials.content-blog', ['index' => $post_index, 'paged' => 1])
            @php $post_index++; @endphp
          @endwhile
        </div>
      @endif

      @php wp_reset_postdata(); @endphp
    </div>
    <div class="container pb-5 d-flex justify-content-center">
      <button id="load-more" data-page="2" class="btn btn-outline-primary">Load More</button>
    </div>
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



 
