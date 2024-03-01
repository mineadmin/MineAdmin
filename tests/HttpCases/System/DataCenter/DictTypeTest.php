<?php

use Hyperf\Collection\Arr;
use Hyperf\Stringable\Str;
beforeEach(function (){
    $this->prefix = '/system/dictType';
});

test('dict type controller test',function (){
    $this->actionTest([
        $this->buildTest('getNoParamsTest') => 'index',
        $this->buildTest('getNoParamsTest') => 'recycle',
    ]);
    $this->remoteTest();

    $successParam = [
        'name' => Str::random(5),
        'code' => Str::random(6)
    ];
    $failParams = [
        Arr::only($successParam, 'name'),
        Arr::only($successParam, 'code'),
    ];
    $updateSuccessParam = [
        'name' => Str::random(5),
        'code' => Str::random(6)
    ];
    $updateFailParams = [
        Arr::only($updateSuccessParam, 'name'),
        Arr::only($updateSuccessParam, 'code'),
    ];
    $id = $this->saveAndUpdate($successParam, $failParams, $updateSuccessParam, $updateFailParams);
    $this->actionTest([
        $this->buildTest('getNoParamsTest') => 'read/' . $id,
    ]);
    $this->changeStatusTest($id);
    $this->recoveryAndDeleteTest([$id]);
});