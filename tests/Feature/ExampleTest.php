<?php

test('the application responds', function () {
    $response = $this->get('/demo');

    $response->assertStatus(200);
});
