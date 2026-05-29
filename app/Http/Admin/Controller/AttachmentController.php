<?php

namespace App\Http\Admin\Controller;

use App\Http\Admin\Request\UploadRequest;
use App\Http\Common\Controller\AbstractController;
use App\Http\Common\Result;
use App\Models\User;
use App\Service\AttachmentService;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\HeaderParameter;
use Dedoc\Scramble\Attributes\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

#[Group('附件管理', '附件列表、上传和删除')]
class AttachmentController extends AbstractController
{
    public function __construct(
        private readonly AttachmentService $attachmentService,
    ) {}

    #[Endpoint('attachmentList', '附件列表')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    #[Response(type: 'array{code: int, message: string, data: array{list: array<int, \App\Models\Attachment>, total: int}}')]
    public function list(Request $request): JsonResponse
    {
        return Result::success($this->attachmentService->page(
            array_merge($request->all(), ['current_user_id' => $this->user($request)->id]),
            (int) $request->input('page', 1),
            (int) $request->input('page_size', 10),
        ));
    }

    #[Endpoint('attachmentUpload', '附件上传')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    public function upload(UploadRequest $request): JsonResponse
    {
        return Result::success($this->attachmentService->upload($request->file('file'), $this->user($request)->id));
    }

    #[Endpoint('attachmentDelete', '附件删除')]
    #[HeaderParameter('Authorization', 'Bearer 访问令牌', required: true, type: 'string', example: 'Bearer {access_token}')]
    public function delete(int $id): JsonResponse
    {
        $this->attachmentService->deleteById($id);

        return Result::success();
    }

    private function user(Request $request): User
    {
        /** @var User $user */
        $user = $request->user('api');

        return $user;
    }
}
