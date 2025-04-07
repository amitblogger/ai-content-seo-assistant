<?php
/*
 * Plugin Name: AI-Driven Content & SEO Assistant
 * Description: A simple AI tool to optimize content and suggest keywords.
 * Version: 1.0.2
 * Author: Your Name
 */
require_once __DIR__ . '/ai/ai_engine.php';

// Add a notice with button at separa the top of admin pages
add_action('admin_notices', 'add_ai_button');
function add_ai_button() {
    echo '<div style="margin: 20px; padding: 15px; background: #e0f7fa; border: 1px solid #00acc1;">';
    echo '<p style="color: #00796b; font-weight: bold;">AI Plugin Test: If you see this, the plugin is working!</p>';
    echo '<button id="ai-generate" style="padding: 5px 10px; background: #00acc1; color: white; border: none; cursor: pointer;">Generate Content</button>';
    echo '<div id="ai-output" style="margin-top: 10px; padding: 10px; background: white; border: 1px solid #ddd;"></div>';
    echo '</div>';
}

// Add script to handle button click
add_action('admin_head', 'enqueue_ai_script');
function enqueue_ai_script() {
    echo '<script>
        console.log("AI Plugin: Script loaded.");
        document.addEventListener("DOMContentLoaded", function() {
            console.log("AI Plugin: DOM loaded.");
            var button = document.getElementById("ai-generate");
            if (button) {
                console.log("AI Plugin: Button found.");
                button.addEventListener("click", function() {
                    console.log("AI Plugin: Button clicked.");
                    var content = document.getElementById("content") ? document.getElementById("content").value : "";
                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "' . admin_url('admin-ajax.php') . '", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            document.getElementById("ai-output").innerHTML = xhr.responseText;
                            console.log("AI Plugin: AJAX response received.");
                        } else {
                            console.log("AI Plugin: AJAX error - Status: " + xhr.status);
                        }
                    };
                    xhr.send("action=run_ai&content=" + encodeURIComponent(content));
                });
            } else {
                console.log("AI Plugin: Button not found!");
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
