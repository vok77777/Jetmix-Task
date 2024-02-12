<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReferenceCreateRequest;
use App\Models\Attachments;
use App\Models\References;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;

/**
 * Class ReferencesController
 * @package App\Http\Controllers
 *
 * @OA\Tag(
 *     name="References",
 *     description="Методы работы с обращениями"
 * )
 */
class ReferencesController extends Controller
{
    /**
     * Получение списка обращений
     *
     * @OA\Get(
     *     path="/api/references",
     *     operationId="references",
     *     tags={"References"},
     *     summary="Получение обращений",
     *     description="Получение списка всех обращений",
     *     @OA\Parameter(
     *         description="Число элементов на странице",
     *         in="query",
     *         name="limit",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="Номер страницы",
     *         in="query",
     *         name="page",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Successful", @OA\JsonContent()),
     *     @OA\Response(response=404, description="Обращения не найдены", @OA\JsonContent()),
     *     @OA\Response(response=500, description="Query server error", @OA\JsonContent())
     * )
     *
     * @param Request $request
     * @return mixed
     */
    public function getReferencesList(Request $request): mixed
    {
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);

        $references = References::paginate($limit, ['*'], 'page', $page);

        if(!$references) {
            return Response::error('Обращения не были найдены');
        }

        $result = [
            'current_page' => $references->currentPage(),
            'pages_count' => $references->lastPage(),
            'elements_count' => $references->total(),
            'references' => $references->items(),
        ];

        return Response::success(data: $result);
    }

    /**
     * Получить обращение по ID
     *
     * @OA\Get(
     *     path="/api/references/{id}",
     *     operationId="references_detail_by_id",
     *     tags={"References"},
     *     summary="Получение обращения по ID",
     *     description="",
     *     @OA\Parameter(
     *         description="ID обращения",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Successful", @OA\JsonContent()),
     *     @OA\Response(response=404, description="Обращение не найдено", @OA\JsonContent()),
     *     @OA\Response(response=500, description="Query server error", @OA\JsonContent())
     * )
     * @param int $id
     * @return mixed
     */
    public function getReferenceById(int $id): mixed
    {
        $reference = References::find($id);

        if($reference) {
            return Response::success(data: $reference);
        }

        return Response::error('Обращение не найдено');
    }

    /**
     * Создание обращения
     *
     * @OA\Post(
     *     path="/api/references/create",
     *     operationId="references_create",
     *     tags={"References"},
     *     summary="Создание обращения",
     *     description="",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             encoding={
     *                 "attachments[]": {"explode": true},
     *             },
     *             @OA\Schema(
     *                 required={"topic", "message"},
     *                 @OA\Property(property="topic", type="string"),
     *                 @OA\Property(property="message", type="string"),
     *                 @OA\Property(
     *                     property="attachments[]",
     *                     type="array",
     *                     @OA\Items(type="file", format="binary")
     *                 ),
     *             ),
     *         ),
     *     ),
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Successful", @OA\JsonContent()),
     *     @OA\Response(response=400, description="Validate error", @OA\JsonContent()),
     *     @OA\Response(response=500, description="Query server error", @OA\JsonContent())
     * )
     *
     * @param ReferenceCreateRequest $request
     * @return mixed
     */
    public function createReference(ReferenceCreateRequest $request): mixed
    {
        $request->validated();
        $user = $request->user();

        if(!$user) {
            return Response::error('Пользователь не найден');
        }

        $attachments = $request->file('attachments');
        $data = [
            'topic' => $request->post('topic'),
            'message' => $request->post('message'),
            'user_id' => $user->id
        ];

        DB::beginTransaction();

        try {
//            $attachPaths = [];
//            foreach ($attachments as $attachment) {
//                $filename = $attachment->getClientOriginalName();
//                $attachment->storeAs('references', $filename, 'public');
//
//                $attach = Attachments::create([
//                    'file_name' => $filename,
//                    'extension' => $attachment->getClientOriginalExtension(),
//                    'path' => 'attachments/' . $filename,
//                ]);
//
//                $attachPaths[] = $attach->path;
//                if($attach) {
//                    $data['attachments'][] = $attach->id;
//                }
//            }
//
//            References::create($data);

            $params = [
                'name' => $user->name,
                'email' => $user->email,
                'topic' => $request->post('topic'),
                'message' => $request->post('message'),
//                'attachments' => $attachPaths
            ];

            $managerRole = Roles::where('slug', 'manager')->first();
            $managers = User::whereHas('roles', function($query) use ($managerRole) {
                $query->where('role_id', $managerRole->id);
            })->get();

            dd($managers);

//            Mail::to($)

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::error($e->getMessage());
        }

        return Response::success('Обращение создано');
    }
}
