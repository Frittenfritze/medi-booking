<?php

namespace Medi\View\Frontend;

use Medi\View\Frontend\MediFrontendShortCode;

class MediFrontendBlock
{
    private MediFrontendShortCode $frontend_short_code;

    public function __construct()
    {
        $this->frontend_short_code = new MediFrontendShortCode();
    }

    /**
     * Renders the booking block using the provided attributes and content.
     *
     * @param array $attributes The attributes passed to the booking block.
     * @param string $content The inner content of the booking block.
     * @return string The rendered output of the booking block.
     */
    function medi_booking_block_render(array $attributes, string $content) : string
    {
        return $this->frontend_short_code->medi_shortcode_render();
    }


}