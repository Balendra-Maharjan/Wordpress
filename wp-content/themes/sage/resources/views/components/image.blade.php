{{--
    * Image component for Origami Risk
    *
    * @param string $imageClass Class for the image element
    * @param string $figureClass Class for the figure element
    * @param string $imageSizes Sizes attribute for the image element
    * @param array $image Image data containing src, srcset, and alt attributes
    * @param bool $isLeadspace Flag to determine if the image is in a leadspace
    * @param string $attribute takes all the attributes except props

    syntax:

    <x-image
        :imageField="$imageField"
        :imageSourceType="$imageSourceType"
        isLeadspace='true'
        imageSizes="100vw"
        figureClass="mb-0 thumb-img"
        imageClass="w-100 h-100"
        data-foo="foo-image"
        width="{!! $card['width'] !!}"
        height="{!! $card['height'] !!}"
        showWidthHeight="true"

    />

--}}

<figure class="{{ $figureClass }}" {{ $attributes }}>
    <img
        {{ $fetchPriorityString }}
        loading="{{ ( $isLeadspace == true ) ? 'eager' : 'lazy' }}"
        class="{{ $imageClass }}"
        src="{{ esc_url($image['imageSrc']) }}"
        srcset="{{ $image['imageSrcSet'] }}"
        sizes="{{ $imageSizes }}"
        @if($showWidthHeight == true)
            @if (isset($width) && !empty($width))
                width="{{ $width }}"
            @endif
            @if (isset($height) && !empty($height))
                height="{{ $height }}"
            @endif
        @endif
        alt="{{ $image['imageAlt'] }}" />
</figure>
