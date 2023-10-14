<?php
/**
 * WidgetServiceProvider.php
 *
 * @package   novelist
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace Novelist\ServiceProviders;

use Novelist\Widgets\BooksBySeriesWidget;
use Novelist\Widgets\BookWidget;
use Novelist\Widgets\WordCountWidget;

class WidgetServiceProvider implements ServiceProvider
{
    protected $widgets = [
        BookWidget::class,
        BooksBySeriesWidget::class,
        WordCountWidget::class,
    ];

    public function register(): void
    {

    }

    public function boot(): void
    {
        add_action('widgets_init', function () {
            foreach ($this->widgets as $widget) {
                register_widget($widget);
            }
        });
    }
}
