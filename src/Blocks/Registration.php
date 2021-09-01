<?php
/**
 * Registration.php
 *
 * @package   novelist
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace Novelist\Blocks;

class Registration
{
    /**
     * Blocks to register
     *
     * @var string[]
     */
    private $blocks = [
        Book::class,
    ];

    public function registerCategory(array $categories, \WP_Post $post): array
    {
        $categories[] = [
            'slug'  => 'novelist',
            'title' => __('Novelist', 'novelist'),
            'icon'  => 'book',
        ];

        return $categories;
    }

    public function registerBlocks(): void
    {
        if (! function_exists('register_block_type')) {
            return;
        }

        $this->registerBlockJs();

        foreach ($this->blocks as $block) {
            if (! is_subclass_of($block, Block::class)) {
                throw new \InvalidArgumentException(sprintf(
                    'The %s class must implement the %s interface.',
                    $block,
                    Block::class
                ));
            }

            /** @var Block $block */
            $block = new $block();
            $block->register();
        }
    }

    private function registerBlockJs(): void
    {
        wp_register_script(
            'novelist-blocks',
            NOVELIST_PLUGIN_URL.'assets/build/blocks.js',
            ['wp-blocks', 'wp-i18n'],
            NOVELIST_VERSION
        );
    }

}
