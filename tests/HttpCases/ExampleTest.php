<?php
test('index testing',function (){
    expect(true)->toBeTrue()
        ->and($this->get('/'))
        ->toBeNull();
});