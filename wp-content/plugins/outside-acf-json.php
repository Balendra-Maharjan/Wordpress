<?php
/**
 * Plugin Name: Outside ACF JSON Module
 * Plugin URI: https://outside.tech
 * Description: Outside ACF Block/Module development - Place block templates in /views/blocks and preview images in /images/preview
 * Version: 1.0
 * Author: Outside
 * Author URI: https://outside.tech
 */

namespace App;

use function Roots\bundle;
use function Roots\asset;

if (!class_exists('OutsideAcfBlocks')) {

    /**
     * Handles registration of ACF JSON-based blocks
     */
    class OutsideAcfBlocks
    {
        public function __construct()
        {
            add_action('init', [$this, 'register_blocks']);
        }

        /**
         * Register ACF Blocks via block.json files
         */
        public function register_blocks(): void
        {
            $blocksDir = get_theme_file_path('resources/views/blocks');
            $blockJsonFiles = glob("{$blocksDir}/**/block.json");

            if (!$blockJsonFiles) return;

            foreach ($blockJsonFiles as $blockJsonPath) {
                $blockData = json_decode(file_get_contents($blockJsonPath), true);

                if (!is_array($blockData) || empty($blockData['name'])) {
                    continue;
                }

                [, $slug] = explode('/', $blockData['name']);

                acf_register_block_type([
                    'name'            => $slug,
                    'title'           => $blockData['title'] ?? ucfirst($slug),
                    'description'     => $blockData['description'] ?? '',
                    'category'        => $blockData['category'] ?? 'common',
                    'icon'            => $blockData['icon'] ?? '',
                    'keywords'        => $blockData['keywords'] ?? [],
                    'supports'        => $blockData['supports'] ?? [],
                    'style'           => $blockData['style'] ?? null,
                    'editor_style'    => $blockData['editorStyle'] ?? null,
                    'script'          => $blockData['script'] ?? null,
                    'mode'            => $blockData['acf']['mode'] ?? 'auto',
                    'enqueue_assets'  => function ($block) use ($slug, $blockData) {
                        $this->maybe_enqueue_assets($blockData);
                    },
                    'render_callback' => function ($block, $content = '', $is_preview = false, $post_id = 0) use ($slug) {
                        $context = [
                            'block'      => $block,
                            'content'    => $content,
                            'is_preview' => $is_preview,
                            'post_id'    => $post_id,
                            'fields'     => get_fields(),
                        ];

                        echo view("blocks/{$slug}/{$slug}", ['block' => $context]);
                    },
                ]);
            }
        }

        /**
         * Enqueue scripts and styles for blocks
         */
        protected function maybe_enqueue_assets(array $blockData): void
        {
            if (!class_exists('Vite')) {
                // Fallback or silent fail if Vite is not present
                return;
            }

            if (!empty($blockData['style'])) {
                $styles = (array) $blockData['style'];
                foreach ($styles as $style) {
                    $cssPath = \Vite::asset("resources/styles/modules/{$style}.scss");
                    wp_enqueue_style($style, $cssPath, [], null);
                }
            }

            if (!empty($blockData['script'])) {
                $scripts = (array) $blockData['script'];
                foreach ($scripts as $script) {
                    $jsPath = \Vite::asset("resources/js/modules/{$script}.js");
                    wp_enqueue_script_module($script, $jsPath, [], null, true);
                }
            }
        }
    }

    new OutsideAcfBlocks();
}
