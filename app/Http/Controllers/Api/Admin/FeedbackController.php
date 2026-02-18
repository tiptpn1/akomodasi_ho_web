<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FeedbackStoreRequest;
use App\Http\Requests\FeedbackUpdateRequest;
use App\Http\Resources\FeedbackResource;
use App\Models\Feedback;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     *
     * @return \App\Http\Resources\FeedbackResource
     */
    public function show(int $id)
    {
        $feedback = Feedback::where('sendvicon_id', $id)->first();

        if (!$feedback) {
            throw new HttpResponseException(response([
                "errors" => [
                    'message' => [
                        'Feedback not found'
                    ]
                ]
            ]));
        }

        return new FeedbackResource($feedback);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(FeedbackStoreRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();
        $feedback = new Feedback($data);
        $feedback->sendvicon_id = $id;
        $feedback->save();

        return (new FeedbackResource($feedback))->response()->setStatusCode(201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\FeedbackUpdateRequest $request
     * @param int $idVicon
     * @param int $idFeedback
     * @return \App\Http\Resources\FeedbackResource
     */
    public function update(FeedbackUpdateRequest $request, int $idVicon, int $idFeedback): FeedbackResource
    {

        $data = $request->validated();
        $feedback = Feedback::where('sendvicon_id', $idVicon)->where('id', $idFeedback)->first();

        if (!$feedback) {
            throw new HttpResponseException(response([
                'status' => 'error',
                'errors' => [
                    'message' => [
                        'Feedback not found'
                    ]
                ]
            ], 400));
        }

        if (isset($data['petugas_ti'])) {
            $feedback->petugas_ti = $data['petugas_ti'];
        }

        if (isset($data['laptop_vicon'])) {
            $feedback->laptop_vicon = $data['laptop_vicon'];
        }

        if (isset($data['kendala_ti'])) {
            $feedback->kendala_ti = $data['kendala_ti'];
        }

        if (isset($data['kendala_umum'])) {
            $feedback->kendala_umum = $data['kendala_umum'];
        }

        if (isset($data['kendala_ex'])) {
            $feedback->kendala_ex = $data['kendala_ex'];
        }

        $feedback->save();

        return new FeedbackResource($feedback);
    }
}
