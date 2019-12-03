<?php

namespace Cpro\ApiWrapper;

class MultipartParam
{
    /**
     * @var array|string
     */
    protected $content;

    /**
     * @var string|null
     */
    protected $mimeType;

    /**
     * @var string|null
     */
    protected $filename;

    /**
     * MultipartParam constructor.
     *
     * @param array|string $content
     */
    public function __construct($content, $mimeType = null, $filename = null)
    {
        $this->content = $content;
        $this->mimeType = $mimeType;
        $this->filename = $filename;
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
