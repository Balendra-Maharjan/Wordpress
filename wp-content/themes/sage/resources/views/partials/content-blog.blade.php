<article @php post_class('mb-8') @endphp>
  <div class="post-thumbnail mb-4">
    @if (has_post_thumbnail())
      {!! get_the_post_thumbnail(null, 'medium', [
        'class' => 'w-full h-auto rounded-lg shadow-sm',
        'loading' => (isset($index) && $index === 0 && isset($paged) && $paged === 1) ? 'eager' : 'lazy',
        'fetchpriority' => (isset($index) && $index === 0 && isset($paged) && $paged === 1) ? 'high' : 'auto'
      ]) !!}
    @else
      <img src="{{ get_template_directory_uri() }}/images/default.jpg" alt="Default Image" class="w-full h-auto rounded-lg shadow-sm">
    @endif
  </div>
  
  <h2 class="text-2xl font-bold mb-2">
    <a href="{{ get_permalink() }}" class="hover:text-blue-600 transition-colors">
      {{ get_the_title() }}
    </a>
  </h2>
  
  <div class="excerpt text-gray-600">
    @php the_excerpt() @endphp
  </div>
</article>
