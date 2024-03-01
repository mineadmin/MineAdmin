<?php
beforeEach(function (){
    $this->prefix = '/setting/code';
});

test('generator code test',function (){
    $this->actionTest([
        $this->buildTest('getNoParamsTest') => 'index',
        $this->buildTest('getNoParamsTest') => 'getDataSourceList',
        $this->buildTest('getNoParamsTest') => 'getTableColumns',
        $this->buildTest('getNoParamsTest') => 'preview',
        $this->buildTest('getNoParamsTest') => 'readTable',
    ]);
});

