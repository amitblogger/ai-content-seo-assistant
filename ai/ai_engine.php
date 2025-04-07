<?php
function analyze_content($content) {
    $words = preg_split('/\s+/', strtolower(trim($content)));
    $word_count = count($words);
    $keywords = json_decode(file_get_contents(__DIR__ . '/keywords.json'), true);
    $keyword_frequency = [];
    foreach ($words as $word) {
        if (isset($keywords[$word])) {
            $keyword_frequency[$word] = isset($keyword_frequency[$word]) ? $keyword_frequency[$word] + 1 : 1;
        }
    }
    $sentences = preg_split('/[.!?]+/', $content, -1, PREG_SPLIT_NO_EMPTY);
    $sentence_count = count($sentences);
    $avg_words_per_sentence = $word_count / max(1, $sentence_count);
    $readability = max(0, 100 - ($avg_words_per_sentence * 2));
    return [
        'keywords' => $keyword_frequency,
        'word_count' => $word_count,
        'readability' => round($readability)
    ];
}

function suggest_keywords($content) {
    $analysis = analyze_content($content);
    $content_keywords = $analysis['keywords'];
    $all_keywords = json_decode(file_get_contents(__DIR__ . '/keywords.json'), true);
    $suggestions = [];
    foreach ($all_keywords as $keyword => $base_score) {
        $frequency = isset($content_keywords[$keyword]) ? $content_keywords[$keyword] : 0;
        $score = $base_score + ($frequency * 10);
        $suggestions[$keyword] = min(100, $score);
    }
    arsort($suggestions);
    return array_slice($suggestions, 0, 5, true);
}

function generate_content($keyword, $word_count = 100) {
    $templates = [
        "$keyword is essential for improving your online presence.",
        "Learn how $keyword can drive more traffic to your site.",
        "Using $keyword effectively boosts your business growth.",
        "The key to success lies in mastering $keyword.",
        "Start optimizing with $keyword today for better results."
    ];
    $output = '';
    $current_words = 0;
    while ($current_words < $word_count) {
        $sentence = $templates[array_rand($templates)];
        $output .= $sentence . ' ';
        $current_words = str_word_count($output);
    }
    $words = explode(' ', trim($output));
    $output = implode(' ', array_slice($words, 0, $word_count));
    return $output . '.';
}
