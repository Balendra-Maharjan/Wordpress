<?php
namespace App\View\Components;

use Illuminate\View\Component;

class Link extends Component
{
    public $linkText;
    public $linkURL;
    public $linkTarget;
    public $linkStyle;
    public $linkSize;
    public $linkStyleDefaultIcon;
    public $linkAriaLabel;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct( $linkText='', $linkURL = '', $linkTarget = '_self', $linkStyle = '', $linkSize = '', $linkStyleDefaultIcon = '', $linkAriaLabel = '' ) {
        $this->linkText = $linkText;
        $this->linkURL = $linkURL;
        $this->linkTarget = $linkTarget;
        $this->linkStyle = $linkStyle;
        $this->linkSize = $linkSize;
        $this->linkStyleDefaultIcon = $linkStyleDefaultIcon;
        $this->linkAriaLabel = $linkAriaLabel;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.link');
    }
}