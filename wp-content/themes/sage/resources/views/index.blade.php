
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
          @while ($query->have_posts()) 
            @php $query->the_post(); @endphp
            <article>
              @php static $post_index = 0; @endphp
              @if (has_post_thumbnail())
                <div class="post-thumbnail">
                  {!! get_the_post_thumbnail(null, 'medium', $post_index === 0 ? ['loading' => 'eager', 'fetchpriority' => 'high'] : []) !!}
                </div>
              @endif
              <h2><a href="{{ get_permalink() }}">{{ get_the_title() }}</a></h2>
              <div class="excerpt">{!! get_the_excerpt() !!}</div>
              @php $post_index++; @endphp
            </article>
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



 
