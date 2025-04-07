<?php
/*
 * Plugin Name: AI-Driven Content & SEO Assistant
 * Description: A simple AI tool to optimize content and suggest keywords.
 * Version: 1.0.1
 * Author: Your Name
 */
require_once __DIR__ . '/ai/ai_engine.php';

// Add a button below the editor with debug notice
add_action('edit_form_after_editor', 'add_ai_button');
function add_ai_button() {
    echo '<div style="margin-top: 20px; padding: 10px; background: #f0f0f0; border: 1px solid #ccc;">';
    echo '<p style="color: green;">Plugin Loaded Successfully!</p>';
    echo '<button id="ai-generate" style="margin: 10px 0; padding: 5px 10px; background: #0073aa; color: white; border: none; cursor: pointer;">Generate Content</button>';
    echo '<div id="ai-output" style="margin-top: 10px; padding: 10px; border: 1px solid #ddd;"></div>';
    echo '</div>';
}

// Add script to handle button click
add_action('admin_enqueue_scripts', 'enqueue_ai_script');
function enqueue_ai_script($hook) {
    // Only load on post edit screens
    if ($hook !== 'post.php' && $hook !== 'post-new.php') {
        return;
    }
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            var button = document.getElementById("ai-generate");
            if (button) {
                console.log("Button found, attaching click event.");
                button.addEventListener("click", function() {
                    console.log("Button clicked!");
                    var content = document.getElementById("content") ? document.getElementById("content").value : "";
                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "' . admin_url('admin-ajax.php') . '", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            document.getElementById("ai-output").innerHTML = xhr.responseText;
                            console.log("AJAX response received.");
                        } else {
                            console.log("AJAX error: " + xhr.status);
                        }
                    };
                    xhr.send("action=run_ai&content=" + encodeURIComponent(content));
                });
            } else {
                console.log("Button not found!");
            }
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
