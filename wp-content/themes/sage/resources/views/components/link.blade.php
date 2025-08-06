{{--

    /**
    * Link Component
    *
    * @param string $linkURL The URL for the link.
    * @param string $linkText The text for the link.
    * @param string $linkTarget The target attribute for the link (e.g., _blank).
    * @param string $linkStyle The CSS class for the link style.
    * @param string $linkStyleDefaultIcon The default icon for the link style.
    * @param string $linkSize The size of the link (e.g., small, large).
    * @param string $linkAriaLabel The aria-label for accessibility.
    *
    * @return \Illuminate\View\View
    */

    Syntax
    <x-link
    :linkURL="$ctaButton['url']"
    :linkText="$ctaButton['text']"
    :linkTarget="$ctaButton['target']"
    :linkStyle="$ctaButton['buttonStyle']"
    :linkStyleDefaultIcon="$ctaButton['buttonStyleLinkIcon']"
    :linkAriaLabel="$ctaButton['text']"
    ></x-link>

--}}

@if( $linkURL && $linkText )
    <a {{ $attributes }}
        href="{{ $linkURL }}"
        target="{{ $linkTarget }}"
        @if($linkAriaLabel)
            aria-label="{{ $linkAriaLabel }}"
        @endif
        class="btn btn-{{ $linkStyle }}">
        {!! $linkText !!}
        @if($linkStyleDefaultIcon)
            <i class='{!! $linkStyleDefaultIcon !!}' aria-hidden="true"></i>
        @endif
        {!! $slot !!}
    </a>
@endif
