<?php
/*
 * Plugin Name: AI-Driven Content & SEO Assistant
 * Description: A simple AI tool to optimize content and suggest keywords.
 * Version: 1.0.0
 * Author: Your Name
 */
require_once __DIR__ . '/ai/ai_engine.php';

// Add a button to the editor
add_action('admin_footer', 'add_ai_button');
function add_ai_button() {
    echo '<button id="ai-generate" style="margin: 10px; padding: 5px 10px; background: #0073aa; color: white; border: none;">Generate Content</button>';
    echo '<div id="ai-output" style="margin: 10px; padding: 10px; border: 1px solid #ddd;"></div>';
}

// Add script to handle button click
add_action('admin_enqueue_scripts', 'enqueue_ai_script');
function enqueue_ai_script() {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("ai-generate").addEventListener("click", function() {
                var content = document.getElementById("content") ? document.getElementById("content").value : "";
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "' . admin_url('admin-ajax.php') . '", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        document.getElementById("ai-output").innerHTML = xhr.responseText;
                    }
                };
                xhr.send("action=run_ai&content=" + encodeURIComponent(content));
            });
        });
    </script>';
}

// Process AI request
add_action('wp_ajax_run_ai', 'run_ai_function');
function run_ai_function() {
    $content = isset($_POST['content']) ? $_POST['content'] : '';
    $analysis = analyze_content($content);
    $keywords = suggest_keywords($content);
    $generated = generate_content(array_key_first($keywords));
    $output = "Word Count: " . $analysis['word_count'] . "<br>";
    $output .= "Readability: " . $analysis['readability'] . "/100<br>";
    $output .= "Top Keywords: " . implode(', ', array_keys($keywords)) . "<br>";
    $output .= "Generated Content: " . $generated;
    echo $output;
    wp_die();
}
