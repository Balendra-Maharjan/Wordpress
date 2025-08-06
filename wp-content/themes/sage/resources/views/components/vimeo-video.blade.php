    {{--
        /**
        * Vimeo Video Component
        *
        * @param string $videoUrl The full Vimeo player URL (e.g., https://player.vimeo.com/video/1077466257).
        * @param bool $autoplay Whether the video should autoplay (default false).
        * @param bool $loop Whether the video should loop (default false).
        * @param bool $title Whether to show the video title (default true).
        * @param bool $byline Whether to show the byline (default true).
        * @param bool $portrait Whether to show the portrait image (default true).
        *
        * @return \Illuminate\View\View
        *
        * Example Vimeo URL: https://player.vimeo.com/video/1077466257
        */

        Syntax
        <x-vimeo-video
            video-url="https://player.vimeo.com/video/1077466257"
            autoplay="true"
            loop="true"
            title="false"
            byline="false" // Show/hide the name of the video creator (the author's name).
            portrait="false" // Show/hide the profile picture of the video creator (small photo).
            custom-controls="true" // Show/hide the profile picture of the video creator (small photo).
        />
    --}}

    <div class="c-vimeo-video js-vimeo-video-container ratio ratio-{{ $aspectRatio }}" {{ $attributes }}>
        <iframe
        id="vimeo-player-{{ $videoId }}"
            src="{!! $videoUrl !!}?autoplay={{ $autoplay ? 1 : 0 }}&muted={{ $muted ? 1 : 0 }}&loop={{ $loop }}&title={{ $title }}&byline={{ $byline }}&portrait={{ $portrait }}&controls={{ $customControls ? 0 : 1 }}"
            frameborder="0"
            allow="autoplay; fullscreen; picture-in-picture"
            allowfullscreen>
        </iframe>
        @if($customControls)
            {{-- TODO:: Add Acessibility for vimeo video component --}}
            <div class="c-vimeo-video__controls js-vimeo-video-control">
                <button class="c-vimeo-video__controls--button" data-vimeo-toggle="{{ $videoId }}">
                    <span class="c-vimeo-video__controls--play-icon js-vimeo-video-play-icon icon-play"></span>
                    <span class="c-vimeo-video__controls--pause-icon js-vimeo-video-pause-icon icon-pause" style="display: none;"></span>
                </button>
            </div>
            <script defer>
                 {{-- TODO:: Check if we can optimize this  --}}
                document.addEventListener('DOMContentLoaded', function() {
                    document.dispatchEvent(new CustomEvent('vimeoVideoAdded', {
                        detail: { videoId: '{{ $videoId }}' }
                    }));
                });
            </script>
            @endif
    </div>
