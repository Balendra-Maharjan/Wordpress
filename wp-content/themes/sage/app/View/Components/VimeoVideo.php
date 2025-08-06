<?php
namespace App\View\Components;

use Illuminate\View\Component;

class VimeoVideo extends Component
{
    public $videoUrl;
    public $autoplay;
    public $loop;
    public $title;
    public $byline;
    public $portrait;
    public $aspectRatio;
    public $customControls;
    public $videoId;
    public $muted;

    public function __construct($videoUrl, $autoplay = false, $muted = false, $loop = false, $title = true, $byline = false, $customControls = false, $portrait = false, $aspectRatio="16x9")
    {
        /* TODO:: Check $title == 'true' ? 1 : 0 these condition work properly or not  */
        $this->videoUrl = $videoUrl;
        $this->autoplay = $autoplay == 'true' ? true : false;
        $this->muted = $muted == 'true' ? true : false;
        $this->loop = $loop == 'true' ? 1 : 0;
        $this->title = $title == 'true' ? 1 : 0;
        $this->byline = $byline == 'true' ? 1 : 0;
        $this->portrait = $portrait == 'true' ? 1 : 0;
        $this->customControls = $customControls == 'true' ? true : false;
        $this->aspectRatio = $aspectRatio;

        // Extract video ID from URL for JavaScript targeting
        preg_match('/video\/(\d+)/', $videoUrl, $matches);
        $this->videoId = $matches[1] ?? 'unknown';
    }

    public function render()
    {
        return view('components.vimeo-video');
    }
}
