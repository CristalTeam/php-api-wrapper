<?php

namespace Cristal\ApiWrapper\Bridges\BarryvdhLaravelDebugbar;

use DebugBar\DataCollector\MessagesCollector;

class ApiCollector extends MessagesCollector
{
    /**
     * @param string $name
     */
    public function __construct($name = 'api')
    {
        parent::__construct($name);
    }

    /**
     * @return array
     */
    public function getWidgets()
    {
        $name = $this->getName();

        return [
            $name => [
                'icon' => 'star icon',
                'widget' => 'PhpDebugBar.Widgets.MessagesWidget',
                'map' => "$name.messages",
                'default' => '[]',
                'title' => 'API',
            ],
            "$name:badge" => [
                'map' => "$name.count",
                'default' => 'null',
            ],
        ];
    }
}
