<?php

use App\Models\{User, Post, Comment};

/*
|--------------------------------------------------------------------------
| OWASP A03 — Injection (XSS and SQL Injection)
|--------------------------------------------------------------------------
| User content is rendered through CommonMark with html_input => 'strip'
| and allow_unsafe_links => false. All queries use Eloquent parameter
| binding, including LIKE searches.
*/

test('stored discussion markdown strips raw HTML and script tags (XSS defense)', function () {
    $post = Post::factory()->create([
        'body' => "Legitimate scholarly analysis. <script>alert('xss')</script> and "
            . "<img src=x onerror=alert(document.cookie)>. Further methodology is discussed here.",
    ]);

    $html = $post->formatted_body;

    expect($html)->not->toContain('<script>');
    expect($html)->not->toContain('onerror');
});

test('stored comment markdown strips raw HTML (XSS defense)', function () {
    $post = Post::factory()->create();
    $comment = Comment::factory()->create([
        'post_id' => $post->id,
        'body'    => "Interesting point <script>document.cookie</script> indeed, thank you.",
    ]);

    expect($comment->body_html)->not->toContain('<script>');
});

test('the search endpoint resists SQL injection (parameterised queries)', function () {
    Post::factory()->count(3)->create();
    $before = Post::count();

    $payloads = [
        "' OR '1'='1",
        "'; DROP TABLE posts; --",
        "x%' UNION SELECT password FROM users --",
    ];

    foreach ($payloads as $payload) {
        $this->get('/search?'.http_build_query(['q' => $payload]))->assertOk();
    }

    // The posts table is intact and untouched → the input was bound, not executed.
    expect(Post::count())->toBe($before);
});
