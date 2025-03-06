<?php

namespace Cristal\ApiWrapper;

class MultipartParam
{
    /**
     * MultipartParam constructor.
     *
     * @param array|string $content
     * @param string|null $mimeType
     * @param string|null $filename
     */
    public function __construct(protected $content, protected $mimeType = null, protected $filename = null)
    {
    }

    /**
     * @param $name
     * @param $delimiter
     * @return string
     */
    public function render(string $name, string $delimiter)
    {
        $content = !is_string($this->content) ? json_encode($this->content) : $this->content;

        return "--" . $delimiter . "\r\n"
            . 'Content-Disposition: form-data; name="' . $name . '"' . ($this->filename ? '; filename="' . $this->filename . '"' : '') . "\r\n"
            . ($this->mimeType ? 'Content-Type: ' . $this->mimeType . "\r\n" : '')
            . "\r\n"
            . $content . "\r\n";
    }
}
