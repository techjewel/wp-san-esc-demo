<?php

defined('ABSPATH') or die;

/*
Plugin Name:  WP SanEsc Demo
Plugin URI:   https://github.com/WPManageNinja/fluent-security
Description:  Data Sanitization/Escaping Interactive Demo. Just use shortcode [fluent_san_esc_demo]
Version:      1.0
Author:       Fluent Security Team
Author URI:   https://jewel.im
License:      GPLv2 or later
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  wp-san-demo
Domain Path:  /language/
*/

class DataSanEscDemo
{
    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        add_shortcode('fluent_san_esc_demo', array($this, 'showDemo'));
        add_action('wp_ajax_fluent_demo_parser', [$this, 'handleAjax']);
        add_action('wp_ajax_nopriv_fluent_demo_parser', [$this, 'handleAjax']);
    }

    public function handleAjax()
    {
        $func = sanitize_text_field($_REQUEST['function']);
        $content = $_REQUEST['content'];

        if($func == 'none' || !$func) {
            echo $content;
            die();
        }

        $funcs = $this->getFunctions();
        $allFuncs = $funcs['santizes'] + $funcs['escps'];

        if(!in_array($func, $allFuncs)) {
            echo 'No Function found';
            die();
        }

        echo call_user_func($func, $content);
        die();
    }

    public function showDemo()
    {
        wp_enqueue_script('fluent_demo', plugin_dir_url(__FILE__).'script.js', ['jquery'], '1.0', true);
        wp_localize_script('fluent_demo', 'fluent_demo', [
            'ajax_url' => admin_url('admin-ajax.php')
        ]);

        $funcs = $this->getFunctions();

        ob_start();
        ?>
        <style>
            .fluent_demo_wrapper {
                width: 100%;
                background: #efefef;
                border-radius: 5px;
                padding: 20px;
            }
            .fluent_demo_wrapper p {
                margin: 5px 0 0;
            }
            .fluent_demo_wrapper h3 {
                margin: 0;
                font-size: 22px;
                line-height: 30px;
                margin-bottom: 20px;
            }
            .form_item {
                display: block;
                margin-bottom: 10px;
            }
            .form_item label {
                display: block;
                width: 100%;
                font-weight: bold;
            }
            .form_item select {
                width: 100%;
                padding: 10px;
            }
            pre.response_body {
                background: #fff498;
                margin-top: 0px;
            }
            .response_html {
                padding: 10px;
                background: white;
            }
        </style>
        <div class="fluent_demo_wrapper">
            <h3>Data Sanitization/Escaping Interactive Demo</h3>
            <form id="fluent_demo" method="POST">
                <input type="hidden" name="action" value="fluent_demo_parser">
                <div class="form_item">
                    <label>Function to Test</label>
                    <select id="demo_func" name="function">
                        <option value="none">None</option>
                        <optgroup label="INPUT">
                            <?php foreach ($funcs['santizes'] as $function): ?>
                                <option value="<?php echo $function; ?>"><?php echo $function; ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                        <optgroup label="OUTPUT">
                            <?php foreach ($funcs['escps'] as $function): ?>
                                <option value="<?php echo $function; ?>"><?php echo $function; ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                    </select>
                    <p style="display: none;"  id="doc_link" ><a target="_blank" href="#">Doc</a></p>
                </div>
                <div class="form_item">
                    <label>Your Input</label>
                    <textarea name="content"></textarea>
                </div>
                <div class="form_item">
                    <button id="test_run">Run Test</button>
                </div>

                <div style="display: none;" class="data_responses">
                    <hr />

                    <p>Raw Response</p>
                    <pre class="response_body"></pre>

                    <p>HTML Response</p>
                    <div class="response_html"></div>
                </div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    public function getFunctions() {
        $functions = [
            'sanitize_email',
            'sanitize_file_name',
            'sanitize_html_class',
            'sanitize_key',
            'sanitize_meta',
            'sanitize_mime_type',
            'sanitize_option',
            'sanitize_sql_orderby',
            'sanitize_text_field',
            'sanitize_title',
            'sanitize_title_for_query',
            'sanitize_title_with_dashes',
            'sanitize_user',
            'esc_url_raw',
            'wp_filter_post_kses',
            'wp_filter_nohtml_kses',
            'wp_kses_post'
        ];
        $escs = [
            'esc_html',
            'esc_attr',
            'esc_url',
            'esc_sql',
            'esc_textarea',
            'esc_url_raw',
            'esc_js'
        ];

        return [
            'santizes' => $functions,
            'escps' => $escs
        ];
    }
}


add_action('plugin_loaded', function () {
   new DataSanEscDemo();
});
