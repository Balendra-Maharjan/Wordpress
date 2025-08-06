<?php
namespace App\View\Components;

use Illuminate\View\Component;

class Image extends Component
{
    public $image;
    public $isLeadspace;
    public $imageSizes;
    public $figureClass;
    public $imageClass;
    public $fetchPriorityString;
    public $width;
    public $height;
    public $showWidthHeight;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        $imageField = '',
        $imageSourceType = '',
        $isLeadspace = '',
        $imageSizes = '100vw',
        $figureClass = '',
        $imageClass = '',
        $showWidthHeight = false
    ) {
        $this->image = sage_get_image_data( $imageField, $imageSourceType );
        $this->isLeadspace = $isLeadspace;
        $this->imageSizes = $imageSizes;
        $this->figureClass = $figureClass;
        $this->imageClass = $imageClass;
        $this->fetchPriorityString = ( $isLeadspace == true ) ? "fetchpriority='high'" : '';
        if($imageSourceType == 'acf'){
            $this->width = $imageField['width'] ? $imageField['width'] : '';
            $this->height = $imageField['height'] ? $imageField['height'] : '';
        }
        $this->showWidthHeight = $showWidthHeight;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.image');
    }
}
