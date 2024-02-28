<?php
beforeEach(function (){
    $this->prefix = '/system/common';
});

test('getUserList test',function (){
    testSuccessResponse($this->get($this->prefix.'/getUserList'));
});

test('getUserInfoByIds test',function (){
    testSuccessResponse($this->post($this->prefix.'/getUserInfoByIds',['ids'=>[1,2,3]]));
});

test('getDeptTreeList test',function (){
    testSuccessResponse($this->get($this->prefix.'/getDeptTreeList'));
});

test('getRoleList test',function (){
    testSuccessResponse($this->get($this->prefix.'/getRoleList'));
});

test('getPostList test',function (){
    testSuccessResponse($this->get($this->prefix.'/getPostList'));
});

test('getOperationLogList test',function (){
    testSuccessResponse($this->get($this->prefix.'/getOperationLogList'));
});

test('getLoginLogList test',function (){
    testSuccessResponse($this->get($this->prefix.'/getLoginLogList'));
});

test('getResourceList test',function (){
    testSuccessResponse($this->get($this->prefix.'/getResourceList'));
});

test('getNoticeList test',function (){
    testSuccessResponse($this->get($this->prefix.'/getNoticeList'));
});

test('clearAllCache test',function (){
    testSuccessResponse($this->get($this->prefix.'/clearAllCache'));
});
